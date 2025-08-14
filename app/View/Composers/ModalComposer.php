<?php

namespace App\View\Composers;

use App\Services\ModalService;
use Illuminate\View\View;
use Illuminate\Support\Facades\Route;

class ModalComposer
{
    protected $modalService;
    
    public function __construct(ModalService $modalService)
    {
        $this->modalService = $modalService;
    }
    
    /**
     * Bind data to the view
     */
    public function compose(View $view)
    {
        // Only add modals to non-admin routes
        $currentRoute = Route::currentRouteName();
        
        if (!str_starts_with($currentRoute, 'admin.')) {
            $modals = $this->modalService->getModalsToShow();
            $view->with('dynamicModals', $modals);
        }
    }
}
