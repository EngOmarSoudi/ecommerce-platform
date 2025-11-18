<?php

namespace App\Services\Search;

interface SearchServiceInterface
{
    /**
     * Search for products
     *
     * @param string $query
     * @param array $filters
     * @param int $perPage
     * @return mixed
     */
    public function searchProducts(string $query, array $filters = [], int $perPage = 15);

    /**
     * Get search suggestions
     *
     * @param string $query
     * @param int $limit
     * @return array
     */
    public function getSuggestions(string $query, int $limit = 5): array;

    /**
     * Index a product
     *
     * @param int $productId
     * @return bool
     */
    public function indexProduct(int $productId): bool;

    /**
     * Remove a product from index
     *
     * @param int $productId
     * @return bool
     */
    public function removeProduct(int $productId): bool;

    /**
     * Rebuild entire search index
     *
     * @return bool
     */
    public function rebuildIndex(): bool;
}
