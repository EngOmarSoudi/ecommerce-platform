<?php

namespace App\Services\Search;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class DatabaseSearchService implements SearchServiceInterface
{
    /**
     * Search for products using database queries
     *
     * @param string $query
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchProducts(string $query, array $filters = [], int $perPage = 15)
    {
        $productsQuery = Product::query()
            ->with(['category', 'brand', 'skus', 'media']);

        // Apply search query
        if (!empty($query)) {
            $productsQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            });
        }

        // Apply filters
        if (isset($filters['category_id'])) {
            $productsQuery->where('category_id', $filters['category_id']);
        }

        if (isset($filters['brand_id'])) {
            $productsQuery->where('brand_id', $filters['brand_id']);
        }

        if (isset($filters['min_price'])) {
            $productsQuery->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $productsQuery->where('price', '<=', $filters['max_price']);
        }

        if (isset($filters['status'])) {
            $productsQuery->where('status', $filters['status']);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $productsQuery->orderBy($sortBy, $sortOrder);

        return $productsQuery->paginate($perPage);
    }

    /**
     * Get search suggestions
     *
     * @param string $query
     * @param int $limit
     * @return array
     */
    public function getSuggestions(string $query, int $limit = 5): array
    {
        if (empty($query)) {
            return [];
        }

        $products = Product::where('name', 'like', "%{$query}%")
            ->orWhere('sku', 'like', "%{$query}%")
            ->limit($limit)
            ->get(['id', 'name', 'sku']);

        return $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
            ];
        })->toArray();
    }

    /**
     * Index a product (no-op for database search)
     *
     * @param int $productId
     * @return bool
     */
    public function indexProduct(int $productId): bool
    {
        // Database search doesn't require indexing
        return true;
    }

    /**
     * Remove a product from index (no-op for database search)
     *
     * @param int $productId
     * @return bool
     */
    public function removeProduct(int $productId): bool
    {
        // Database search doesn't require index removal
        return true;
    }

    /**
     * Rebuild entire search index (no-op for database search)
     *
     * @return bool
     */
    public function rebuildIndex(): bool
    {
        // Database search doesn't require index rebuild
        return true;
    }
}
