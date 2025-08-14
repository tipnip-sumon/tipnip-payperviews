<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ModalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ModalApiController extends Controller
{
    protected $modalService;
    
    public function __construct(ModalService $modalService)
    {
        $this->modalService = $modalService;
    }
    
    /**
     * Get modals to show for current user
     */
    public function getModals()
    {
        try {
            $modals = $this->modalService->getModalsToShow();
            
            return response()->json([
                'success' => true,
                'data' => $modals,
                'count' => count($modals)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching modals',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Record modal show event
     */
    public function recordShow(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'modal_name' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request data',
                'errors' => $validator->errors()
            ], 400);
        }
        
        try {
            $userId = Auth::id();
            $modalName = $request->modal_name;
            
            $this->modalService->recordModalShow($modalName, $userId);
            
            return response()->json([
                'success' => true,
                'message' => 'Modal show recorded successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error recording modal show',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Record modal click event
     */
    public function recordClick(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'modal_name' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request data',
                'errors' => $validator->errors()
            ], 400);
        }
        
        try {
            $userId = Auth::id();
            $modalName = $request->modal_name;
            
            $this->modalService->recordModalClick($modalName, $userId);
            
            return response()->json([
                'success' => true,
                'message' => 'Modal click recorded successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error recording modal click',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Record modal dismiss event
     */
    public function recordDismiss(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'modal_name' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request data',
                'errors' => $validator->errors()
            ], 400);
        }
        
        try {
            $userId = Auth::id();
            $modalName = $request->modal_name;
            
            $this->modalService->recordModalDismiss($modalName, $userId);
            
            return response()->json([
                'success' => true,
                'message' => 'Modal dismiss recorded successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error recording modal dismiss',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
