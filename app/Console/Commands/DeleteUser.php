<?php

namespace App\Console\Commands;

use App\Http\Controllers\CronController;
use App\Models\User;
use Illuminate\Console\Command;

class DeleteUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $controller = new CronController();
        $controller->userupdate();  
        $this->info('User data updated successfully.');
    }
}
