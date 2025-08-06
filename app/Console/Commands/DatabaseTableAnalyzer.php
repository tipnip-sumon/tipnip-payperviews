<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class DatabaseTableAnalyzer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:analyze-tables {--fix : Automatically fix missing tables}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze database table differences and fix production issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Analyzing database table structure...');
        
        // Get all current tables
        $currentTables = $this->getCurrentTables();
        $this->info("📊 Current database has " . count($currentTables) . " tables");
        
        // List all current tables
        $this->info("\n📋 Current Tables:");
        foreach ($currentTables as $index => $table) {
            $this->line(($index + 1) . ". " . $table);
        }
        
        // Check for missing critical tables
        $this->checkCriticalTables();
        
        // Check migration status
        $this->checkMigrationStatus();
        
        if ($this->option('fix')) {
            $this->fixMissingTables();
        }
        
        return 0;
    }
    
    private function getCurrentTables()
    {
        try {
            $tables = DB::select('SHOW TABLES');
            $tableNames = [];
            
            foreach ($tables as $table) {
                $tableArray = (array) $table;
                $tableNames[] = array_values($tableArray)[0];
            }
            
            sort($tableNames);
            return $tableNames;
        } catch (\Exception $e) {
            $this->error("Error getting tables: " . $e->getMessage());
            return [];
        }
    }
    
    private function checkCriticalTables()
    {
        $this->info("\n🔍 Checking critical tables...");
        
        $criticalTables = [
            'users',
            'lottery_settings',
            'lottery_tickets',
            'lottery_draws',
            'lottery_winners',
            'special_lottery_tickets',
            'special_ticket_transfers',
            'deposits',
            'withdrawals',
            'transactions',
            'support_tickets',
            'support_messages',
            'video_links',
            'video_views',
            'plans',
            'invests',
            'referrals',
            'referral_commissions',
            'migrations'
        ];
        
        $missingTables = [];
        
        foreach ($criticalTables as $table) {
            if (Schema::hasTable($table)) {
                $this->info("✅ $table - EXISTS");
            } else {
                $this->error("❌ $table - MISSING");
                $missingTables[] = $table;
            }
        }
        
        if (!empty($missingTables)) {
            $this->warn("\n⚠️  Missing " . count($missingTables) . " critical tables:");
            foreach ($missingTables as $table) {
                $this->line("   - $table");
            }
        } else {
            $this->info("\n✅ All critical tables exist!");
        }
        
        return $missingTables;
    }
    
    private function checkMigrationStatus()
    {
        $this->info("\n🔍 Checking migration status...");
        
        try {
            $pendingMigrations = DB::table('migrations')
                ->selectRaw('COUNT(*) as total')
                ->first();
            
            $this->info("📊 Total migrations run: " . $pendingMigrations->total);
            
            // Get recent migrations
            $recentMigrations = DB::table('migrations')
                ->orderBy('batch', 'desc')
                ->limit(10)
                ->get();
            
            $this->info("\n📋 Recent migrations:");
            foreach ($recentMigrations as $migration) {
                $this->line("   Batch {$migration->batch}: {$migration->migration}");
            }
            
        } catch (\Exception $e) {
            $this->error("Error checking migrations: " . $e->getMessage());
        }
    }
    
    private function fixMissingTables()
    {
        $this->info("\n🔧 Starting automatic table fixes...");
        
        // Create special_ticket_transfers if missing
        if (!Schema::hasTable('special_ticket_transfers')) {
            $this->warn("Creating special_ticket_transfers table...");
            $this->createSpecialTicketTransfersTable();
            $this->info("✅ special_ticket_transfers table created!");
        }
        
        // Run any pending migrations
        $this->info("\n🔄 Running pending migrations...");
        $this->call('migrate', ['--force' => true]);
        
        // Initialize lottery settings if needed
        $this->checkAndFixLotterySettings();
        
        $this->info("\n🎉 Database fixes completed!");
    }
    
    private function createSpecialTicketTransfersTable()
    {
        Schema::create('special_ticket_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('special_ticket_id');
            $table->unsignedBigInteger('from_user_id');
            $table->unsignedBigInteger('to_user_id')->nullable();
            $table->decimal('transfer_amount', 10, 2)->default(0);
            $table->enum('transfer_type', ['gift', 'sale', 'trade'])->default('gift');
            $table->text('transfer_message')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled', 'expired'])->default('pending');
            $table->timestamp('transfer_requested_at')->nullable();
            $table->timestamp('transfer_completed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('transfer_code')->unique()->nullable();
            $table->boolean('requires_acceptance')->default(false);
            $table->timestamps();

            // Add foreign key constraints with checks
            if (Schema::hasTable('special_lottery_tickets')) {
                $table->foreign('special_ticket_id')->references('id')->on('special_lottery_tickets')->onDelete('cascade');
            }
            if (Schema::hasTable('users')) {
                $table->foreign('from_user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('to_user_id')->references('id')->on('users')->onDelete('cascade');
            }

            // Indexes
            $table->index(['from_user_id', 'status']);
            $table->index(['to_user_id', 'status']);
            $table->index(['status', 'expires_at']);
        });
    }
    
    private function checkAndFixLotterySettings()
    {
        try {
            $settingsCount = DB::table('lottery_settings')->count();
            if ($settingsCount == 0) {
                $this->warn("Initializing lottery settings...");
                \App\Models\LotterySetting::getDefaultSettings();
                $this->info("✅ Lottery settings initialized!");
            }
        } catch (\Exception $e) {
            $this->error("Error checking lottery settings: " . $e->getMessage());
        }
    }
}
