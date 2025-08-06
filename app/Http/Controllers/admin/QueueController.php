<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;

class QueueController extends Controller
{
    /**
     * Display queue management dashboard
     */
    public function index()
    {
        try {
            // Get queue statistics
            $pendingJobs = DB::table('jobs')->count();
            $failedJobs = DB::table('failed_jobs')->count();
            $processedToday = $this->getProcessedJobsToday();
            
            // Get recent jobs
            $recentJobs = $this->getRecentJobs();
            
            // Get worker status
            $workerStatus = $this->checkWorkerStatus();
            
            return view('admin.queue.index', compact(
                'pendingJobs',
                'failedJobs',
                'processedToday',
                'recentJobs',
                'workerStatus'
            ));
            
        } catch (\Exception $e) {
            Log::error('Queue dashboard error: ' . $e->getMessage());
            
            return view('admin.queue.index', [
                'pendingJobs' => 0,
                'failedJobs' => 0,
                'processedToday' => 0,
                'recentJobs' => [],
                'workerStatus' => ['active' => false, 'workers' => []],
                'error' => 'Unable to load queue data: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get queue worker status
     */
    public function workerStatus()
    {
        try {
            $status = $this->checkWorkerStatus();
            $pendingJobs = DB::table('jobs')->count();
            $failedJobs = DB::table('failed_jobs')->count();
            $processedToday = $this->getProcessedJobsToday();
            
            return response()->json([
                'workers' => $status['workers'],
                'active' => $status['active'],
                'pending_jobs' => $pendingJobs,
                'failed_jobs' => $failedJobs,
                'processed_today' => $processedToday
            ]);
            
        } catch (\Exception $e) {
            Log::error('Worker status check failed: ' . $e->getMessage());
            
            return response()->json([
                'workers' => [],
                'active' => false,
                'pending_jobs' => 0,
                'failed_jobs' => 0,
                'processed_today' => 0,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Start queue worker
     */
    public function startWorker(Request $request)
    {
        try {
            // Check if workers are already running
            $status = $this->checkWorkerStatus();
            
            if ($status['active'] && count($status['workers']) > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Queue workers are already running (' . count($status['workers']) . ' active)'
                ]);
            }
            
            // Start queue worker in background
            if (PHP_OS_FAMILY === 'Windows') {
                // Windows command
                $command = 'start /B php artisan queue:work --tries=3 --timeout=60';
                pclose(popen($command, 'r'));
            } else {
                // Unix/Linux command
                $command = 'nohup php artisan queue:work --tries=3 --timeout=60 > /dev/null 2>&1 &';
                exec($command);
            }
            
            Log::info('Queue worker start initiated via admin panel');
            
            // Wait a moment for worker to start
            sleep(2);
            
            // Check if worker started successfully
            $newStatus = $this->checkWorkerStatus();
            
            if ($newStatus['active']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Queue worker started successfully',
                    'workers' => count($newStatus['workers'])
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Worker start command executed but status unclear. Check manually.'
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to start queue worker: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to start queue worker: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get queue counts for menu badges
     */
    public function getCounts()
    {
        try {
            $pending = DB::table('jobs')->count();
            $failed = DB::table('failed_jobs')->count();
            
            return response()->json([
                'pending' => $pending,
                'failed' => $failed
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'pending' => 0,
                'failed' => 0,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Check if queue workers are running
     */
    private function checkWorkerStatus()
    {
        try {
            $workers = [];
            $active = false;
            
            if (PHP_OS_FAMILY === 'Windows') {
                // Windows: Check for PHP processes running queue:work
                $output = shell_exec('wmic process where "name=\'php.exe\'" get CommandLine,ProcessId /format:csv 2>nul');
                
                if ($output) {
                    $lines = explode("\n", $output);
                    foreach ($lines as $line) {
                        if (strpos($line, 'queue:work') !== false) {
                            $parts = explode(',', $line);
                            if (count($parts) >= 3) {
                                $workers[] = [
                                    'pid' => trim($parts[2]),
                                    'command' => trim($parts[1])
                                ];
                            }
                        }
                    }
                }
            } else {
                // Unix/Linux: Use ps command
                $output = shell_exec('ps aux | grep "queue:work" | grep -v grep');
                
                if ($output) {
                    $lines = explode("\n", trim($output));
                    foreach ($lines as $line) {
                        if (!empty($line)) {
                            $parts = preg_split('/\s+/', $line, 11);
                            if (count($parts) >= 2) {
                                $workers[] = [
                                    'pid' => $parts[1],
                                    'user' => $parts[0],
                                    'command' => isset($parts[10]) ? $parts[10] : 'queue:work'
                                ];
                            }
                        }
                    }
                }
            }
            
            $active = count($workers) > 0;
            
            return [
                'active' => $active,
                'workers' => $workers
            ];
            
        } catch (\Exception $e) {
            Log::error('Error checking worker status: ' . $e->getMessage());
            
            return [
                'active' => false,
                'workers' => []
            ];
        }
    }
    
    /**
     * Get recent jobs from queue
     */
    private function getRecentJobs()
    {
        try {
            $jobs = DB::table('jobs')
                ->select('id', 'queue', 'payload', 'attempts', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
                
            return $jobs->map(function ($job) {
                $payload = json_decode($job->payload, true);
                $displayName = isset($payload['displayName']) ? $payload['displayName'] : 'Unknown Job';
                
                return [
                    'id' => $job->id,
                    'name' => $displayName,
                    'queue' => $job->queue ?: 'default',
                    'attempts' => $job->attempts,
                    'created_at' => $job->created_at
                ];
            });
            
        } catch (\Exception $e) {
            Log::error('Error getting recent jobs: ' . $e->getMessage());
            return collect([]);
        }
    }
    
    /**
     * Get count of jobs processed today
     */
    private function getProcessedJobsToday()
    {
        try {
            // This is an approximation since Laravel doesn't track completed jobs by default
            // You might want to implement job tracking in your application
            
            $today = now()->startOfDay();
            
            // Check if there's a custom jobs_completed table
            if (DB::getSchemaBuilder()->hasTable('jobs_completed')) {
                return DB::table('jobs_completed')
                    ->where('completed_at', '>=', $today)
                    ->count();
            }
            
            // Fallback: estimate from logs or other sources
            return $this->estimateProcessedJobs();
            
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * Estimate processed jobs from available data
     */
    private function estimateProcessedJobs()
    {
        try {
            // Check Laravel logs for job completion patterns
            $logFile = storage_path('logs/laravel.log');
            
            if (!file_exists($logFile)) {
                return 0;
            }
            
            $today = now()->format('Y-m-d');
            $content = file_get_contents($logFile);
            
            // Count occurrences of job processing patterns for today
            $patterns = [
                "[$today.*] Processed:",
                "[$today.*] Processing:",
                "[$today.*] local.INFO: Job processed"
            ];
            
            $count = 0;
            foreach ($patterns as $pattern) {
                $count += substr_count($content, $pattern);
            }
            
            return $count;
            
        } catch (\Exception $e) {
            return 0;
        }
    }
}
