<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MaintenanceModeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class MaintenanceController extends Controller
{
    protected MaintenanceModeService $maintenanceService;

    public function __construct(MaintenanceModeService $maintenanceService)
    {
        $this->maintenanceService = $maintenanceService;
    }

    /**
     * Display the maintenance mode management interface
     */
    public function index()
    {
        $isInMaintenance = $this->maintenanceService->isInMaintenanceMode();
        $maintenanceData = $this->maintenanceService->getMaintenanceData();
        $scenarios = $this->maintenanceService->getAvailableScenarios();
        $templates = $this->maintenanceService->getAvailableTemplates();
        $status = $this->maintenanceService->getStatus();

        return view('admin.maintenance.index', compact(
            'isInMaintenance',
            'maintenanceData',
            'scenarios',
            'templates',
            'status'
        ));
    }

    /**
     * Enable maintenance mode with custom options
     */
    public function enable(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string|max:500',
            'retry' => 'nullable|integer|min:60|max:86400',
            'refresh' => 'nullable|integer|min:0|max:3600',
            'template' => 'nullable|string|in:default,minimal',
            'secret' => 'nullable|string|max:50'
        ]);

        $options = [];
        
        if ($request->has('message') && $request->message) {
            $options['message'] = $request->message;
        }
        
        if ($request->has('retry') && $request->retry) {
            $options['retry'] = $request->retry;
        }
        
        if ($request->has('refresh') && $request->refresh) {
            $options['refresh'] = $request->refresh;
        }
        
        if ($request->has('secret') && $request->secret) {
            $options['secret'] = $request->secret;
        }

        // Handle template rendering
        if ($request->has('template') && $request->template !== 'default') {
            $options['render'] = "errors::503-{$request->template}";
        }

        $result = $this->maintenanceService->enableMaintenanceMode($options);

        return response()->json($result);
    }

    /**
     * Disable maintenance mode
     */
    public function disable()
    {
        $result = $this->maintenanceService->disableMaintenanceMode();
        return response()->json($result);
    }

    /**
     * Enable maintenance mode with predefined scenario
     */
    public function enableScenario(Request $request)
    {
        $request->validate([
            'scenario' => 'required|string',
            'custom_message' => 'nullable|string|max:500'
        ]);

        $result = $this->maintenanceService->enableWithScenario(
            $request->scenario,
            $request->custom_message
        );

        return response()->json($result);
    }

    /**
     * Get current maintenance status
     */
    public function status()
    {
        $status = $this->maintenanceService->getStatus();
        return response()->json($status);
    }

    /**
     * Generate a bypass secret
     */
    public function generateSecret()
    {
        $secret = $this->maintenanceService->generateSecret();
        return response()->json(['secret' => $secret]);
    }

    /**
     * Preview maintenance page
     */
    public function preview(Request $request)
    {
        $template = $request->get('template', 'default');
        $message = $request->get('message', 'This is a preview of the maintenance page.');
        
        $maintenanceData = [
            'message' => $message,
            'retry' => 3600,
            'refresh' => 300,
            'status' => 503
        ];

        $viewName = $template === 'minimal' ? 'errors.503-minimal' : 'errors.503';
        
        return view($viewName, compact('maintenanceData'));
    }

    /**
     * Get maintenance templates
     */
    public function templates()
    {
        $templates = $this->maintenanceService->getAvailableTemplates();
        return response()->json($templates);
    }

    /**
     * Get available scenarios
     */
    public function scenarios()
    {
        $scenarios = $this->maintenanceService->getAvailableScenarios();
        return response()->json($scenarios);
    }

    /**
     * Schedule automatic maintenance disable
     */
    public function scheduleDisable(Request $request)
    {
        $request->validate([
            'minutes' => 'required|integer|min:1|max:1440' // Max 24 hours
        ]);

        $result = $this->maintenanceService->scheduleAutoDisable($request->minutes);
        return response()->json($result);
    }

    /**
     * Test if IP should bypass maintenance
     */
    public function testBypass(Request $request)
    {
        $request->validate([
            'ip' => 'required|ip'
        ]);

        $shouldBypass = $this->maintenanceService->shouldBypassMaintenance($request->ip);
        
        return response()->json([
            'ip' => $request->ip,
            'should_bypass' => $shouldBypass
        ]);
    }

    /**
     * Validate bypass secret
     */
    public function validateSecret(Request $request)
    {
        $request->validate([
            'secret' => 'required|string'
        ]);

        $isValid = $this->maintenanceService->validateSecret($request->secret);
        
        return response()->json([
            'valid' => $isValid
        ]);
    }
}
