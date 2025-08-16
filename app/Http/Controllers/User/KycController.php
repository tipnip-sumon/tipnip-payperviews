<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\KycVerification;
use App\Services\KycImageService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf; // Correct import for PDF generation
use Intervention\Image\ImageManagerStatic as Image;

class KycController extends Controller
{
    /**
     * Display KYC status page
     */
    public function index()
    {
        $user = User::find(Auth::id());
        $kycVerification = KycVerification::where('user_id', $user->id)->latest()->first();
        
        $data = [
            'pageTitle' => 'KYC Verification',
            'kycVerification' => $kycVerification
        ];
        
        return view('frontend.kyc.index', $data);
    }

    /**
     * Show KYC form
     */
    public function create()
    {
        $user = User::find(Auth::id());
        // Check if user already has pending or approved KYC
        $existingKyc = KycVerification::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();
            
        if ($existingKyc) {
            return redirect()->route('user.kyc.index')
                ->with('error', 'You already have a KYC verification in progress or approved.');
        }
        
        $data = [
            'pageTitle' => 'KYC Verification Form'
        ];
        
        return view('frontend.kyc.create', $data);
    }

    /**
     * Store KYC application
     */
    public function store(Request $request)
    {
        // dd($request->all());
        try {
            
            Log::info('KYC submission started', ['user_id' => Auth::id()]);
            Log::info('KYC request data', ['data' => $request->all()]); // Log all request data
            
            $user = Auth::user();
            
            // Country processing with error handling
            try {
                Log::info('Loading country data');
                $c = json_decode(file_get_contents(resource_path('views/country/country.json')), true); // Add true to get array instead of stdClass
                Log::info('Country data loaded successfully', ['country_count' => count($c)]);
                
                $countries = [];
                foreach ($c as $k => $country) {
                    $countries[] = ['country'=> $country['country'],'dial_code'=> $country['dial_code'],'country_code'=> $k];
                    if($country['country'] == $request->country) {
                        $request->merge(['dial_code' => $country['dial_code']]);
                        Log::info('Found matching country', ['country' => $request->country, 'dial_code' => $country['dial_code']]);
                    }
                }
                Log::info('Country processing completed');
            } catch (\Exception $e) {
                Log::error('Country processing failed', ['error' => $e->getMessage()]);
                // Set a default dial code if country processing fails
                $request->merge(['dial_code' => '+880']); // Default to Bangladesh
            }
            // Check if user already has a pending or approved KYC
            Log::info('Checking user KYC status', ['user_id' => $user->id, 'kv_status' => $user->kv]);
            if ($user->kv == 2 || $user->kv == 1) {
                Log::warning('User already has KYC submission, redirecting', ['user_id' => $user->id, 'kv_status' => $user->kv]);
                return redirect()->route('user.kyc.index')
                    ->with('error', 'You already have a KYC verification request.');
            }

            Log::info('Starting validation for KYC submission');
            
            // Validate step 1
            try {
                $request->validate(KycVerification::getValidationRules('step1'));
                Log::info('Step 1 validation passed');
            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::error('Step 1 validation failed', ['errors' => $e->errors()]);
                throw $e;
            }
            
            // Validate step 2
            try {
                $request->validate(KycVerification::getValidationRules('step2'));
                Log::info('Step 2 validation passed');
            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::error('Step 2 validation failed', ['errors' => $e->errors()]);
                throw $e;
            }
            
            // Validate step 3
            try {
                $request->validate(KycVerification::getValidationRules('step3'));
                Log::info('Step 3 validation passed');
            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::error('Step 3 validation failed', ['errors' => $e->errors()]);
                throw $e;
            }

            // Process mobile number: remove leading 0 and combine with dial code
            $phoneNumber = $request->phone_number;
            $dialCode = $request->dial_code ?? '';
            // Remove leading zero from phone number if exists
            if ($phoneNumber && str_starts_with($phoneNumber, '0')) {
                $phoneNumber = substr($phoneNumber, 1);
            }
            // Combine dial code with processed phone number
            $fullMobileNumber = $dialCode . $phoneNumber;

            // Check if phone number is already used by another user
            $existingPhone = KycVerification::where('phone_number', $fullMobileNumber)
                ->where('user_id', '!=', $user->id)
                ->first();
            if ($existingPhone) {
                Log::warning('Phone number already in use', [
                    'phone' => $fullMobileNumber,
                    'existing_user' => $existingPhone->user_id,
                    'current_user' => $user->id
                ]);
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['phone_number' => 'This phone number is already registered by another user.']);
            }

            // Check if document number is already used by another user
            $existingDoc = KycVerification::where('document_number', $request->document_number)
                ->where('user_id', '!=', $user->id)
                ->first();
            if ($existingDoc) {
                Log::warning('Document number already in use', [
                    'document_number' => $request->document_number,
                    'existing_user' => $existingDoc->user_id,
                    'current_user' => $user->id
                ]);
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['document_number' => 'This document number is already registered by another user.']);
            }

