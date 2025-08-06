<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AdminKycController extends Controller
{
    /**
     * Display all KYC verifications
     */
    public function index(Request $request)
    {
        $query = KycVerification::with('user')->latest();
        
        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Search by user details
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $kycVerifications = $query->paginate(20);
        
        $data = [
            'pageTitle' => 'KYC Verifications Management',
            'kycVerifications' => $kycVerifications,
            'filters' => $request->only(['status', 'search'])
        ];
        
        return view('admin.kyc.index', $data);
    }
    
    /**
     * Show specific KYC verification details
     */
    public function show($id)
    {
        $kycVerification = KycVerification::with(['user', 'reviewer'])->findOrFail($id);
        
        $data = [
            'pageTitle' => 'KYC Verification Details',
            'kycVerification' => $kycVerification
        ];
        
        return view('admin.kyc.show', $data);
    }
    
    /**
     * Update KYC verification status (approve/reject/under_review)
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            // Log the incoming request
            \Log::info('KYC Update Status Request', [
                'kyc_id' => $id,
                'request_data' => $request->all(),
                'admin_id' => Auth::id()
            ]);
            
            // Validate request
            $validated = $request->validate([
                'status' => 'required|in:approved,rejected,under_review,pending',
                'admin_remarks' => 'nullable|string|max:1000'
            ]);
            
            // Find KYC record
            $kycVerification = KycVerification::find($id);
            if (!$kycVerification) {
                return response()->json([
                    'success' => false,
                    'message' => 'KYC verification not found.'
                ], 404);
            }
            
            // Update KYC status
            $kycVerification->update([
                'status' => $validated['status'],
                'admin_remarks' => $validated['admin_remarks'],
                'reviewed_at' => now(),
                'reviewed_by' => Auth::id(),
                'approved_at' => $validated['status'] === 'approved' ? now() : null,
                'rejected_at' => $validated['status'] === 'rejected' ? now() : null,
            ]);
            
            // Update user KYC status
            if ($kycVerification->user) {
                $userKvStatus = [
                    'approved' => 1,
                    'rejected' => 0,
                    'under_review' => 2,
                    'pending' => 0
                ];
                
                $kycVerification->user->update([
                    'kv' => $userKvStatus[$validated['status']] ?? 0
                ]);
            }
            
            \Log::info('KYC Status Updated Successfully', [
                'kyc_id' => $id,
                'new_status' => $validated['status'],
                'admin_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'KYC verification status updated successfully!'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $e->errors())
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('KYC Update Error', [
                'kyc_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mark KYC as under review
     */
    public function markUnderReview(Request $request, $id)
    {
        try {
            $request->validate([
                'admin_remarks' => 'nullable|string|max:1000'
            ]);
            
            $kycVerification = KycVerification::findOrFail($id);
            $oldStatus = $kycVerification->status;
            
            // Update to under review
            $kycVerification->update([
                'status' => 'under_review',
                'admin_remarks' => $request->admin_remarks ?? 'Document under review by admin.',
                'reviewed_at' => now(),
                'reviewed_by' => Auth::id(),
                'under_review_at' => now(),
                'approved_at' => null,
                'rejected_at' => null,
            ]);
            
            // Update user's KYC status
            $user = $kycVerification->user;
            if ($user) {
                $user->kv = 2; // Under review
                $user->save();
            }
            
            Log::info('KYC marked as under review', [
                'kyc_id' => $id,
                'user_id' => $kycVerification->user_id,
                'old_status' => $oldStatus,
                'admin_id' => Auth::id(),
                'admin_remarks' => $request->admin_remarks
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'KYC verification marked as under review!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error marking KYC under review: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the status.'
            ], 500);
        }
    }
    
    /**
     * Bulk change status
     */
    public function bulkChangeStatus(Request $request)
    {
        try {
            $request->validate([
                'kyc_ids' => 'required|array',
                'kyc_ids.*' => 'exists:kyc_verifications,id',
                'status' => 'required|in:approved,rejected,under_review,pending',
                'admin_remarks' => 'nullable|string|max:1000'
            ]);
            
            $kycVerifications = KycVerification::whereIn('id', $request->kyc_ids)
                ->with('user')
                ->get();
            
            $updated = 0;
            $adminRemarks = $request->admin_remarks ?? "Bulk {$request->status} by admin";
            
            foreach ($kycVerifications as $kyc) {
                $updateData = [
                    'status' => $request->status,
                    'admin_remarks' => $adminRemarks,
                    'reviewed_at' => now(),
                    'reviewed_by' => Auth::id(),
                ];
                
                // Set status-specific timestamps and user KV status
                switch ($request->status) {
                    case 'approved':
                        $updateData['approved_at'] = now();
                        $updateData['rejected_at'] = null;
                        $updateData['under_review_at'] = null;
                        $userKvStatus = 1;
                        break;
                        
                    case 'rejected':
                        $updateData['rejected_at'] = now();
                        $updateData['approved_at'] = null;
                        $updateData['under_review_at'] = null;
                        $userKvStatus = 0;
                        break;
                        
                    case 'under_review':
                        $updateData['under_review_at'] = now();
                        $updateData['approved_at'] = null;
                        $updateData['rejected_at'] = null;
                        $userKvStatus = 2;
                        break;
                        
                    default:
                        $updateData['approved_at'] = null;
                        $updateData['rejected_at'] = null;
                        $updateData['under_review_at'] = null;
                        $userKvStatus = 0;
                        break;
                }
                
                $kyc->update($updateData);
                
                // Update user KYC status
                if ($kyc->user) {
                    $kyc->user->update(['kv' => $userKvStatus]);
                }
                
                $updated++;
            }
            
            Log::info('Bulk KYC status change completed', [
                'admin_id' => Auth::id(),
                'status' => $request->status,
                'updated_count' => $updated,
                'kyc_ids' => $request->kyc_ids
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "{$updated} KYC verification(s) status changed to {$request->status} successfully!"
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in bulk status change: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during bulk status change.'
            ], 500);
        }
    }
    
    /**
     * Bulk approve KYC verifications (legacy method)
     */
    public function bulkApprove(Request $request)
    {
        return $this->bulkChangeStatus(array_merge($request->all(), ['status' => 'approved']));
    }
    
    /**
     * Get KYC verifications under review
     */
    public function getUnderReview(Request $request)
    {
        $query = KycVerification::with('user')
            ->where('status', 'under_review')
            ->latest('under_review_at');
        
        $kycVerifications = $query->paginate(20);
        
        $data = [
            'pageTitle' => 'KYC Verifications Under Review',
            'kycVerifications' => $kycVerifications,
            'filters' => ['status' => 'under_review']
        ];
        
        return view('admin.kyc.index', $data);
    }
    
    /**
     * View KYC document
     */
    public function viewDocument($id, $type)
    {
        $kyc = KycVerification::findOrFail($id);
        
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
     * Download KYC document
     */
    public function downloadDocument($id, $type)
    {
        $kyc = KycVerification::findOrFail($id);
        
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
     * Get KYC statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => KycVerification::count(),
            'pending' => KycVerification::where('status', 'pending')->count(),
            'approved' => KycVerification::where('status', 'approved')->count(),
            'rejected' => KycVerification::where('status', 'rejected')->count(),
            'under_review' => KycVerification::where('status', 'under_review')->count(),
        ];
        
        return response()->json($stats);
    }
}
