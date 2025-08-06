<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GeneralSetting;

class RefreshMailConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:refresh-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh mail configuration from database settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Refreshing mail configuration...');
        
        try {
            GeneralSetting::refreshMailConfiguration();
            $this->info('✅ Mail configuration refreshed successfully!');
            
            // Display current mail config status
            $status = GeneralSetting::getMailConfigStatus();
            $this->line('');
            $this->line('Mail Configuration Status:');
            $this->line('- Configured: ' . ($status['configured'] ? '✅ Yes' : '❌ No'));
            $this->line('- Host: ' . ($status['host'] ? '✅ Set' : '❌ Not set'));
            $this->line('- Username: ' . ($status['username'] ? '✅ Set' : '❌ Not set'));
            $this->line('- Password: ' . ($status['password'] ? '✅ Set' : '❌ Not set'));
            $this->line('- From Address: ' . ($status['from_address'] ? '✅ Set' : '❌ Not set'));
            $this->line('- From Name: ' . ($status['from_name'] ? '✅ Set' : '❌ Not set'));
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to refresh mail configuration: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
