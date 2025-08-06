<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - EXACT PRODUCTION STRUCTURE
     * This migration recreates ALL tables with EXACT production column names, types, and constraints
     */
    public function up(): void
    {
        // Disable foreign key checks to allow table recreation
        Schema::disableForeignKeyConstraints();
        
        // Drop existing problematic tables first
        Schema::dropIfExists('general_settings');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('admin_notifications');
        Schema::dropIfExists('admin_trans_receives');
        Schema::dropIfExists('popups');
        Schema::dropIfExists('kyc_verifications');
        
        // EXACT PRODUCTION: general_settings table (85 columns)
        Schema::create('general_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('site_name', 191)->default('PayPerViews')->nullable(false);
            $table->string('cur_text', 191)->default('USD')->nullable(false);
            $table->string('cur_sym', 191)->default('$')->nullable(false);
            $table->string('email_from', 191)->nullable();
            $table->text('email_template')->nullable();
            $table->text('sms_body')->nullable();
            $table->string('sms_from', 191)->nullable();
            $table->string('base_color', 191)->default('#007bff')->nullable(false);
            $table->string('secondary_color', 191)->default('#6c757d')->nullable(false);
            $table->text('logo')->nullable();
            $table->string('loader_image', 191)->nullable();
            $table->string('admin_logo', 191)->nullable();
            $table->text('header_content')->nullable();
            $table->json('header_scripts')->nullable();
            $table->string('header_background_color', 191)->default('#ffffff')->nullable(false);
            $table->string('header_text_color', 191)->default('#000000')->nullable(false);
            $table->text('footer_content')->nullable();
            $table->json('footer_scripts')->nullable();
            $table->string('footer_background_color', 191)->default('#343a40')->nullable(false);
            $table->string('footer_text_color', 191)->default('#ffffff')->nullable(false);
            $table->text('copyright_text')->nullable();
            $table->string('meta_title', 191)->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('meta_image')->nullable();
            $table->json('social_media_links')->nullable();
            $table->string('contact_email', 191)->nullable();
            $table->string('contact_phone', 191)->nullable();
            $table->text('contact_address')->nullable();
            $table->text('home_page_content')->nullable();
            $table->text('about_us_content')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->text('privacy_policy')->nullable();
            $table->longText('custom_css')->nullable();
            $table->longText('custom_js')->nullable();
            $table->json('notification_settings')->nullable();
            $table->json('theme_settings')->nullable();
            $table->json('widget_settings')->nullable();
            $table->text('maintenance_message')->nullable();
            $table->string('maintenance_image', 191)->nullable();
            $table->json('api_settings')->nullable();
            $table->string('timezone', 191)->default('UTC')->nullable(false);
            $table->string('date_format', 191)->default('Y-m-d')->nullable(false);
            $table->string('time_format', 191)->default('H:i:s')->nullable(false);
            $table->json('file_upload_settings')->nullable();
            $table->json('security_settings')->nullable();
            $table->json('transfer_conditions')->nullable();
            $table->json('withdrawal_conditions')->nullable();
            $table->json('referral_benefits_settings')->nullable();
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
            $table->boolean('force_ssl')->default(0)->nullable(false);
            $table->boolean('maintenance_mode')->default(0)->nullable(false);
            $table->boolean('secure_password')->default(0)->nullable(false);
            $table->boolean('agree')->default(0)->nullable(false);
            $table->boolean('registration')->default(1)->nullable(false);
            $table->string('active_template', 191)->default('basic')->nullable(false);
            $table->json('system_info')->nullable();
            $table->decimal('deposit_commission', 8, 2)->default(0.00)->nullable(false);
            $table->decimal('investment_commission', 8, 2)->default(0.00)->nullable(false);
            $table->decimal('invest_return_commission', 8, 2)->default(0.00)->nullable(false);
            $table->decimal('signup_bonus_amount', 8, 2)->default(0.00)->nullable(false);
            $table->text('signup_bonus_control')->nullable();
            $table->text('promotional_tool')->nullable();
            $table->json('firebase_config')->nullable();
            $table->json('firebase_template')->nullable();
            $table->boolean('push_notify')->default(0)->nullable(false);
            $table->text('off_day')->nullable();
            $table->timestamp('last_cron')->nullable();
            $table->boolean('b_transfer')->default(0)->nullable(false);
            $table->decimal('f_charge', 8, 2)->default(0.00)->nullable(false);
            $table->decimal('p_charge', 8, 2)->default(0.00)->nullable(false);
            $table->boolean('holiday_withdraw')->default(0)->nullable(false);
            $table->boolean('language_switch')->default(0)->nullable(false);
            $table->timestamps();
        });

        // EXACT PRODUCTION: admins table (29 columns)
        Schema::create('admins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 191)->nullable();
            $table->string('email', 40)->nullable(false);
            $table->string('username', 40)->nullable(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('image', 255)->nullable();
            $table->string('password', 255)->nullable(false);
            $table->string('remember_token', 255)->nullable();
            $table->decimal('balance', 15, 2)->default(0.00)->nullable(false);
            $table->decimal('total_deposited', 15, 2)->default(0.00)->nullable(false);
            $table->decimal('total_withdrawn', 15, 2)->default(0.00)->nullable(false);
            $table->decimal('total_transferred', 15, 2)->default(0.00)->nullable(false);
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('role', 20)->default('admin')->nullable(false);
            $table->json('permissions')->nullable();
            $table->boolean('is_active')->default(1)->nullable(false);
            $table->boolean('is_super_admin')->default(0)->nullable(false);
            $table->string('two_factor_secret', 255)->nullable();
            $table->string('two_factor_recovery_codes', 255)->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->string('last_login_user_agent', 255)->nullable();
            $table->integer('login_attempts')->default(0)->nullable(false);
            $table->timestamp('locked_until')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // EXACT PRODUCTION: admin_notifications table (17 columns)
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('admin_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('title', 255)->nullable();
            $table->text('message')->nullable();
            $table->enum('type', ['info', 'success', 'warning', 'danger', 'primary'])->default('info')->nullable(false);
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal')->nullable(false);
            $table->string('icon', 191)->default('fas fa-bell')->nullable(false);
            $table->boolean('read')->default(0)->nullable(false);
            $table->timestamp('read_at')->nullable();
            $table->string('action_url', 191)->nullable();
            $table->string('action_text', 191)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->text('click_url')->nullable();
            $table->timestamps();
        });

        // EXACT PRODUCTION: admin_trans_receives table (9 columns)
        Schema::create('admin_trans_receives', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('admin_id')->unsigned()->nullable(false);
            $table->string('user_transfer', 255)->nullable(false);
            $table->integer('amount')->nullable(false);
            $table->boolean('status')->default(0)->nullable(false)->comment('0 = pending, 1 = active, 2 = suspended');
            $table->string('user_receive', 255)->nullable(false);
            $table->string('note', 191)->nullable();
            $table->timestamps();
        });

        // EXACT PRODUCTION: popups table (34 columns)
        Schema::create('popups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 191)->nullable(false);
            $table->text('content')->nullable();
            $table->string('type', 191)->default('announcement')->nullable(false);
            $table->string('display_type', 191)->default('text')->nullable(false);
            $table->string('image', 191)->nullable();
            $table->string('button_text', 191)->default('Close')->nullable(false);
            $table->string('button_url', 191)->nullable();
            $table->string('button_color', 191)->default('#007bff')->nullable(false);
            $table->string('background_color', 191)->default('#ffffff')->nullable(false);
            $table->string('text_color', 191)->default('#333333')->nullable(false);
            $table->string('overlay_color', 191)->default('rgba(0,0,0,0.5)')->nullable(false);
            $table->enum('size', ['small', 'medium', 'large', 'fullscreen'])->default('medium')->nullable(false);
            $table->enum('position', ['center', 'top', 'bottom', 'left', 'right'])->default('center')->nullable(false);
            $table->enum('animation', ['fade', 'slide-up', 'slide-down', 'zoom', 'bounce'])->default('fade')->nullable(false);
            $table->integer('delay')->default(0)->nullable(false);
            $table->integer('auto_close')->nullable();
            $table->boolean('closable')->default(1)->nullable(false);
            $table->boolean('backdrop_close')->default(1)->nullable(false);
            $table->enum('frequency', ['once', 'daily', 'session', 'always'])->default('session')->nullable(false);
            $table->json('target_users')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->boolean('is_active')->default(1)->nullable(false);
            $table->integer('priority')->default(1)->nullable(false);
            $table->integer('view_count')->default(0)->nullable(false);
            $table->integer('click_count')->default(0)->nullable(false);
            $table->json('custom_css')->nullable();
            $table->json('custom_js')->nullable();
            $table->boolean('show_on_mobile')->default(1)->nullable(false);
            $table->boolean('show_on_desktop')->default(1)->nullable(false);
            $table->string('pages', 191)->nullable();
            $table->timestamps();
        });

        // EXACT PRODUCTION: kyc_verifications table (25 columns)
        Schema::create('kyc_verifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->nullable(false);
            $table->date('date_of_birth')->nullable();
            $table->enum('document_type', ['passport', 'national_id', 'driving_license'])->nullable(false);
            $table->string('document_number', 50)->nullable(false);
            $table->string('document_front', 191)->nullable(false);
            $table->string('document_back', 191)->nullable();
            $table->string('selfie_image', 191)->nullable(false);
            $table->string('nationality', 100)->nullable(false);
            $table->text('address')->nullable(false);
            $table->string('city', 100)->nullable(false);
            $table->string('state', 100)->nullable(false);
            $table->string('postal_code', 20)->nullable(false);
            $table->string('country', 100)->nullable(false);
            $table->string('phone_number', 20)->nullable(false);
            $table->enum('status', ['pending', 'approved', 'rejected', 'under_review'])->default('pending')->nullable(false);
            $table->text('admin_remarks')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->bigInteger('reviewed_by')->unsigned()->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('under_review_at')->nullable();
            $table->timestamps();
        });
        
        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kyc_verifications');
        Schema::dropIfExists('popups');
        Schema::dropIfExists('admin_trans_receives');
        Schema::dropIfExists('admin_notifications');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('general_settings');
    }
};
