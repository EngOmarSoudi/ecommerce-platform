<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Search\SearchServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    protected SearchServiceInterface $searchService;

    public function __construct(SearchServiceInterface $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Get paginated product list with filters
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = $request->input('q', '');
        $perPage = $request->input('per_page', 15);
        
        $filters = [
            'category_id' => $request->input('category_id'),
            'brand_id' => $request->input('brand_id'),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'status' => $request->input('status', 'active'),
            'sort_by' => $request->input('sort_by', 'created_at'),
            'sort_order' => $request->input('sort_order', 'desc'),
        ];

        // Remove null filters
        $filters = array_filter($filters, fn($value) => !is_null($value));

        $products = $this->searchService->searchProducts($query, $filters, $perPage);

        return response()->json([
            'data' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    /**
     * Get product details with variants
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $cacheKey = "product:{$id}";
        
        $product = Cache::tags(['products'])->remember($cacheKey, 3600, function () use ($id) {
            return Product::with([
                'category',
                'brand',
                'skus.units',
                'media',
                'descriptions',
                'seller'
            ])->findOrFail($id);
        });

        return response()->json([
            'data' => $product
        ]);
    }

    /**
     * Get search suggestions
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestions(Request $request)
    {
        $query = $request->input('q', '');
        $limit = $request->input('limit', 5);

        $suggestions = $this->searchService->getSuggestions($query, $limit);

        return response()->json([
            'data' => $suggestions
        ]);
    }

    /**
     * Get product by SKU
     *
     * @param string $sku
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBySku(string $sku)
    {
        $product = Product::where('sku', $sku)
            ->with(['category', 'brand', 'skus', 'media'])
            ->firstOrFail();

        return response()->json([
            'data' => $product
        ]);
    }
}
