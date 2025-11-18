<?php

namespace App\Services;

use App\Models\Product;
use App\Models\UserBehavior;
use App\Models\ProductSimilarity;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    /**
     * Get personalized recommendations for a user
     */
    public function getPersonalizedRecommendations(?int $userId = null, int $limit = 10): array
    {
        $userId = $userId ?? auth()->id();
        
        if (!$userId) {
            return $this->getTrendingProducts($limit);
        }

        $cacheKey = "recommendations:user:{$userId}:limit:{$limit}";
        
        return Cache::tags(['recommendations'])->remember($cacheKey, 3600, function () use ($userId, $limit) {
            // Get user's purchase and view history
            $userProducts = UserBehavior::where('user_id', $userId)
                ->whereIn('event_type', ['purchase', 'view'])
                ->distinct('product_id')
                ->pluck('product_id')
                ->toArray();

            if (empty($userProducts)) {
                return $this->getTrendingProducts($limit);
            }

            // Collaborative filtering: Find similar products
            $recommendedIds = ProductSimilarity::whereIn('product_id', $userProducts)
                ->whereNotIn('similar_product_id', $userProducts)
                ->orderByDesc('similarity_score')
                ->limit($limit * 2)
                ->pluck('similar_product_id')
                ->unique()
                ->take($limit)
                ->toArray();

            if (empty($recommendedIds)) {
                return $this->getTrendingProducts($limit);
            }

            return Product::whereIn('id', $recommendedIds)
                ->where('is_active', true)
                ->with('category')
                ->get()
                ->toArray();
        });
    }

    /**
     * Get "Customers also bought" recommendations
     */
    public function getAlsoBought(int $productId, int $limit = 6): array
    {
        $cacheKey = "recommendations:also_bought:{$productId}:limit:{$limit}";
        
        return Cache::tags(['recommendations'])->remember($cacheKey, 7200, function () use ($productId, $limit) {
            // Find users who purchased this product
            $userIds = UserBehavior::where('product_id', $productId)
                ->where('event_type', 'purchase')
                ->distinct('user_id')
                ->pluck('user_id');

            if ($userIds->isEmpty()) {
                return [];
            }

            // Find other products these users purchased
            $alsoBoughtIds = UserBehavior::whereIn('user_id', $userIds)
                ->where('product_id', '!=', $productId)
                ->where('event_type', 'purchase')
                ->select('product_id', DB::raw('COUNT(*) as purchase_count'))
                ->groupBy('product_id')
                ->orderByDesc('purchase_count')
                ->limit($limit)
                ->pluck('product_id')
                ->toArray();

            return Product::whereIn('id', $alsoBoughtIds)
                ->where('is_active', true)
                ->with('category')
                ->get()
                ->toArray();
        });
    }

    /**
     * Get similar items based on pre-computed similarities
     */
    public function getSimilarItems(int $productId, int $limit = 6): array
    {
        $cacheKey = "recommendations:similar:{$productId}:limit:{$limit}";
        
        return Cache::tags(['recommendations'])->remember($cacheKey, 7200, function () use ($productId, $limit) {
            $similarIds = ProductSimilarity::where('product_id', $productId)
                ->orderByDesc('similarity_score')
                ->limit($limit)
                ->pluck('similar_product_id')
                ->toArray();

            if (empty($similarIds)) {
                // Fallback: Same category
                $product = Product::find($productId);
                if ($product) {
                    $similarIds = Product::where('category_id', $product->category_id)
                        ->where('id', '!=', $productId)
                        ->where('is_active', true)
                        ->limit($limit)
                        ->pluck('id')
                        ->toArray();
                }
            }

            return Product::whereIn('id', $similarIds)
                ->where('is_active', true)
                ->with('category')
                ->get()
                ->toArray();
        });
    }

    /**
     * Get trending products
     */
    public function getTrendingProducts(int $limit = 10): array
    {
        $cacheKey = "recommendations:trending:limit:{$limit}";
        
        return Cache::tags(['recommendations'])->remember($cacheKey, 1800, function () use ($limit) {
            // Products with most views/purchases in last 7 days
            $trendingIds = UserBehavior::where('event_at', '>=', now()->subDays(7))
                ->whereIn('event_type', ['view', 'purchase'])
                ->select('product_id', DB::raw('COUNT(*) as interaction_count'))
                ->groupBy('product_id')
                ->orderByDesc('interaction_count')
                ->limit($limit)
                ->pluck('product_id')
                ->toArray();

            if (empty($trendingIds)) {
                // Fallback: Latest products
                $trendingIds = Product::where('is_active', true)
                    ->latest()
                    ->limit($limit)
                    ->pluck('id')
                    ->toArray();
            }

            return Product::whereIn('id', $trendingIds)
                ->where('is_active', true)
                ->with('category')
                ->get()
                ->toArray();
        });
    }

    /**
     * Get recently viewed products for current session/user
     */
    public function getRecentlyViewed(int $limit = 10): array
    {
        $userId = auth()->id();
        $sessionId = session()->getId();

        $cacheKey = $userId 
            ? "recommendations:recent:user:{$userId}:limit:{$limit}"
            : "recommendations:recent:session:{$sessionId}:limit:{$limit}";
        
        return Cache::tags(['recommendations'])->remember($cacheKey, 1800, function () use ($userId, $sessionId, $limit) {
            $query = UserBehavior::where('event_type', 'view');

            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }

            $productIds = $query->orderByDesc('event_at')
                ->limit($limit)
                ->pluck('product_id')
                ->unique()
                ->toArray();

            return Product::whereIn('id', $productIds)
                ->where('is_active', true)
                ->with('category')
                ->get()
                ->toArray();
        });
    }

    /**
     * Calculate behavioral score for a user
     */
    public function getUserBehavioralScore(int $userId): array
    {
        $cacheKey = "user:score:{$userId}";
        
        return Cache::tags(['user_scores'])->remember($cacheKey, 3600, function () use ($userId) {
            $behaviors = UserBehavior::where('user_id', $userId)
                ->where('event_at', '>=', now()->subDays(30))
                ->get();

            $score = [
                'engagement_score' => 0,
                'purchase_frequency' => 0,
                'avg_cart_value' => 0,
                'category_preferences' => [],
                'last_activity' => null,
            ];

            if ($behaviors->isEmpty()) {
                return $score;
            }

            // Engagement score based on different actions
            $weights = [
                'purchase' => 10,
                'cart_add' => 5,
                'wishlist_add' => 3,
                'view' => 1,
                'review' => 7,
            ];

            foreach ($behaviors as $behavior) {
                $score['engagement_score'] += $weights[$behavior->event_type] ?? 0;
            }

            // Purchase metrics
            $purchases = $behaviors->where('event_type', 'purchase');
            $score['purchase_frequency'] = $purchases->count();
            $score['avg_cart_value'] = $purchases->avg('price') ?? 0;

            // Category preferences
            $categoryPrefs = $behaviors->groupBy('product.category_id')
                ->map->count()
                ->sortDesc()
                ->take(5)
                ->toArray();

            $score['category_preferences'] = $categoryPrefs;
            $score['last_activity'] = $behaviors->max('event_at');

            return $score;
        });
    }

    /**
     * Invalidate recommendation caches
     */
    public function invalidateCache(): void
    {
        Cache::tags(['recommendations', 'user_scores'])->flush();
    }
}
