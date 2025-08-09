<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ClearDeviceCache extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cache:clear-device {--force : Force clear all caches}';

    /**
     * The console command description.
     */
    protected $description = 'Clear device-specific caches to fix desktop/mobile layout issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Clearing device-specific caches...');

        try {
            // Clear Laravel caches
            $this->info('📱 Clearing Laravel cache...');
            Artisan::call('cache:clear');
            
            $this->info('🗂️ Clearing config cache...');
            Artisan::call('config:clear');
            
            $this->info('🛣️ Clearing route cache...');
            Artisan::call('route:clear');
            
            $this->info('👁️ Clearing view cache...');
            Artisan::call('view:clear');

            // Clear session data
            $this->info('🔑 Clearing session cache...');
            if (config('session.driver') === 'database') {
                DB::table('sessions')->delete();
                $this->line('  └─ Database sessions cleared');
            }

            // Clear specific device detection cookies/cache
            $this->info('📲 Clearing device detection cache...');
            Cache::forget('device_detection');
            Cache::forget('mobile_layout');
            Cache::forget('desktop_layout');

            // Clear browser cache files if they exist
            $this->info('🌐 Clearing browser cache files...');
            $publicCacheFiles = [
                'manifest.json',
                'sw.js',
                'font-sw.js'
            ];

            foreach ($publicCacheFiles as $file) {
                $filePath = public_path($file);
                if (file_exists($filePath)) {
                    // Touch file to update timestamp, forcing browser reload
                    touch($filePath);
                    $this->line("  └─ Updated {$file}");
                }
            }

            // Clear bootstrap cache files
            $this->info('🥾 Clearing bootstrap cache...');
            $bootstrapCache = storage_path('framework/cache');
            if (is_dir($bootstrapCache)) {
                $files = glob($bootstrapCache . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
                $this->line('  └─ Bootstrap cache files cleared');
            }

            // Force clear specific cache issues
            if ($this->option('force')) {
                $this->info('💥 Force clearing all caches...');
                
                // Clear all cache stores
                foreach (config('cache.stores') as $store => $config) {
                    try {
                        Cache::store($store)->clear();
                        $this->line("  └─ Cleared {$store} cache store");
                    } catch (\Exception $e) {
                        $this->warn("  └─ Could not clear {$store}: " . $e->getMessage());
                    }
                }

                // Clear OPCache if available
                if (function_exists('opcache_reset')) {
                    opcache_reset();
                    $this->line('  └─ OPCache cleared');
                }
            }

            $this->newLine();
            $this->info('✅ Device cache clearing completed successfully!');
            $this->newLine();
            $this->comment('💡 Recommendations:');
            $this->line('   • Ask users to hard refresh (Ctrl+F5 or Cmd+Shift+R)');
            $this->line('   • Users can manually clear cache using: window.clearPayPerViewsCache()');
            $this->line('   • Check service worker updates in browser DevTools');
            $this->newLine();

        } catch (\Exception $e) {
            $this->error('❌ Error clearing device cache: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
