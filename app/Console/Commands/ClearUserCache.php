<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClearUserCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-user {--force : Force clear all cache without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear user browser cache by updating cache version and clearing server cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');

        if (!$force) {
            if (!$this->confirm('This will clear all user browser cache and server cache. Continue?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Starting cache clearing process...');

        // Clear Laravel application cache
        $this->call('cache:clear');
        $this->call('view:clear');
        $this->call('config:clear');
        $this->call('route:clear');

        // Update cache version in environment
        $this->updateCacheVersion();

        // Clear sessions (optional - commented out to avoid logging out users)
        // $this->clearUserSessions();

        // Clear temporary files
        $this->clearTempFiles();

        // Update database cache indicators if needed
        $this->updateDatabaseCacheIndicators();

        $this->info('✅ Cache clearing completed successfully!');
        $this->info('ℹ️  Users will automatically get fresh cache on their next page load.');

        return 0;
    }

    /**
     * Update cache version to force browser cache clearing
     */
    private function updateCacheVersion()
    {
        $newVersion = time();
        
        try {
            // Read current .env file
            $envPath = base_path('.env');
            $envContent = file_get_contents($envPath);

            // Update or add APP_CACHE_VERSION
            if (preg_match('/^APP_CACHE_VERSION=.*/m', $envContent)) {
                $envContent = preg_replace(
                    '/^APP_CACHE_VERSION=.*/m',
                    "APP_CACHE_VERSION={$newVersion}",
                    $envContent
                );
            } else {
                $envContent .= "\nAPP_CACHE_VERSION={$newVersion}";
            }

            // Write back to .env file
            file_put_contents($envPath, $envContent);

            $this->info("✅ Cache version updated to: {$newVersion}");
        } catch (\Exception $e) {
            $this->error("❌ Failed to update cache version: " . $e->getMessage());
        }
    }

    /**
     * Clear user sessions (optional)
     */
    private function clearUserSessions()
    {
        try {
            if ($this->confirm('Do you want to clear all user sessions? (This will log out all users)')) {
                DB::table('sessions')->delete();
                $this->info('✅ User sessions cleared.');
            }
        } catch (\Exception $e) {
            $this->error("❌ Failed to clear sessions: " . $e->getMessage());
        }
    }

    /**
     * Clear temporary files
     */
    private function clearTempFiles()
    {
        try {
            // Clear storage/app/public cache if exists
            if (Storage::disk('public')->exists('cache')) {
                Storage::disk('public')->deleteDirectory('cache');
                $this->info('✅ Public cache directory cleared.');
            }

            // Clear storage/framework/cache
            $cacheDir = storage_path('framework/cache');
            if (is_dir($cacheDir)) {
                $files = glob($cacheDir . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
                $this->info('✅ Framework cache files cleared.');
            }

        } catch (\Exception $e) {
            $this->error("❌ Failed to clear temp files: " . $e->getMessage());
        }
    }

    /**
     * Update database cache indicators
     */
    private function updateDatabaseCacheIndicators()
    {
        try {
            // Update a settings table or create a cache indicator
            DB::table('general_settings')
                ->where('key', 'cache_version')
                ->updateOrInsert(
                    ['key' => 'cache_version'],
                    [
                        'value' => time(),
                        'updated_at' => now()
                    ]
                );

            $this->info('✅ Database cache indicators updated.');
        } catch (\Exception $e) {
            // Ignore if table doesn't exist
            $this->warn("⚠️  Could not update database cache indicators (table may not exist).");
        }
    }
}