            // Create directory for user KYC files
            $userKycPath = 'kyc_documents/' . $user->id;

            // Store uploaded files with proper naming
            Log::info('About to process document_front', ['file_size' => $request->file('document_front')->getSize()]);
            $documentFront = $this->storeKycFile($request->file('document_front'), $userKycPath, 'front');
            
            $documentBack = null;
            if ($request->hasFile('document_back')) {
                Log::info('About to process document_back', ['file_size' => $request->file('document_back')->getSize()]);
                $documentBack = $this->storeKycFile($request->file('document_back'), $userKycPath, 'back');
            }
            
            Log::info('About to process selfie_image', ['file_size' => $request->file('selfie_image')->getSize()]);
            $selfieImage = $this->storeKycFile($request->file('selfie_image'), $userKycPath, 'selfie');
            
            // Create KYC verification record
            Log::info('Creating KYC record with data', [
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'files_processed' => ['front' => $documentFront, 'back' => $documentBack, 'selfie' => $selfieImage]
            ]);
            
            $kycRecord = KycVerification::create([
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'date_of_birth' => $request->date_of_birth,
                'document_type' => $request->document_type,
                'document_number' => $request->document_number,
                'document_front' => $documentFront,
                'document_back' => $documentBack,
                'selfie_image' => $selfieImage,
                'nationality' => $request->nationality,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
                'phone_number' => $fullMobileNumber,
                'submitted_at' => now()
            ]);
            
