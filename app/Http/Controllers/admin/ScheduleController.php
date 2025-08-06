<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    /**
     * Display schedule management dashboard
     */
    public function index()
    {
        try {
            // Get queue statistics
            $pendingJobs = DB::table('jobs')->count();
            $failedJobs = DB::table('failed_jobs')->count();
            
            // Get recent scheduled tasks (from logs if available)
            $recentTasks = $this->getRecentScheduledTasks();
            
            // Get cron job status
            $cronStatus = $this->getCronJobStatus();
            
            return view('admin.schedule.index', compact(
                'pendingJobs',
                'failedJobs', 
                'recentTasks',
                'cronStatus'
            ));
            
        } catch (\Exception $e) {
            Log::error('Schedule dashboard error: ' . $e->getMessage());
            
            return view('admin.schedule.index', [
                'pendingJobs' => 0,
                'failedJobs' => 0,
                'recentTasks' => [],
                'cronStatus' => 'unknown',
                'error' => 'Unable to load schedule data: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Manually run the Laravel scheduler
     */
    public function runSchedule(Request $request)
    {
        try {
            $startTime = microtime(true);
            $startMemory = memory_get_usage(true);
            
            // Run the scheduler
            Log::info('Manual schedule run initiated by admin');
            
            // Capture output from schedule:run
            $exitCode = Artisan::call('schedule:run');
            $output = Artisan::output();
            
            $endTime = microtime(true);
            $endMemory = memory_get_usage(true);
            
            $duration = round(($endTime - $startTime) * 1000, 2) . 'ms';
            $memoryUsed = $this->formatBytes($endMemory - $startMemory);
            
            // Parse output to count tasks
            $tasksRun = substr_count($output, 'Running scheduled command:');
            
            Log::info('Manual schedule run completed', [
                'exit_code' => $exitCode,
                'tasks_run' => $tasksRun,
                'duration' => $duration,
                'memory_used' => $memoryUsed,
                'output' => $output
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Schedule executed successfully',
                'tasks_run' => $tasksRun,
                'duration' => $duration,
                'memory_used' => $memoryUsed,
                'exit_code' => $exitCode,
                'output' => $output
            ]);
            
        } catch (\Exception $e) {
            Log::error('Manual schedule run failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to run schedule: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get recent scheduled tasks from application logs
     */
    private function getRecentScheduledTasks()
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            
            if (!file_exists($logFile)) {
                return [];
            }
            
            // Read last 100 lines for recent tasks
            $lines = array_slice(file($logFile), -100);
            $tasks = [];
            
            foreach ($lines as $line) {
                if (strpos($line, 'schedule:run') !== false || strpos($line, 'Running scheduled command') !== false) {
                    $tasks[] = [
                        'timestamp' => $this->extractTimestamp($line),
                        'message' => trim($line),
                        'status' => strpos($line, 'ERROR') !== false ? 'failed' : 'completed'
                    ];
                }
            }
            
            return array_reverse(array_slice($tasks, -10)); // Last 10 tasks
            
        } catch (\Exception $e) {
            Log::error('Error reading scheduled tasks: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Check if cron jobs are properly configured
     */
    private function getCronJobStatus()
    {
        try {
            // Check if we can determine cron status
            $lastScheduleRun = cache('last_schedule_run');
            
            if (!$lastScheduleRun) {
                return 'unknown';
            }
            
            $timeSinceLastRun = now()->diffInMinutes($lastScheduleRun);
            
            if ($timeSinceLastRun <= 2) {
                return 'active';
            } elseif ($timeSinceLastRun <= 10) {
                return 'delayed';
            } else {
                return 'inactive';
            }
            
        } catch (\Exception $e) {
            return 'unknown';
        }
    }
    
    /**
     * Extract timestamp from log line
     */
    private function extractTimestamp($line)
    {
        $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/';
        
        if (preg_match($pattern, $line, $matches)) {
            return $matches[1];
        }
        
        return date('Y-m-d H:i:s');
    }
    
    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
