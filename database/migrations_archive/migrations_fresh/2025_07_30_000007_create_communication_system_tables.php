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
        // Support tickets table
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ticket', 40)->unique();
            $table->string('subject', 255);
            $table->enum('status', ['open', 'answered', 'replied', 'closed'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->foreignId('last_reply')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamp('last_reply_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['ticket']);
            $table->index(['status', 'priority']);
        });

        // Support messages table
        Schema::create('support_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained()->onDelete('set null');
            $table->text('message');
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->index(['support_ticket_id', 'created_at']);
            $table->index(['admin_id']);
        });

        // Messages table (general messaging system)
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained()->onDelete('set null');
            $table->string('subject', 255);
            $table->text('message');
            $table->enum('type', ['user_to_admin', 'admin_to_user', 'system', 'support'])->default('user_to_admin');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->json('attachments')->nullable();
            $table->foreignId('ticket_id')->nullable()->constrained('support_tickets')->onDelete('set null');
            $table->enum('sender_type', ['user', 'admin', 'system'])->default('user');
            $table->timestamps();

            $table->index(['user_id', 'is_read']);
            $table->index(['admin_id', 'type']);
            $table->index(['type', 'priority']);
            $table->index(['ticket_id']);
        });

        // Subscribers table (newsletter/email subscriptions)
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamps();

            $table->index(['email']);
        });

        // Newsletter subscribers table
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('subscribed_at');
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('subscription_source', 50)->default('website');
            $table->json('preferences')->nullable();
            $table->timestamps();

            $table->index(['email', 'is_active']);
            $table->index(['subscribed_at']);
        });

        // Forms table (dynamic form management)
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->string('act', 100);
            $table->text('form_data');
            $table->timestamps();

            $table->index(['act']);
        });

        // Holidays table
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->date('date');
            $table->text('description')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['date', 'is_active']);
            $table->index(['is_recurring']);
        });

        // General settings table
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name', 40)->nullable();
            $table->string('cur_text', 40)->nullable();
            $table->string('cur_sym', 40)->nullable();
            $table->string('email_from', 40)->nullable();
            $table->text('email_template')->nullable();
            $table->text('sms_body')->nullable();
            $table->string('sms_from', 40)->nullable();
            $table->text('base_color')->nullable();
            $table->text('mail_config')->nullable();
            $table->text('sms_config')->nullable();
            $table->text('global_shortcodes')->nullable();
            $table->boolean('ev')->default(false)->comment('email verification');
            $table->boolean('en')->default(false)->comment('email notification');
            $table->boolean('sv')->default(false)->comment('sms verification');
            $table->boolean('sn')->default(false)->comment('sms notification');
            $table->boolean('force_ssl')->default(false);
            $table->boolean('secure_password')->default(false);
            $table->boolean('registration')->default(false)->comment('registration ON/OFF');
            $table->boolean('agree')->default(false);
            $table->boolean('multi_language')->default(true);
            $table->string('last_cron', 40)->nullable();
            $table->text('system_info')->nullable();
            $table->boolean('module')->default(false);
            $table->boolean('deposit')->default(false);
            $table->boolean('withdraw')->default(false);
            $table->boolean('transfer')->default(false);
            $table->string('transfer_limit', 40)->nullable();
            $table->string('withdraw_limit', 40)->nullable();
            $table->decimal('min_transfer_amount', 28, 8)->default(0.00000000);
            $table->decimal('max_transfer_amount', 28, 8)->default(0.00000000);
            $table->decimal('transfer_charge_percentage', 5, 2)->default(0.00);
            $table->decimal('transfer_charge_fixed', 28, 8)->default(0.00000000);
            $table->decimal('min_withdraw_amount', 28, 8)->default(0.00000000);
            $table->decimal('max_withdraw_amount', 28, 8)->default(0.00000000);
            $table->decimal('withdraw_charge_percentage', 5, 2)->default(0.00);
            $table->decimal('withdraw_charge_fixed', 28, 8)->default(0.00000000);
            $table->json('referral_benefits')->nullable();
            $table->timestamps();
        });

        // Configuration changes table (for tracking system configuration changes)
        Schema::create('configuration_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained()->onDelete('set null');
            $table->string('configuration_key', 255);
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('change_type', 50)->default('update');
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();

            $table->index(['admin_id', 'created_at']);
            $table->index(['configuration_key']);
            $table->index(['change_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuration_changes');
        Schema::dropIfExists('general_settings');
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('forms');
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('subscribers');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('support_messages');
        Schema::dropIfExists('support_tickets');
    }
};
