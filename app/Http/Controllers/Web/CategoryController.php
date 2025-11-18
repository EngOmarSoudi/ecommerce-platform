<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display category with products
     */
    public function show($slug, Request $request)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $perPage = $request->input('per_page', 12);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        // Get all category IDs (including subcategories)
        $categoryIds = [$category->id];
        if ($category->children()->count() > 0) {
            $categoryIds = array_merge($categoryIds, $category->children()->pluck('id')->toArray());
        }

        $products = Product::whereIn('category_id', $categoryIds)
            ->with(['brand', 'skus', 'media'])
            ->where('status', 'active')
            ->orderBy($sortBy, $sortOrder)
            ->paginate($perPage);

        return view('categories.show', compact('category', 'products'));
    }
}
