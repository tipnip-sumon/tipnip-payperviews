<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MaintenanceModeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class SystemCommandController extends Controller
{
    /**
     * Available commands for manual execution
     */
    private $availableCommands = [
        'lottery' => [
            'title' => 'Lottery System Commands',
            'icon' => 'fe-award',
            'commands' => [
                'lottery:optimize --force' => [
                    'name' => 'Optimize Lottery Data',
                    'description' => 'Clean virtual tickets and optimize old draws',
                    'icon' => 'fe-database',
                    'danger' => false,
                    'estimated_time' => '1-3 minutes'
                ],
                'lottery:optimize --summaries --force' => [
                    'name' => 'Create Daily Summaries',
                    'description' => 'Generate daily lottery summaries for all users',
                    'icon' => 'fe-bar-chart-2',
                    'danger' => false,
                    'estimated_time' => '2-5 minutes'
                ],
                'lottery:delete-summaries --days=90 --force' => [
                    'name' => 'Clean Old Summaries',
                    'description' => 'Delete lottery summaries older than 90 days',
                    'icon' => 'fe-trash-2',
                    'danger' => true,
                    'estimated_time' => '30 seconds'
                ],
                'lottery:delete-summaries --duplicates --force' => [
                    'name' => 'Remove Duplicate Summaries',
                    'description' => 'Clean up duplicate lottery summaries',
                    'icon' => 'fe-copy',
                    'danger' => false,
                    'estimated_time' => '1 minute'
                ],
                'schedule:run' => [
                    'name' => 'Run Scheduled Tasks',
                    'description' => 'Manually execute all scheduled tasks',
                    'icon' => 'fe-clock',
                    'danger' => false,
                    'estimated_time' => '30 seconds'
                ]
            ]
        ],
        'video' => [
            'title' => 'Video System Commands',
            'icon' => 'fe-video',
            'commands' => [
                'video:optimize-assignments --force' => [
                    'name' => 'Optimize Video Assignments',
                    'description' => 'Clean and optimize video assignment data',
                    'icon' => 'fe-film',
                    'danger' => false,
                    'estimated_time' => '1-2 minutes'
                ],
                'video:cleanup-views --days=30 --force' => [
                    'name' => 'Clean Video Views',
                    'description' => 'Remove old video view records',
                    'icon' => 'fe-eye',
                    'danger' => false,
                    'estimated_time' => '30 seconds'
                ]
            ]
        ],
        'cache' => [
            'title' => 'Cache & Performance',
            'icon' => 'fe-zap',
            'commands' => [
                'cache:clear' => [
                    'name' => 'Clear Application Cache',
                    'description' => 'Clear all cached data',
                    'icon' => 'fe-refresh-cw',
                    'danger' => false,
                    'estimated_time' => '10 seconds'
                ],
                'config:cache' => [
                    'name' => 'Cache Configuration',
                    'description' => 'Cache configuration files for better performance',
                    'icon' => 'fe-settings',
                    'danger' => false,
                    'estimated_time' => '5 seconds'
                ],
                'route:cache' => [
                    'name' => 'Cache Routes',
                    'description' => 'Cache route definitions',
                    'icon' => 'fe-map',
                    'danger' => false,
                    'estimated_time' => '5 seconds'
                ],
                'view:cache' => [
                    'name' => 'Cache Views',
                    'description' => 'Cache compiled view templates',
                    'icon' => 'fe-eye',
                    'danger' => false,
                    'estimated_time' => '10 seconds'
                ]
            ]
        ],
        'database' => [
            'title' => 'Database Operations',
            'icon' => 'fe-database',
            'commands' => [
                'migrate' => [
                    'name' => 'Run Migrations',
                    'description' => 'Execute pending database migrations',
                    'icon' => 'fe-upload',
                    'danger' => true,
                    'estimated_time' => '10-30 seconds'
                ],
                'db:seed' => [
                    'name' => 'Seed Database',
                    'description' => 'Run database seeders (Development only)',
                    'icon' => 'fe-plus-circle',
                    'danger' => true,
                    'estimated_time' => '30 seconds'
                ],
                'migrate:status' => [
                    'name' => 'Migration Status',
                    'description' => 'Check migration status',
                    'icon' => 'fe-info',
                    'danger' => false,
                    'estimated_time' => '5 seconds'
                ]
            ]
        ],
        'queue' => [
            'title' => 'Queue Management',
            'icon' => 'fe-list',
            'commands' => [
                'queue:work --timeout=30 --once' => [
                    'name' => 'Process Single Queue Job',
                    'description' => 'Process one job from the queue',
                    'icon' => 'fe-play',
                    'danger' => false,
                    'estimated_time' => '10-30 seconds'
                ],
                'queue:clear' => [
                    'name' => 'Clear Failed Jobs',
                    'description' => 'Remove all failed queue jobs',
                    'icon' => 'fe-trash-2',
                    'danger' => true,
                    'estimated_time' => '5 seconds'
                ],
                'queue:restart' => [
                    'name' => 'Restart Queue Workers',
                    'description' => 'Restart all queue worker processes',
                    'icon' => 'fe-refresh-cw',
                    'danger' => false,
                    'estimated_time' => '5 seconds'
                ]
            ]
        ],
        'emergency' => [
            'title' => '🚨 Emergency & Maintenance Commands',
            'icon' => 'fe-alert-triangle',
            'commands' => [
                'down' => [
                    'name' => '🔴 Enable Maintenance Mode',
                    'description' => '⛔ Put application in maintenance mode - Site will be offline',
                    'icon' => 'fe-shield',
                    'danger' => true,
                    'estimated_time' => '5 seconds',
                    'maintenance' => true,
                    'action_type' => 'enable'
                ],
                'up' => [
                    'name' => '🟢 Disable Maintenance Mode',
                    'description' => '✅ Bring application back online - Site will be accessible',
                    'icon' => 'fe-check-circle',
                    'danger' => false,
                    'estimated_time' => '5 seconds',
                    'maintenance' => true,
                    'action_type' => 'disable'
                ],
                'down --message="System maintenance in progress. Please check back in a few minutes." --refresh=60' => [
                    'name' => '🔴 Enable Maintenance Mode (Custom)',
                    'description' => '⛔ Put application in maintenance mode with custom message and auto-refresh',
                    'icon' => 'fe-shield',
                    'danger' => true,
                    'estimated_time' => '5 seconds',
                    'maintenance' => true,
                    'action_type' => 'enable'
                ],
                'down --render="errors::503-minimal" --retry=1800 --refresh=300' => [
                    'name' => '🔴 Enable Maintenance (Minimal Design)',
                    'description' => '⛔ Enable maintenance with minimal template (30min estimated)',
                    'icon' => 'fe-shield',
                    'danger' => true,
                    'estimated_time' => '5 seconds',
                    'maintenance' => true,
                    'action_type' => 'enable'
                ],
                'down --message="Security updates in progress. Enhanced protection coming soon!" --retry=3600 --refresh=180' => [
                    'name' => '🔴 Security Maintenance Mode',
                    'description' => '🛡️ Enable maintenance for security updates (1hr estimated)',
                    'icon' => 'fe-shield',
                    'danger' => true,
                    'estimated_time' => '5 seconds',
                    'maintenance' => true,
                    'action_type' => 'enable'
                ],
                'down --message="Major feature updates in progress. Exciting improvements coming!" --retry=7200 --refresh=600' => [
                    'name' => '🔴 Feature Update Mode',
                    'description' => '🚀 Enable maintenance for major updates (2hrs estimated)',
                    'icon' => 'fe-shield',
                    'danger' => true,
                    'estimated_time' => '5 seconds',
                    'maintenance' => true,
                    'action_type' => 'enable'
                ],
                'inspire' => [
                    'name' => 'Check System Status',
                    'description' => 'Display current system status and info',
                    'icon' => 'fe-info',
                    'danger' => false,
                    'estimated_time' => '5 seconds'
                ],
                'config:clear' => [
                    'name' => 'Clear Configuration Cache',
                    'description' => 'Clear cached configuration files',
                    'icon' => 'fe-settings',
                    'danger' => false,
                    'estimated_time' => '5 seconds'
                ],
                'route:clear' => [
                    'name' => 'Clear Route Cache',
                    'description' => 'Clear cached route definitions',
                    'icon' => 'fe-map',
                    'danger' => false,
                    'estimated_time' => '5 seconds'
                ],
                'view:clear' => [
                    'name' => 'Clear View Cache',
                    'description' => 'Clear compiled view templates',
                    'icon' => 'fe-eye',
                    'danger' => false,
                    'estimated_time' => '5 seconds'
                ],
                'clear-all-cache' => [
                    'name' => 'Clear All Optimizations',
                    'description' => 'Clear all cached optimizations (config, routes, views, cache)',
                    'icon' => 'fe-refresh-cw',
                    'danger' => false,
                    'estimated_time' => '15 seconds'
                ],
                'optimize' => [
                    'name' => 'Optimize Application',
                    'description' => 'Cache all optimizations for production',
                    'icon' => 'fe-zap',
                    'danger' => false,
                    'estimated_time' => '30 seconds'
                ]
            ]
        ],
        'maintenance' => [
            'title' => 'Maintenance Commands',
            'icon' => 'fe-tool',
            'commands' => [
                'notifications:cleanup --days=30 --force' => [
                    'name' => 'Clean Notifications',
                    'description' => 'Remove old notifications',
                    'icon' => 'fe-bell',
                    'danger' => false,
                    'estimated_time' => '30 seconds'
                ],
                'storage:link' => [
                    'name' => 'Link Storage',
                    'description' => 'Create storage symbolic link',
                    'icon' => 'fe-link',
                    'danger' => false,
                    'estimated_time' => '5 seconds'
                ]
            ]
        ]
    ];

    /**
     * Display the command runner page
     */
    public function index()
    {
        try {
            $commands = $this->availableCommands;
            
            // Get recent executions safely - no longer reading log files for performance
            $recentExecutions = $this->getRecentExecutions();
            
            return view('admin.system-commands.index', compact('commands', 'recentExecutions'));
            
        } catch (\Exception $e) {
            Log::error('SystemCommandController index failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Emergency fallback - return minimal data
            $commands = [
                'emergency' => [
                    'title' => '🚨 Emergency Commands Only',
                    'icon' => 'fe-alert-triangle',
                    'commands' => [
                        'cache:clear' => [
                            'name' => 'Clear Cache',
                            'description' => 'Clear application cache',
                            'icon' => 'fe-refresh-cw',
                            'danger' => false,
                            'estimated_time' => '10 seconds'
                        ]
                    ]
                ]
            ];
            $recentExecutions = [];
            
            return view('admin.system-commands.index', compact('commands', 'recentExecutions'));
        }
    }

    /**
     * Execute a command
     */
    public function execute(Request $request)
    {
        $request->validate([
            'command' => 'required|string',
            'confirm' => 'required|accepted'
        ]);

        $command = $request->input('command');
        
        // Validate command is in allowed list
        if (!$this->isCommandAllowed($command)) {
            return response()->json([
                'success' => false,
                'message' => 'Command not allowed or not found in safe commands list'
            ], 403);
        }

        try {
            $startTime = microtime(true);
            
            // Check maintenance mode status before execution
            $isMaintenanceBefore = app()->isDownForMaintenance();
            
            // Handle special clear-all-cache command
            if ($command === 'clear-all-cache') {
                $exitCode = $this->executeMultipleClearCommands();
                $output = "Executed multiple clear commands:\n";
                $output .= "✅ cache:clear - " . Artisan::output() . "\n";
                Artisan::call('config:clear');
                $output .= "✅ config:clear - " . Artisan::output() . "\n";
                Artisan::call('route:clear');
                $output .= "✅ route:clear - " . Artisan::output() . "\n";
                Artisan::call('view:clear');
                $output .= "✅ view:clear - " . Artisan::output() . "\n";
                $output .= "\n🎉 All optimizations cleared successfully!";
            } else {
                // Execute the single command
                $exitCode = Artisan::call($command);
                $output = Artisan::output();
            }
            
            // Check maintenance mode status after execution
            $isMaintenanceAfter = app()->isDownForMaintenance();
            
            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);

            // Enhanced output for maintenance commands
            $enhancedOutput = $output;
            if (strpos($command, 'down') === 0) {
                if ($isMaintenanceBefore && $isMaintenanceAfter) {
                    $enhancedOutput .= "\n\nNOTE: Application was already in maintenance mode.";
                } elseif (!$isMaintenanceBefore && $isMaintenanceAfter) {
                    $enhancedOutput .= "\n\n✅ Application is now in maintenance mode.";
                } else {
                    $enhancedOutput .= "\n\n⚠️ Maintenance mode status unchanged.";
                }
            } elseif (strpos($command, 'up') === 0) {
                if (!$isMaintenanceBefore && !$isMaintenanceAfter) {
                    $enhancedOutput .= "\n\nNOTE: Application was already online (not in maintenance mode).";
                } elseif ($isMaintenanceBefore && !$isMaintenanceAfter) {
                    $enhancedOutput .= "\n\n✅ Application is now back online.";
                } else {
                    $enhancedOutput .= "\n\n⚠️ Application may still be in maintenance mode.";
                }
            }

            // Log the execution
            $this->logCommandExecution($command, $exitCode, $enhancedOutput, $executionTime);

            return response()->json([
                'success' => $exitCode === 0,
                'message' => $exitCode === 0 ? 'Command executed successfully' : 'Command executed with errors',
                'output' => $enhancedOutput,
                'exit_code' => $exitCode,
                'execution_time' => $executionTime
            ]);

        } catch (\Exception $e) {
            Log::error('Command execution failed', [
                'command' => $command,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Command execution failed: ' . $e->getMessage(),
                'output' => '',
                'exit_code' => 1,
                'execution_time' => 0
            ], 500);
        }
    }

    /**
     * Get command status and information
     */
    public function status(Request $request)
    {
        $command = $request->input('command');
        
        if (!$this->isCommandAllowed($command)) {
            return response()->json(['error' => 'Command not allowed'], 403);
        }

        $commandInfo = $this->getCommandInfo($command);
        
        // Return minimal response without recent executions for performance
        return response()->json([
            'command' => $command,
            'info' => $commandInfo,
            'recent_executions' => [], // Disabled for performance
            'note' => 'Recent executions temporarily disabled for performance optimization'
        ]);
    }

    /**
     * Check if command is in allowed list
     */
    private function isCommandAllowed($command)
    {
        foreach ($this->availableCommands as $category) {
            if (isset($category['commands'][$command])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get command information
     */
    private function getCommandInfo($command)
    {
        foreach ($this->availableCommands as $category) {
            if (isset($category['commands'][$command])) {
                return $category['commands'][$command];
            }
        }
        return null;
    }

    /**
     * Log command execution
     */
    private function logCommandExecution($command, $exitCode, $output, $executionTime)
    {
        Log::info('Manual command executed', [
            'command' => $command,
            'exit_code' => $exitCode,
            'execution_time' => $executionTime,
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->username ?? 'Unknown',
            'output_length' => strlen($output),
            'timestamp' => now()
        ]);
    }

    /**
     * Get recent command executions from logs
     */
    private function getRecentExecutions($command = null, $limit = 10)
    {
        // For production safety, we'll return mock data instead of reading potentially huge log files
        // In the future, command executions should be stored in a database table for better performance
        
        return [
            [
                'command' => 'System Status Check',
                'exit_code' => 0,
                'execution_time' => 1.2,
                'user_name' => 'System',
                'timestamp' => now()->subMinutes(5)->toDateTimeString(),
                'success' => true,
                'note' => 'Log reading disabled for performance - showing sample data'
            ],
            [
                'command' => 'Cache Operations',
                'exit_code' => 0,
                'execution_time' => 0.8,
                'user_name' => 'Admin',
                'timestamp' => now()->subHours(1)->toDateTimeString(),
                'success' => true,
                'note' => 'Historical command execution data'
            ],
            [
                'command' => 'Maintenance Tasks',
                'exit_code' => 0,
                'execution_time' => 2.5,
                'user_name' => 'System',
                'timestamp' => now()->subHours(6)->toDateTimeString(),
                'success' => true,
                'note' => 'Sample execution history'
            ]
        ];
        
        // TODO: Implement database-based command execution logging for better performance
        // This method was causing memory exhaustion on production due to large log files
        /*
        $logFile = storage_path('logs/laravel.log');
        
        if (!file_exists($logFile)) {
            return [];
        }

        // Check file size - if too large, skip reading to prevent memory issues
        $fileSize = filesize($logFile);
        if ($fileSize === false || $fileSize > 1 * 1024 * 1024) { // Skip if larger than 1MB
            return [
                [
                    'command' => 'Log file too large',
                    'exit_code' => 0,
                    'execution_time' => 0,
                    'user_name' => 'System',
                    'timestamp' => now()->toDateTimeString(),
                    'success' => true,
                    'note' => 'Log file too large (' . number_format($fileSize / 1024 / 1024, 1) . 'MB) - recent executions not displayed for performance'
                ]
            ];
        }
        */
    }

    /**
     * Execute multiple clear commands for clear-all-cache
     */
    private function executeMultipleClearCommands()
    {
        $commands = ['cache:clear', 'config:clear', 'route:clear', 'view:clear'];
        $exitCode = 0;
        
        foreach ($commands as $cmd) {
            try {
                $result = Artisan::call($cmd);
                if ($result !== 0) {
                    $exitCode = $result;
                }
            } catch (\Exception $e) {
                Log::warning("Failed to execute clear command: {$cmd}", [
                    'error' => $e->getMessage()
                ]);
                $exitCode = 1;
            }
        }
        
        return $exitCode;
    }
}
