<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    /**
     * Get all categories with subcategories
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $categories = Cache::tags(['categories'])->remember('categories.all', 3600, function () {
            return Category::with('children')
                ->whereNull('parent_id')
                ->orderBy('name')
                ->get();
        });

        return response()->json([
            'data' => $categories
        ]);
    }

    /**
     * Get category details
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $category = Category::with('children')->findOrFail($id);

        return response()->json([
            'data' => $category
        ]);
    }

    /**
     * Get products by category
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function products(int $id, Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $category = Category::findOrFail($id);
        
        // Get products from this category and subcategories
        $categoryIds = [$id];
        if ($category->children()->count() > 0) {
            $categoryIds = array_merge($categoryIds, $category->children()->pluck('id')->toArray());
        }

        $products = Product::whereIn('category_id', $categoryIds)
            ->with(['brand', 'skus', 'media'])
            ->where('status', 'active')
            ->orderBy($sortBy, $sortOrder)
            ->paginate($perPage);

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
     * Get category tree for mega menu
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function tree()
    {
        $tree = Cache::tags(['categories'])->remember('categories.tree', 3600, function () {
            return Category::with('children:id,parent_id,name,slug')
                ->whereNull('parent_id')
                ->select('id', 'parent_id', 'name', 'slug')
                ->orderBy('name')
                ->get();
        });

        return response()->json([
            'data' => $tree
        ]);
    }
}
