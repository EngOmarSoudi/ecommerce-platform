<?php

namespace App\Http\Controllers\Web;

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
     * Display product listing page
     */
    public function index(Request $request)
    {
        $query = $request->input('q', '');
        $perPage = $request->input('per_page', 12);
        
        $filters = [
            'category_id' => $request->input('category_id'),
            'brand_id' => $request->input('brand_id'),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'status' => 'active',
            'sort_by' => $request->input('sort_by', 'created_at'),
            'sort_order' => $request->input('sort_order', 'desc'),
        ];

        $filters = array_filter($filters, fn($value) => !is_null($value));
        $products = $this->searchService->searchProducts($query, $filters, $perPage);

        return view('products.index', compact('products', 'query'));
    }

    /**
     * Display product detail page
     */
    public function show($slug)
    {
        $product = Cache::tags(['products'])->remember("product:{$slug}", 3600, function () use ($slug) {
            return Product::where('slug', $slug)
                ->with([
                    'category',
                    'brand',
                    'skus.units',
                    'media',
                    'descriptions',
                    'approvedReviews.user',
                    'seller'
                ])
                ->firstOrFail();
        });

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
