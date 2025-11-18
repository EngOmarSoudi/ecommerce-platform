<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Models\Coupon;
use Illuminate\Support\Collection;

class PriceRulesService
{
    /**
     * Calculate final price for a product with all applicable rules
     */
    public function calculatePrice(Product $product, int $quantity = 1, ?User $user = null, ?string $couponCode = null): array
    {
        $basePrice = $product->price;
        $originalPrice = $basePrice * $quantity;
        $discounts = [];

        // 1. Tier Pricing (Bulk/Wholesale)
        $tierDiscount = $this->applyTierPricing($product, $quantity, $user);
        if ($tierDiscount > 0) {
            $discounts[] = [
                'type' => 'tier_pricing',
                'amount' => $tierDiscount,
                'label' => "Bulk discount ({$quantity} units)",
            ];
        }

        // 2. Product-level Promotions
        $productPromotion = $this->applyProductPromotions($product);
        if ($productPromotion > 0) {
            $discounts[] = [
                'type' => 'product_promotion',
                'amount' => $productPromotion * $quantity,
                'label' => 'Product promotion',
            ];
        }

        // 3. Coupon Discount
        if ($couponCode) {
            $couponDiscount = $this->applyCoupon($couponCode, $product, $quantity, $user);
            if ($couponDiscount > 0) {
                $discounts[] = [
                    'type' => 'coupon',
                    'amount' => $couponDiscount,
                    'label' => "Coupon: {$couponCode}",
                ];
            }
        }

        // 4. Flash Sale
        $flashSaleDiscount = $this->applyFlashSale($product);
        if ($flashSaleDiscount > 0) {
            $discounts[] = [
                'type' => 'flash_sale',
                'amount' => $flashSaleDiscount * $quantity,
                'label' => 'Flash sale',
            ];
        }

        $totalDiscount = collect($discounts)->sum('amount');
        $finalPrice = max(0, $originalPrice - $totalDiscount);

        return [
            'original_price' => $originalPrice,
            'final_price' => $finalPrice,
            'total_discount' => $totalDiscount,
            'discounts' => $discounts,
            'unit_price' => $finalPrice / $quantity,
        ];
    }

    /**
     * Apply tier pricing based on quantity
     */
    protected function applyTierPricing(Product $product, int $quantity, ?User $user): float
    {
        // Check if user has B2B/Wholesale access
        $userType = $user?->type ?? 'b2c';
        
        // Get tier pricing rules (stored in product or separate table)
        $tiers = $product->price_tiers ?? [];
        
        foreach ($tiers as $tier) {
            if ($quantity >= $tier['min_quantity']) {
                if (isset($tier['user_type']) && $tier['user_type'] !== $userType) {
                    continue;
                }
                
                if ($tier['discount_type'] === 'percentage') {
                    return ($product->price * $quantity * $tier['discount_value']) / 100;
                } elseif ($tier['discount_type'] === 'fixed') {
                    return $tier['discount_value'] * $quantity;
                }
            }
        }

        return 0;
    }

    /**
     * Apply active product promotions
     */
    protected function applyProductPromotions(Product $product): float
    {
        $activePromotions = $product->promotions()
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        $maxDiscount = 0;

        foreach ($activePromotions as $promotion) {
            $discount = $this->calculatePromotionDiscount($promotion, $product->price);
            $maxDiscount = max($maxDiscount, $discount);
        }

        return $maxDiscount;
    }

    /**
     * Apply coupon discount
     */
    protected function applyCoupon(string $code, Product $product, int $quantity, ?User $user): float
    {
        $coupon = Coupon::where('code', $code)
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->first();

        if (!$coupon) {
            return 0;
        }

        $promotion = $coupon->promotion;
        
        if (!$promotion || !$promotion->is_active) {
            return 0;
        }

        // Check if applicable to this product
        if ($promotion->products()->count() > 0 && !$promotion->products->contains($product->id)) {
            return 0;
        }

        // Check if applicable to product's category
        if ($promotion->categories()->count() > 0 && !$promotion->categories->contains($product->category_id)) {
            return 0;
        }

        return $this->calculatePromotionDiscount($promotion, $product->price * $quantity);
    }

    /**
     * Apply flash sale discount
     */
    protected function applyFlashSale(Product $product): float
    {
        // Check for active flash sales
        $flashSale = $product->promotions()
            ->where('promotion_type', 'flash_sale')
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if (!$flashSale) {
            return 0;
        }

        return $this->calculatePromotionDiscount($flashSale, $product->price);
    }

    /**
     * Calculate discount amount from promotion
     */
    protected function calculatePromotionDiscount($promotion, float $baseAmount): float
    {
        if ($promotion->discount_type === 'percentage') {
            return ($baseAmount * $promotion->discount_value) / 100;
        } elseif ($promotion->discount_type === 'fixed') {
            return min($promotion->discount_value, $baseAmount);
        }

        return 0;
    }

    /**
     * Apply cart-level promotions
     */
    public function applyCartLevelPromotions(Collection $cartItems, ?string $couponCode = null): array
    {
        $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);
        $discounts = [];

        // Minimum purchase discount
        if ($subtotal >= 100) {
            $discounts[] = [
                'type' => 'minimum_purchase',
                'amount' => $subtotal * 0.05, // 5% off orders over $100
                'label' => '5% off orders over $100',
            ];
        }

        // Cart-level coupon
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)
                ->where('is_active', true)
                ->where('expires_at', '>', now())
                ->first();

            if ($coupon && $coupon->promotion && $coupon->promotion->promotion_type === 'cart') {
                $discount = $this->calculatePromotionDiscount($coupon->promotion, $subtotal);
                $discounts[] = [
                    'type' => 'cart_coupon',
                    'amount' => $discount,
                    'label' => "Coupon: {$couponCode}",
                ];
            }
        }

        $totalDiscount = collect($discounts)->sum('amount');

        return [
            'subtotal' => $subtotal,
            'total_discount' => $totalDiscount,
            'final_total' => $subtotal - $totalDiscount,
            'discounts' => $discounts,
        ];
    }

    /**
     * Check price visibility for user type (B2B vs B2C)
     */
    public function canViewPrice(Product $product, ?User $user): bool
    {
        // If product is public, everyone can see price
        if ($product->price_visibility === 'public') {
            return true;
        }

        // B2B only products
        if ($product->price_visibility === 'b2b') {
            return $user && $user->type === 'b2b';
        }

        // Logged in users only
        if ($product->price_visibility === 'authenticated') {
            return $user !== null;
        }

        return true;
    }
}
