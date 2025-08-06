<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GeneralSetting;

class UpdateGeneralSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:update-nulls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update null values in general settings with defaults';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating general settings...');

        $settings = GeneralSetting::first();
        
        if (!$settings) {
            $this->error('No general settings found!');
            return 1;
        }

        $updated = $settings->update([
            'kv' => $settings->kv ?? false,
            'ev' => $settings->ev ?? false,
            'en' => $settings->en ?? true,
            'sv' => $settings->sv ?? false,
            'sn' => $settings->sn ?? false,
            'signup_bonus_control' => $settings->signup_bonus_control ?? false,
            'promotional_tool' => $settings->promotional_tool ?? false,
            'email_template' => $settings->email_template ?? 'Hello {{username}},

Welcome to {{site_name}}!

Your account has been created successfully. You can now start earning money by watching videos.

Best regards,
{{site_name}} Team',
            'sms_body' => $settings->sms_body ?? 'Hello {{username}}, Welcome to {{site_name}}! Start earning money by watching videos.',
            'sms_from' => $settings->sms_from ?? 'ViewCash',
        ]);

        if ($updated) {
            $this->info('General settings updated successfully!');
            $this->line('Updated fields with default values for null entries.');
        } else {
            $this->warn('No changes were needed.');
        }

        return 0;
    }
}
