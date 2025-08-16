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
            $documentFront = $this->storeKycFile($request->file('document_front'), $userKycPath, 'front');
            $documentBack = $request->hasFile('document_back') 
                ? $this->storeKycFile($request->file('document_back'), $userKycPath, 'back')
                : null;
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
     * Optimize image automatically - compress and resize
     */
    private function optimizeImage($sourcePath, $destinationPath, $type)
    {
        try {
            // Check if GD extension is available
            if (!function_exists('imagecreatefromjpeg') || !function_exists('imagecreatetruecolor')) {
                throw new \Exception('GD extension functions are not available');
            }
            
            // Set optimal dimensions based on document type
            $maxWidth = ($type === 'selfie') ? 800 : 1200;  // Selfies smaller, documents larger
            $maxHeight = ($type === 'selfie') ? 800 : 1600;
            $quality = 85; // Good balance between quality and file size
            
            // Get image info
            $imageInfo = \getimagesize($sourcePath);
            if (!$imageInfo) {
                throw new \Exception('Invalid image file');
            }
            
            $originalWidth = $imageInfo[0];
            $originalHeight = $imageInfo[1];
            $imageType = $imageInfo[2];
            
            // Calculate new dimensions while maintaining aspect ratio
            $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight, 1);
            $newWidth = (int)($originalWidth * $ratio);
            $newHeight = (int)($originalHeight * $ratio);
            
            // Create image resource from source
            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    $sourceImage = \imagecreatefromjpeg($sourcePath);
                    break;
                case IMAGETYPE_PNG:
                    $sourceImage = \imagecreatefrompng($sourcePath);
                    break;
                case IMAGETYPE_GIF:
                    $sourceImage = \imagecreatefromgif($sourcePath);
                    break;
                case IMAGETYPE_WEBP:
                    if (function_exists('imagecreatefromwebp')) {
                        $sourceImage = \imagecreatefromwebp($sourcePath);
                    } else {
                        throw new \Exception('WebP support not available');
                    }
                    break;
                default:
                    throw new \Exception('Unsupported image format');
            }
            
            if (!$sourceImage) {
                throw new \Exception('Failed to create image resource');
            }
            
            // Create new optimized image
            $optimizedImage = \imagecreatetruecolor($newWidth, $newHeight);
            
            // Preserve transparency for PNG
            if ($imageType === IMAGETYPE_PNG) {
                \imagealphablending($optimizedImage, false);
                \imagesavealpha($optimizedImage, true);
                $transparent = \imagecolorallocatealpha($optimizedImage, 255, 255, 255, 127);
                \imagefill($optimizedImage, 0, 0, $transparent);
            }
            
            // Resize image with high quality
            \imagecopyresampled(
                $optimizedImage, $sourceImage,
                0, 0, 0, 0,
                $newWidth, $newHeight,
                $originalWidth, $originalHeight
            );
            
            // Save as optimized JPEG
            $result = \imagejpeg($optimizedImage, $destinationPath, $quality);
            
            // Clean up memory
            \imagedestroy($sourceImage);
            \imagedestroy($optimizedImage);
            
            if (!$result) {
                throw new \Exception('Failed to save optimized image');
            }
            
            // Log optimization results
            $originalSize = filesize($sourcePath);
            $optimizedSize = filesize($destinationPath);
            $compressionRatio = round((1 - ($optimizedSize / $originalSize)) * 100, 2);
            
            Log::info('Image optimization completed', [
                'type' => $type,
                'original_size' => $originalSize . ' bytes',
                'optimized_size' => $optimizedSize . ' bytes',
                'compression' => $compressionRatio . '%',
                'dimensions' => $newWidth . 'x' . $newHeight
            ]);
            
        } catch (\Exception $e) {
            Log::error('Image optimization failed', [
                'error' => $e->getMessage(),
                'type' => $type,
                'source' => $sourcePath
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
}
