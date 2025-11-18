<?php

namespace App\Services;

use App\Models\Commission;
use App\Models\Payout;
use App\Models\Seller;
use Illuminate\Support\Facades\DB;

class PayoutService
{
    /**
     * Generate payout batches for eligible sellers
     */
    public function generatePayoutBatches(): array
    {
        $batches = [];
        
        // Get sellers with pending commissions
        $sellersWithPendingCommissions = Commission::where('status', 'approved')
            ->whereNull('payout_id')
            ->groupBy('seller_id')
            ->selectRaw('seller_id, SUM(amount) as total_amount')
            ->having('total_amount', '>', 0)
            ->get();

        foreach ($sellersWithPendingCommissions as $sellerCommission) {
            $seller = Seller::find($sellerCommission->seller_id);
            
            // Check minimum payout threshold
            if ($sellerCommission->total_amount < ($seller->minimum_payout ?? 100)) {
                continue;
            }

            $batch = $this->createPayoutBatch($seller, $sellerCommission->total_amount);
            $batches[] = $batch;
        }

        return $batches;
    }

    /**
     * Create payout batch for seller
     */
    protected function createPayoutBatch(Seller $seller, float $amount): Payout
    {
        return DB::transaction(function () use ($seller, $amount) {
            $payout = Payout::create([
                'seller_id' => $seller->id,
                'amount' => $amount,
                'currency' => 'USD',
                'status' => 'pending',
                'batch_number' => $this->generateBatchNumber(),
            ]);

            // Link commissions to this payout
            Commission::where('seller_id', $seller->id)
                ->where('status', 'approved')
                ->whereNull('payout_id')
                ->update(['payout_id' => $payout->id]);

            return $payout;
        });
    }

    /**
     * Process payout (mark as settled)
     */
    public function settlePayout(Payout $payout, array $data): void
    {
        $payout->update([
            'status' => 'completed',
            'settled_at' => now(),
            'transaction_id' => $data['transaction_id'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        // Update commissions status
        Commission::where('payout_id', $payout->id)
            ->update(['status' => 'paid']);
    }

    /**
     * Generate unique batch number
     */
    protected function generateBatchNumber(): string
    {
        return 'PAYOUT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
