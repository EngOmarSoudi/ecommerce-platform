<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Commission;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CommissionService
{
    /**
     * Calculate commission for an order
     */
    public function calculateCommission(Order $order): array
    {
        $commissions = [];
        
        foreach ($order->items as $item) {
            $product = $item->sku->product;
            $seller = $product->seller;
            
            if (!$seller) {
                continue;
            }

            // Get commission rate (per-category or seller-specific override)
            $rate = $this->getCommissionRate($product->category_id, $seller->id);
            
            $commissionAmount = $item->total_amount * ($rate / 100);
            
            $commissions[] = [
                'seller_id' => $seller->id,
                'product_id' => $product->id,
                'order_item_id' => $item->id,
                'rate' => $rate,
                'amount' => $commissionAmount,
            ];
        }

        return $commissions;
    }

    /**
     * Get commission rate for category/seller
     */
    protected function getCommissionRate(int $categoryId, int $sellerId): float
    {
        // Check for seller-specific override
        $override = DB::table('seller_commission_overrides')
            ->where('seller_id', $sellerId)
            ->where('category_id', $categoryId)
            ->first();

        if ($override) {
            return $override->rate;
        }

        // Get category default rate
        $category = Category::find($categoryId);
        return $category->commission_rate ?? 10.0; // Default 10%
    }

    /**
     * Record commissions for order
     */
    public function recordCommissions(Order $order): void
    {
        $commissions = $this->calculateCommission($order);

        foreach ($commissions as $commissionData) {
            Commission::create(array_merge($commissionData, [
                'order_id' => $order->id,
                'status' => 'pending',
            ]));
        }
    }
}
