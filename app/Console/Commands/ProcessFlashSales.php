<?php

namespace App\Console\Commands;

use App\Models\Promotion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ProcessFlashSales extends Command
{
    protected $signature = 'promotions:process-flash-sales';
    protected $description = 'Activate/deactivate flash sales based on schedule';

    public function handle()
    {
        $now = now();

        // Activate flash sales that should start
        $toActivate = Promotion::where('promotion_type', 'flash_sale')
            ->where('is_active', false)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->get();

        foreach ($toActivate as $promotion) {
            $promotion->update(['is_active' => true]);
            Cache::tags(['promotions', 'flash_sales'])->flush();
            $this->info("Activated flash sale: {$promotion->name}");
        }

        // Deactivate expired flash sales
        $toDeactivate = Promotion::where('promotion_type', 'flash_sale')
            ->where('is_active', true)
            ->where('end_date', '<', $now)
            ->get();

        foreach ($toDeactivate as $promotion) {
            $promotion->update(['is_active' => false]);
            Cache::tags(['promotions', 'flash_sales'])->flush();
            $this->info("Deactivated flash sale: {$promotion->name}");
        }

        $this->info('Flash sales processing complete.');

        return 0;
    }
}
