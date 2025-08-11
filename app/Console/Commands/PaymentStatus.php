<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CronController;

class PaymentStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:payment-status';

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
        try {
            $this->info('Checking payment status...');
            $cronJob = new CronController();
            $cronJob->paymentStatus();
            $this->info('Payment status checked successfully.');
            return 0; // Success
        } catch (\Exception $e) {
            $this->error('Payment status check failed: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error('PaymentStatus command failed: ' . $e->getMessage());
            return 1; // Failure
        }
    }
}
