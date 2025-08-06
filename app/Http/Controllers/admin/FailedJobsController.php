<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FailedJobsController extends Controller
{
    /**
     * Display failed jobs management
     */
    public function index()
    {
        try {
            // Get failed jobs with pagination
            $failedJobs = DB::table('failed_jobs')
                ->orderBy('failed_at', 'desc')
                ->paginate(20);
                
            // Parse and enhance job data
            $failedJobs->getCollection()->transform(function ($job) {
                $payload = json_decode($job->payload, true);
                $exception = json_decode($job->exception, true);
                
                return (object) [
                    'id' => $job->id,
                    'uuid' => $job->uuid,
                    'connection' => $job->connection,
                    'queue' => $job->queue,
                    'payload' => $payload,
                    'exception' => $exception,
                    'failed_at' => $job->failed_at,
                    'job_name' => isset($payload['displayName']) ? $payload['displayName'] : 'Unknown Job',
                    'error_message' => $this->extractErrorMessage($job->exception)
                ];
            });
            
            // Get statistics
            $stats = [
                'total_failed' => DB::table('failed_jobs')->count(),
                'today_failed' => DB::table('failed_jobs')
                    ->whereDate('failed_at', today())
                    ->count(),
                'week_failed' => DB::table('failed_jobs')
                    ->where('failed_at', '>=', now()->startOfWeek())
                    ->count()
            ];
            
            return view('admin.failed-jobs.index', compact('failedJobs', 'stats'));
            
        } catch (\Exception $e) {
            Log::error('Failed jobs dashboard error: ' . $e->getMessage());
            
            return view('admin.failed-jobs.index', [
                'failedJobs' => collect([]),
                'stats' => ['total_failed' => 0, 'today_failed' => 0, 'week_failed' => 0],
                'error' => 'Unable to load failed jobs: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Retry a specific failed job
     */
    public function retry($id)
    {
        try {
            $job = DB::table('failed_jobs')->where('id', $id)->first();
            
            if (!$job) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed job not found'
                ], 404);
            }
            
            // Use Artisan command to retry the job
            $exitCode = Artisan::call('queue:retry', ['id' => $job->uuid]);
            
            if ($exitCode === 0) {
                Log::info('Failed job retried successfully', ['job_id' => $id, 'uuid' => $job->uuid]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Job retried successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to retry job'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Error retrying failed job: ' . $e->getMessage(), ['job_id' => $id]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrying job: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete a specific failed job
     */
    public function delete($id)
    {
        try {
            $job = DB::table('failed_jobs')->where('id', $id)->first();
            
            if (!$job) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed job not found'
                ], 404);
            }
            
            // Use Artisan command to forget the job
            $exitCode = Artisan::call('queue:forget', ['id' => $job->uuid]);
            
            if ($exitCode === 0) {
                Log::info('Failed job deleted successfully', ['job_id' => $id, 'uuid' => $job->uuid]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Job deleted successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete job'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Error deleting failed job: ' . $e->getMessage(), ['job_id' => $id]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting job: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Retry all failed jobs
     */
    public function retryAll()
    {
        try {
            $failedCount = DB::table('failed_jobs')->count();
            
            if ($failedCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No failed jobs to retry'
                ]);
            }
            
            // Use Artisan command to retry all failed jobs
            $exitCode = Artisan::call('queue:retry', ['id' => 'all']);
            
            if ($exitCode === 0) {
                Log::info('All failed jobs retried successfully', ['count' => $failedCount]);
                
                return response()->json([
                    'success' => true,
                    'message' => "Successfully retried $failedCount failed jobs"
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to retry all jobs'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Error retrying all failed jobs: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrying all jobs: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Clear all failed jobs
     */
    public function clearAll()
    {
        try {
            $failedCount = DB::table('failed_jobs')->count();
            
            if ($failedCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No failed jobs to clear'
                ]);
            }
            
            // Use Artisan command to flush all failed jobs
            $exitCode = Artisan::call('queue:flush');
            
            if ($exitCode === 0) {
                Log::info('All failed jobs cleared successfully', ['count' => $failedCount]);
                
                return response()->json([
                    'success' => true,
                    'message' => "Successfully cleared $failedCount failed jobs"
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to clear all jobs'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Error clearing all failed jobs: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error clearing all jobs: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Extract readable error message from exception JSON
     */
    private function extractErrorMessage($exceptionJson)
    {
        try {
            $exception = json_decode($exceptionJson, true);
            
            if (isset($exception['message'])) {
                return $exception['message'];
            }
            
            // Try to extract from the raw exception string
            if (is_string($exceptionJson)) {
                $lines = explode("\n", $exceptionJson);
                foreach ($lines as $line) {
                    if (strpos($line, 'Exception:') !== false || strpos($line, 'Error:') !== false) {
                        return trim($line);
                    }
                }
                // Return first line if no specific error line found
                return isset($lines[0]) ? trim($lines[0]) : 'Unknown error';
            }
            
            return 'Unknown error occurred';
            
        } catch (\Exception $e) {
            return 'Error parsing exception message';
        }
    }
}
