<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LotterySetting;
use App\Models\User;

class SetVirtualUserId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lottery:set-virtual-user {user_id : The ID of the user to use for virtual tickets}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the virtual user ID for lottery virtual tickets';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        
        // Validate user ID is numeric
        if (!is_numeric($userId)) {
            $this->error('User ID must be a number');
            return 1;
        }
        
        $userId = (int) $userId;
        
        // Check if user exists
        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID {$userId} not found!");
            return 1;
        }
        
        // Display user information
        $this->info("User found:");
        $this->info("ID: {$user->id}");
        $this->info("Username: {$user->username}");
        $this->info("Email: {$user->email}");
        $this->info("Name: {$user->firstname} {$user->lastname}");
        $this->info("Status: " . ($user->status == 1 ? 'Active' : 'Inactive'));
        
        // Warn if user is active (recommended to use inactive users for virtual tickets)
        if ($user->status == 1) {
            $this->warn('WARNING: This user is currently active. It is recommended to use inactive users for virtual tickets to avoid confusion.');
            if (!$this->confirm('Do you want to continue anyway?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }
        
        // Update lottery settings
        try {
            LotterySetting::updateSettings(['virtual_user_id' => $userId]);
            $this->info("Successfully updated virtual user ID to {$userId}");
            $this->info("All future virtual lottery tickets will be associated with this user.");
        } catch (\Exception $e) {
            $this->error("Failed to update virtual user ID: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
