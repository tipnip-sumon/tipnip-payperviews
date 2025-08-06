<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void 
    {
        if(Schema::hasTable('general_settings')) {
            return; // Table already exists, no need to create it again
        }else {
            Schema::dropIfExists('general_settings'); // Ensure table is dropped if it exists
        }
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id(); 
            $table->string('site_name')->default('PayPerViews');
            $table->string('cur_text')->default('USD');
            $table->string('cur_sym')->default('$');
            $table->string('email_from')->nullable();
            $table->text('email_template')->nullable();
            $table->text('sms_body')->nullable();
            $table->string('sms_from')->nullable();
            $table->string('base_color')->default('#007bff');
            $table->string('secondary_color')->default('#6c757d');
            $table->text('logo')->nullable();
            $table->string('loader_image')->nullable();
            $table->string('admin_logo')->nullable();
            
            // Header Settings
            $table->text('header_content')->nullable();
            $table->json('header_scripts')->nullable();
            $table->string('header_background_color')->default('#ffffff');
            $table->string('header_text_color')->default('#000000');
            
            // Footer Settings
            $table->text('footer_content')->nullable();
            $table->json('footer_scripts')->nullable();
            $table->string('footer_background_color')->default('#343a40');
            $table->string('footer_text_color')->default('#ffffff');
            $table->text('copyright_text')->nullable();
            
            // SEO Settings
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('meta_image')->nullable();
            
            // Social Media Settings
            $table->json('social_media_links')->nullable();
            
            // Contact Information
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('contact_address')->nullable();
            
            // Additional Content Areas
            $table->text('home_page_content')->nullable();
            $table->text('about_us_content')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->text('privacy_policy')->nullable();
            
            // Custom CSS and JS
            $table->longText('custom_css')->nullable();
            $table->longText('custom_js')->nullable();
            
            // Notification Settings
            $table->json('notification_settings')->nullable();
            
            // Theme Settings
            $table->json('theme_settings')->nullable();
            
            // Widget Settings
            $table->json('widget_settings')->nullable();
            
            // Maintenance Page Content
            $table->text('maintenance_message')->nullable();
            $table->string('maintenance_image')->nullable();
            
            // API Settings
            $table->json('api_settings')->nullable();
            
            // Timezone and Localization
            $table->string('timezone')->default('UTC');
            $table->string('date_format')->default('Y-m-d');
            $table->string('time_format')->default('H:i:s');
            
            // File Upload Settings
            $table->json('file_upload_settings')->nullable();
            
            // Security Settings
            $table->json('security_settings')->nullable();
            $table->text('icon')->nullable();
            $table->text('favicon')->nullable();
            $table->json('mail_config')->nullable();
            $table->json('sms_config')->nullable();
            $table->json('global_shortcodes')->nullable();
            $table->json('kv')->nullable();
            $table->json('ev')->nullable();
            $table->json('en')->nullable();
            $table->json('sv')->nullable();
            $table->json('sn')->nullable();
            $table->boolean('force_ssl')->default(false);
            $table->boolean('maintenance_mode')->default(false);
            $table->boolean('secure_password')->default(false);
            $table->boolean('agree')->default(false);
            $table->boolean('registration')->default(true);
            $table->string('active_template')->default('basic');
            $table->json('system_info')->nullable();
            $table->decimal('deposit_commission', 8, 2)->default(0);
            $table->decimal('investment_commission', 8, 2)->default(0);
            $table->decimal('invest_return_commission', 8, 2)->default(0);
            $table->decimal('signup_bonus_amount', 8, 2)->default(0);
            $table->text('signup_bonus_control')->nullable();
            $table->text('promotional_tool')->nullable();
            $table->json('firebase_config')->nullable();
            $table->json('firebase_template')->nullable();
            $table->boolean('push_notify')->default(false);
            $table->text('off_day')->nullable();
            $table->timestamp('last_cron')->nullable();
            $table->boolean('b_transfer')->default(false);
            $table->decimal('f_charge', 8, 2)->default(0);
            $table->decimal('p_charge', 8, 2)->default(0);
            $table->boolean('holiday_withdraw')->default(false);
            $table->boolean('language_switch')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};
