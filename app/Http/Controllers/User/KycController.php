<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\KycVerification;
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
        
        try {
            $user = Auth::user();
            $c = json_decode(file_get_contents(resource_path('views/country/country.json')));
            $countries = [];
            foreach ($c as $k => $country) {
                $countries[] = ['country'=> $country->country,'dial_code'=> $country->dial_code,'country_code'=> $k];
                if($country->country == $request->country) {
                    $request->merge(['dial_code' => $country->dial_code]);
                }
            }
            // Process mobile number: remove leading 0 and combine with dial code
            $phoneNumber = $request->phone_number;
            $dialCode = $request->dial_code ?? '';
            // Remove leading zero from phone number if exists
            if (str_starts_with($phoneNumber, '0')) {
                $phoneNumber = substr($phoneNumber, 1);
            }
            // Combine dial code with processed phone number
            $fullMobileNumber = $dialCode . $phoneNumber;
            // Check if user already has a pending or approved KYC
            if ($user->kv == 2 || $user->kv == 1) {
                return redirect()->route('user.kyc.index')
                    ->with('error', 'You already have a KYC verification request.');
            }
            $request->validate(KycVerification::getValidationRules('step1'),);
            $request->validate(KycVerification::getValidationRules('step2'));
            $request->validate(KycVerification::getValidationRules('step3'));

            // Create directory for user KYC files
            $userKycPath = 'kyc_documents/' . $user->id;

            // Store uploaded files with proper naming
            $documentFront = $this->storeKycFile($request->file('document_front'), $userKycPath, 'front');
            $documentBack = $request->hasFile('document_back') 
                ? $this->storeKycFile($request->file('document_back'), $userKycPath, 'back')
                : null;
            $selfieImage = $this->storeKycFile($request->file('selfie_image'), $userKycPath, 'selfie');

            // Create KYC verification record
            KycVerification::create([
                'user_id' => $user->id,
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
                'status' => 'pending',
                'submitted_at' => now()
            ]);
            // Update user's KYC status
            $user = User::find(Auth::id());
            $user->firstname = $request->first_name;
            $user->lastname = $request->last_name;
            $user->kv = 2; // 2 = KYC pending
            $user->save();
            return redirect()->route('user.kyc.index')
                ->with('success', 'KYC verification submitted successfully! We will review your application within 24-48 hours.');

        } catch (\Exception $e) {
            Log::error('KYC submission error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while submitting your KYC verification. Please try again.')
                ->withInput();
        }
    }

    /**
     * Store KYC file with proper naming
     */
    private function storeKycFile($file, $path, $type)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = $type . '_' . time() . '_' . uniqid() . '.' . $extension;
        return $file->storeAs($path, $filename, 'public');
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
        $mimeType = Storage::disk('public')->mimeType($filePath);
        
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
        
        if (!$documentNumber) {
            return response()->json(['available' => false, 'message' => 'Document number is required']);
        }
        
        $exists = KycVerification::where('document_number', $documentNumber)->exists();
        
        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Document number already exists' : 'Document number is available'
        ]);
    }
}
