<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanExpiredOrders extends Command
{
    protected $signature = 'orders:clean-expired';
    protected $description = 'Clean up expired orders that have not been paid';

    public function handle()
    {
        DB::beginTransaction();
        try {
            $count = \App\Models\Payment::expireOverdue();
            $this->info("Marked {$count} pending payments as expired.");
            Log::info("Expired orders cleanup completed: {$count} payments marked as expired");

            DB::commit();
            $this->info("Expired orders cleanup completed successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error cleaning up expired orders: {$e->getMessage()}");
            Log::error("Error cleaning up expired orders: {$e->getMessage()}");
        }
    }
}
