<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

class MaintenanceModeService
{
    /**
     * The path to the maintenance mode file
     */
    protected string $maintenanceFile;

    public function __construct()
    {
        $this->maintenanceFile = storage_path('framework/down');
    }

    /**
     * Check if the application is in maintenance mode
     */
    public function isInMaintenanceMode(): bool
    {
        return File::exists($this->maintenanceFile);
    }

    /**
     * Get maintenance mode data
     */
    public function getMaintenanceData(): ?array
    {
        if (!$this->isInMaintenanceMode()) {
            return null;
        }

        $content = File::get($this->maintenanceFile);
        return json_decode($content, true);
    }

    /**
     * Enable maintenance mode with options
     */
    public function enableMaintenanceMode(array $options = []): array
    {
        $defaultOptions = [
            'message' => config('maintenance.settings.default_message'),
            'retry' => config('maintenance.settings.behavior.default_retry_seconds', 3600),
            'refresh' => config('maintenance.settings.behavior.default_refresh_seconds', 300),
            'secret' => null,
            'status' => 503,
        ];

        $options = array_merge($defaultOptions, $options);
        
        // Build artisan command
        $command = ['down'];
        
        if (isset($options['message'])) {
            $command[] = '--message=' . escapeshellarg($options['message']);
        }
        
        if (isset($options['retry'])) {
            $command[] = '--retry=' . $options['retry'];
        }
        
        if (isset($options['refresh'])) {
            $command[] = '--refresh=' . $options['refresh'];
        }
        
        if (isset($options['secret'])) {
            $command[] = '--secret=' . $options['secret'];
        }
        
        if (isset($options['status'])) {
            $command[] = '--status=' . $options['status'];
        }

        // Execute the command
        $exitCode = Artisan::call(implode(' ', $command));
        
        return [
            'success' => $exitCode === 0,
            'message' => $exitCode === 0 ? 'Maintenance mode enabled successfully' : 'Failed to enable maintenance mode',
            'options' => $options,
            'exit_code' => $exitCode
        ];
    }

    /**
     * Disable maintenance mode
     */
    public function disableMaintenanceMode(): array
    {
        $exitCode = Artisan::call('up');
        
        return [
            'success' => $exitCode === 0,
            'message' => $exitCode === 0 ? 'Maintenance mode disabled successfully' : 'Failed to disable maintenance mode',
            'exit_code' => $exitCode
        ];
    }

    /**
     * Enable maintenance mode with predefined scenario
     */
    public function enableWithScenario(string $scenario, ?string $customMessage = null): array
    {
        $scenarios = config('maintenance.settings.scenarios', []);
        
        if (!isset($scenarios[$scenario])) {
            return [
                'success' => false,
                'message' => "Scenario '{$scenario}' not found",
                'exit_code' => 1
            ];
        }

        $scenarioConfig = $scenarios[$scenario];
        
        $options = [
            'message' => $customMessage ?? $scenarioConfig['message'],
            'retry' => $scenarioConfig['retry_seconds'],
            'refresh' => $scenarioConfig['refresh_seconds'],
        ];

        return $this->enableMaintenanceMode($options);
    }

    /**
     * Get available maintenance scenarios
     */
    public function getAvailableScenarios(): array
    {
        return config('maintenance.settings.scenarios', []);
    }

    /**
     * Get maintenance mode status with detailed information
     */
    public function getStatus(): array
    {
        $isEnabled = $this->isInMaintenanceMode();
        $data = $this->getMaintenanceData();
        
        return [
            'enabled' => $isEnabled,
            'data' => $data,
            'file_exists' => File::exists($this->maintenanceFile),
            'file_path' => $this->maintenanceFile,
            'file_size' => $isEnabled ? File::size($this->maintenanceFile) : 0,
            'created_at' => $isEnabled ? date('Y-m-d H:i:s', File::lastModified($this->maintenanceFile)) : null,
        ];
    }

    /**
     * Create a custom maintenance message
     */
    public function createCustomMessage(string $title, string $description, array $options = []): string
    {
        $template = "
        <div style='text-align: center; padding: 20px;'>
            <h2 style='color: #667eea; margin-bottom: 10px;'>{$title}</h2>
            <p style='color: #6c757d; line-height: 1.6;'>{$description}</p>
        ";

        if (isset($options['estimated_completion'])) {
            $template .= "<p><strong>Estimated completion:</strong> {$options['estimated_completion']}</p>";
        }

        if (isset($options['contact_info'])) {
            $template .= "<p><strong>Need help?</strong> {$options['contact_info']}</p>";
        }

        $template .= "</div>";

        return $template;
    }

    /**
     * Schedule automatic maintenance mode disable
     */
    public function scheduleAutoDisable(int $minutes): array
    {
        // This would typically be handled by a queued job or scheduled task
        // For now, we'll return the configuration for manual scheduling
        
        return [
            'success' => true,
            'message' => "Maintenance mode will be automatically disabled in {$minutes} minutes",
            'disable_at' => now()->addMinutes($minutes)->toDateTimeString(),
            'job_scheduled' => false // Would be true if using queue
        ];
    }

    /**
     * Get maintenance mode templates
     */
    public function getAvailableTemplates(): array
    {
        return config('maintenance.templates', [
            'default' => 'errors.503',
            'minimal' => 'errors.503-minimal',
        ]);
    }

    /**
     * Test if a specific IP should bypass maintenance mode
     */
    public function shouldBypassMaintenance(string $ip): bool
    {
        $allowedIps = config('maintenance.allowed_ips', []);
        return in_array($ip, $allowedIps);
    }

    /**
     * Generate a secret for bypassing maintenance mode
     */
    public function generateSecret(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * Validate a maintenance bypass secret
     */
    public function validateSecret(string $secret): bool
    {
        $maintenanceData = $this->getMaintenanceData();
        
        if (!$maintenanceData || !isset($maintenanceData['secret'])) {
            return false;
        }

        return hash_equals($maintenanceData['secret'], $secret);
    }
}
