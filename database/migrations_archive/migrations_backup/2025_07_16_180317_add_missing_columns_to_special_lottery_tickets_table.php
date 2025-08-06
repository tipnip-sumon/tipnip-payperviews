<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('special_lottery_tickets', function (Blueprint $table) {
            // Check if columns exist before adding them
            $columns = DB::select("SHOW COLUMNS FROM special_lottery_tickets");
            $existingColumns = collect($columns)->pluck('Field')->toArray();
            
            // Only add user_id if it doesn't exist
            if (!in_array('user_id', $existingColumns)) {
                $table->unsignedBigInteger('user_id')->after('ticket_number');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
            
            // Only add is_valid_token if it doesn't exist
            if (!in_array('is_valid_token', $existingColumns)) {
                $table->boolean('is_valid_token')->default(true)->after('early_usage_bonus');
            }
            
            // Only add token_expires_at if it doesn't exist
            if (!in_array('token_expires_at', $existingColumns)) {
                $table->datetime('token_expires_at')->nullable()->after('is_valid_token');
            }
            
            // Only add transaction_reference if it doesn't exist
            if (!in_array('transaction_reference', $existingColumns)) {
                $table->string('transaction_reference')->nullable()->after('token_expires_at');
            }
            
            // Check if indexes exist before adding them
            $indexes = DB::select("SHOW INDEX FROM special_lottery_tickets");
            $existingIndexes = collect($indexes)->pluck('Key_name')->toArray();
            
            if (!in_array('special_lottery_tickets_current_owner_id_status_index', $existingIndexes)) {
                $table->index(['current_owner_id', 'status']);
            }
            
            if (!in_array('special_lottery_tickets_status_is_valid_token_index', $existingIndexes)) {
                $table->index(['status', 'is_valid_token']);
            }
            
            if (!in_array('special_lottery_tickets_token_expires_at_index', $existingIndexes)) {
                $table->index('token_expires_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('special_lottery_tickets', function (Blueprint $table) {
            // Check if indexes exist before dropping them
            $indexes = DB::select("SHOW INDEX FROM special_lottery_tickets");
            $existingIndexes = collect($indexes)->pluck('Key_name')->toArray();
            
            if (in_array('special_lottery_tickets_current_owner_id_status_index', $existingIndexes)) {
                $table->dropIndex(['current_owner_id', 'status']);
            }
            
            if (in_array('special_lottery_tickets_status_is_valid_token_index', $existingIndexes)) {
                $table->dropIndex(['status', 'is_valid_token']);
            }
            
            if (in_array('special_lottery_tickets_token_expires_at_index', $existingIndexes)) {
                $table->dropIndex(['token_expires_at']);
            }
            
            // Check if foreign key exists before dropping
            $foreignKeys = collect($indexes)->where('Key_name', '!=', 'PRIMARY')->pluck('Key_name')->toArray();
            if (in_array('special_lottery_tickets_user_id_foreign', $foreignKeys)) {
                $table->dropForeign(['user_id']);
            }
            
            // Check if columns exist before dropping them
            $columns = DB::select("SHOW COLUMNS FROM special_lottery_tickets");
            $existingColumns = collect($columns)->pluck('Field')->toArray();
            
            $columnsToDrop = [];
            if (in_array('user_id', $existingColumns)) {
                $columnsToDrop[] = 'user_id';
            }
            if (in_array('is_valid_token', $existingColumns)) {
                $columnsToDrop[] = 'is_valid_token';
            }
            if (in_array('token_expires_at', $existingColumns)) {
                $columnsToDrop[] = 'token_expires_at';
            }
            if (in_array('transaction_reference', $existingColumns)) {
                $columnsToDrop[] = 'transaction_reference';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
