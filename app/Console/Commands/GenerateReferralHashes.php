<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateReferralHashes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referral:generate-hashes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate referral hashes for existing users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating referral hashes for existing users...');
        
        $users = User::whereNull('referral_hash')->get();
        
        if ($users->isEmpty()) {
            $this->info('No users found without referral hashes.');
            return;
        }
        
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();
        
        foreach ($users as $user) {
            $user->generateReferralHash();
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('Referral hashes generated for ' . $users->count() . ' users.');
    }
}