            Log::info('KYC record created successfully', ['kyc_id' => $kycRecord->id]);
            // Update user's KYC status
            $user = User::find(Auth::id());
            $user->firstname = $request->first_name;
            $user->lastname = $request->last_name;
            $user->kv = 2; // 2 = KYC pending
            $user->save();
            return redirect()->route('user.kyc.index')
                ->with('success', 'KYC verification submitted successfully! We will review your application within 24-48 hours.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors - let Laravel handle them naturally
            Log::error('KYC validation error', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Illuminate\Database\QueryException $e) {
            // Database constraint errors (like unique violations)
            Log::error('KYC database error: ' . $e->getMessage(), ['user_id' => Auth::id()]);
            
            // Check for specific constraint violations
            if (str_contains($e->getMessage(), 'kyc_phone_number_unique')) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['phone_number' => 'This phone number is already registered by another user.']);
            }
            if (str_contains($e->getMessage(), 'kyc_document_number_unique')) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['document_number' => 'This document number is already registered by another user.']);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'A database error occurred. Please check your information and try again.');
        } catch (\Exception $e) {
            Log::error('KYC submission error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'error_trace' => $e->getTraceAsString()
            ]);
            
            // Check for specific error types
            if (str_contains($e->getMessage(), 'GD extension')) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Image processing is currently unavailable. Please try again later or contact support.');
            }
            if (str_contains($e->getMessage(), 'file upload')) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'File upload failed. Please check your files and try again.');
            }
            if (str_contains($e->getMessage(), 'storage')) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'File storage error. Please try again or contact support.');
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An unexpected error occurred while submitting your KYC verification. Please try again or contact support if the problem persists.');
        }
    }

    /**
     * Store KYC file with automatic image optimization
     */
    private function storeKycFile($file, $path, $type)
    {
        try {
            Log::info('Starting optimized image storage', ['type' => $type, 'path' => $path]);
            
            $extension = strtolower($file->getClientOriginalExtension());
            $filename = $type . '_' . time() . '_' . uniqid() . '.jpg'; // Always save as JPG for optimization
            $fullPath = storage_path('app/public/' . $path . '/' . $filename);
            
            // Create directory if it doesn't exist
            $directory = dirname($fullPath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Get the uploaded file path
            $tempPath = $file->getPathname();
            
            // Optimize and compress the image
            $this->optimizeImage($tempPath, $fullPath, $type);
            
            $storedPath = $path . '/' . $filename;
            Log::info('Optimized storage successful', ['path' => $storedPath, 'type' => $type]);
            return $storedPath;
            
        } catch (\Exception $e) {
            Log::error('Error in storeKycFile', [
                'error' => $e->getMessage(), 
                'type' => $type, 
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            throw $e;
        }
    }

    /**
     * Optimize image using Intervention Image v2 - compress and resize
     */
    private function optimizeImage($sourcePath, $destinationPath, $type)
    {
        $originalSize = filesize($sourcePath);
        
        Log::info('Starting image optimization with Intervention Image v2', [
            'type' => $type,
            'original_size' => round($originalSize / 1024, 2) . ' KB'
        ]);
        
        try {
            // Create image instance with Intervention Image v2
            $image = Image::make($sourcePath);
            
            // Get original dimensions
            $originalWidth = $image->width();
            $originalHeight = $image->height();
            
            Log::info('Image dimensions', [
                'original' => $originalWidth . 'x' . $originalHeight,
                'type' => $type
            ]);
            
            // Set different optimization settings based on image type
            if ($type === 'selfie') {
                // Aggressive compression for selfies - target 30-50KB
                $maxSize = 600;  // Smaller dimensions for selfies
                $quality = 50;   // Lower quality for selfies
                
                Log::info('SELFIE OPTIMIZATION PATH SELECTED', [
                    'type' => $type,
                    'max_size' => $maxSize,
                    'quality' => $quality,
                    'target' => '30-50KB'
                ]);
                
            } else {
                // Higher quality for documents (front/back) - maintain readability
                $maxSize = 1400;  // Larger dimensions for documents
                $quality = 85;    // Higher quality for documents
                
                Log::info('DOCUMENT OPTIMIZATION PATH SELECTED', [
                    'type' => $type,
                    'max_size' => $maxSize,
                    'quality' => $quality,
                    'target' => 'High readability'
                ]);
                
                // Adjust quality based on original file size for documents
                if ($originalSize > 1000 * 1024) {
                    $quality = 75; // Slightly lower for very large files
                    Log::info('Quality adjusted for large file', ['new_quality' => $quality]);
                } elseif ($originalSize > 2000 * 1024) {
                    $quality = 70; // More compression for extremely large files
                    Log::info('Quality adjusted for very large file', ['new_quality' => $quality]);
                }
            }
            
            // Resize image maintaining aspect ratio if needed
            if ($originalWidth > $maxSize || $originalHeight > $maxSize) {
                $image->resize($maxSize, $maxSize, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                
                Log::info('Image resized', [
                    'from' => $originalWidth . 'x' . $originalHeight,
                    'to' => $image->width() . 'x' . $image->height(),
                    'max_size' => $maxSize
                ]);
            }
            
            Log::info('Applying compression', [
                'type' => $type,
                'quality' => $quality,
                'final_dimensions' => $image->width() . 'x' . $image->height()
            ]);
            
            // Save as optimized JPEG with type-specific compression
            $image->save($destinationPath, $quality, 'jpg');
            
            $finalSize = filesize($destinationPath);
            $compressionRatio = round((1 - ($finalSize / $originalSize)) * 100, 2);
            
            Log::info('Image optimization completed successfully', [
                'type' => $type,
                'original_size' => round($originalSize / 1024, 2) . ' KB',
                'final_size' => round($finalSize / 1024, 2) . ' KB',
                'dimensions' => $image->width() . 'x' . $image->height(),
                'saved' => round(($originalSize - $finalSize) / 1024, 2) . ' KB',
                'compression_ratio' => $compressionRatio . '%',
                'quality' => $quality,
                'optimization_type' => $type === 'selfie' ? 'Aggressive (Small Size)' : 'Balanced (High Quality)'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Intervention Image optimization failed', [
                'error' => $e->getMessage(),
                'type' => $type,
                'source' => $sourcePath,
                'line' => $e->getLine()
            ]);
            
            // Fallback: Copy original file if optimization fails
            if (!copy($sourcePath, $destinationPath)) {
                throw new \Exception('Failed to copy original file as fallback');
            }
            
            Log::info('Used original file as fallback');
        }
    }

    /**
     * Generate KYC PDF report
     */
    public function generateKycPdf($id)
    {
        $kyc = KycVerification::with('user')->findOrFail($id);
        
        // Check if user has permission
        if (Auth::id() !== $kyc->user_id && !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access');
        }

        $data = [
            'kyc' => $kyc,
            'user' => $kyc->user,
            'generated_at' => now()
        ];

        $pdf = Pdf::loadView('frontend.kyc.pdf-report', $data);
        
        return $pdf->download('kyc_verification_' . $kyc->user->username . '.pdf');
    }

    /**
     * Show KYC status page
     */
    public function status() 
    {
        $user = Auth::user();
        $kycVerification = KycVerification::where('user_id', $user->id)->latest()->first();
        
        $data = [
            'pageTitle' => 'KYC Status',
            'kycVerification' => $kycVerification
        ];
        
        return view('frontend.kyc.status', $data);
    }

    /**
     * Download KYC document
     */
    public function downloadDocument($id, $type)
    {
        $kyc = KycVerification::findOrFail($id);
        
        // Check if user has permission (admin or own KYC)
        if (Auth::id() !== $kyc->user_id && !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access');
        }

        $filePath = null;
        $fileName = null;

        switch ($type) {
            case 'front':
                $filePath = $kyc->document_front;
                $fileName = 'document_front_' . $kyc->user->username;
                break;
            case 'back':
                $filePath = $kyc->document_back;
                $fileName = 'document_back_' . $kyc->user->username;
                break;
            case 'selfie':
                $filePath = $kyc->selfie_image;
                $fileName = 'selfie_' . $kyc->user->username;
                break;
        }

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File not found');
        }

        $absolutePath = Storage::disk('public')->path($filePath);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        
        return response()->download($absolutePath, $fileName . '.' . $extension);
    }

    /**
     * View KYC document inline (for PDF viewing)
     */
    public function viewDocument($id, $type)
    {
        $kyc = KycVerification::findOrFail($id);
        
        // Check if user has permission
        if (Auth::id() !== $kyc->user_id && !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access');
        }

        $filePath = null;
        switch ($type) {
            case 'front':
                $filePath = $kyc->document_front;
                break;
            case 'back':
                $filePath = $kyc->document_back;
                break;
            case 'selfie':
                $filePath = $kyc->selfie_image;
                break;
        }

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File not found');
        }

        $absolutePath = Storage::disk('public')->path($filePath);
        $mimeType = mime_content_type($absolutePath);
        
        return response()->file($absolutePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline'
        ]);
    }

    /**
     * Check if document number is unique
     */
    public function checkDocumentNumber(Request $request)
    {
        $documentNumber = $request->input('document_number');
        $userId = Auth::id();
        
        if (!$documentNumber) {
            return response()->json(['available' => false, 'message' => 'Document number is required']);
        }
        
        // Check if document number already exists for other users
        $exists = KycVerification::where('document_number', $documentNumber)
            ->where('user_id', '!=', $userId)
            ->exists();
        
        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'This document number is already registered by another user' : 'Document number is available'
        ]);
    }

    /**
     * Check if phone number is unique
     */
    public function checkPhoneNumber(Request $request)
    {
        $phoneNumber = $request->input('phone_number');
        $dialCode = $request->input('dial_code');
        $userId = Auth::id();
        
        if (!$phoneNumber) {
            return response()->json(['available' => false, 'message' => 'Phone number is required']);
        }
        
        // Process mobile number same way as in store method
        if (str_starts_with($phoneNumber, '0')) {
            $phoneNumber = substr($phoneNumber, 1);
        }
        $fullMobileNumber = $dialCode . $phoneNumber;
        
        // Check if phone number already exists for other users
        $existsInKyc = KycVerification::where('phone_number', $fullMobileNumber)
            ->where('user_id', '!=', $userId)
            ->exists();
            
        $existsInUsers = User::where('mobile', $fullMobileNumber)
            ->where('id', '!=', $userId)
            ->exists();
        
        $exists = $existsInKyc || $existsInUsers;
        
        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'This phone number is already registered by another user' : 'Phone number is available'
        ]);
    }

    /**
     * Validate uploaded image quality and format
     */
    public function validateImage(Request $request)
    {
        try {
            if (!$request->hasFile('image')) {
                return response()->json([
                    'valid' => false,
                    'message' => 'No image file provided'
                ]);
            }

            $file = $request->file('image');
            $originalSize = $file->getSize();
            
            // Basic validation
            if ($originalSize > 10 * 1024 * 1024) { // 10MB limit
                return response()->json([
                    'valid' => false,
                    'message' => 'File size too large (maximum 10MB)'
                ]);
            }

            // Check if it's a valid image
            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Invalid file format. Only JPEG, PNG, GIF, and WebP are allowed.'
                ]);
            }

            // Try to read image dimensions
            try {
                $imageInfo = getimagesize($file->getPathname());
                if (!$imageInfo) {
                    return response()->json([
                        'valid' => false,
                        'message' => 'Could not read image information'
                    ]);
                }

                $width = $imageInfo[0];
                $height = $imageInfo[1];
                
                // Minimum dimension check
                if ($width < 200 || $height < 200) {
                    return response()->json([
                        'valid' => false,
                        'message' => 'Image too small. Minimum size is 200x200 pixels.'
                    ]);
                }

                // Calculate compression preview based on image type
                if ($request->input('type') === 'selfie') {
                    $estimatedCompressed = round($originalSize * 0.15); // Aggressive 85% compression for selfies
                    $optimizationType = 'Selfie (Small Size)';
                } else {
                    $estimatedCompressed = round($originalSize * 0.4); // Moderate 60% compression for documents
                    $optimizationType = 'Document (High Quality)';
                }
                
                return response()->json([
                    'valid' => true,
                    'message' => 'Image is valid and ready for optimization',
                    'details' => [
                        'original_size' => round($originalSize / 1024, 2) . ' KB',
                        'dimensions' => $width . 'x' . $height,
                        'estimated_compressed' => round($estimatedCompressed / 1024, 2) . ' KB',
                        'estimated_savings' => round(($originalSize - $estimatedCompressed) / 1024, 2) . ' KB',
                        'optimization_type' => $optimizationType
                    ]
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Could not validate image: ' . $e->getMessage()
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Image validation error', [
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'valid' => false,
                'message' => 'Image validation failed'
            ]);
        }
    }
}
