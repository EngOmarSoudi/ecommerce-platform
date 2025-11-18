<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Search\SearchServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    protected SearchServiceInterface $searchService;

    public function __construct(SearchServiceInterface $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Display admin product listing page with filters
     */
    public function index(Request $request)
    {
        $query = Product::query()->with(['category']);

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($category = $request->input('category')) {
            $query->where('category_id', $category);
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('is_active', $status === 'active');
        }

        // Stock filter
        if ($stock = $request->input('stock')) {
            if ($stock === 'in_stock') {
                $query->where('stock_quantity', '>', 10);
            } elseif ($stock === 'low_stock') {
                $query->whereBetween('stock_quantity', [1, 10]);
            } elseif ($stock === 'out_of_stock') {
                $query->where('stock_quantity', '<=', 0);
            }
        }

        // Price range filter
        if ($minPrice = $request->input('min_price')) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice = $request->input('max_price')) {
            $query->where('price', '<=', $maxPrice);
        }

        // Sorting
        $sort = $request->input('sort', 'created_at_desc');
        [$sortField, $sortOrder] = explode('_', $sort . '_desc');
        $sortOrder = in_array($sortOrder, ['asc', 'desc']) ? $sortOrder : 'desc';
        $query->orderBy($sortField === 'created' ? 'created_at' : $sortField, $sortOrder);

        $products = $query->paginate(20)->withQueryString();

        return view('products.index', compact('products'));
    }

    /**
     * Show create product form
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store new product
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'nullable|string|unique:products,sku',
            'barcode' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'track_inventory' => 'nullable|boolean',
            'is_active' => 'required|boolean',
            'category_id' => 'nullable|exists:categories,id',
            'images.*' => 'nullable|image|max:5120'
        ]);

        // Auto-generate SKU if not provided
        if (empty($validated['sku'])) {
            $validated['sku'] = 'PRD-' . strtoupper(Str::random(8));
        }

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);

        $product = Product::create($validated);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                // Store image path (in production, create ProductImage model)
                if ($index === 0) {
                    $product->update(['image_url' => '/storage/' . $path]);
                }
            }
        }

        Cache::tags(['products'])->flush();

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully!');
    }

    /**
     * Show edit product form
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('products.edit', compact('product'));
    }

    /**
     * Update product
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'nullable|string|unique:products,sku,' . $id,
            'barcode' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'track_inventory' => 'nullable|boolean',
            'is_active' => 'required|boolean',
            'category_id' => 'nullable|exists:categories,id',
            'images.*' => 'nullable|image|max:5120'
        ]);

        // Update slug if name changed
        if ($product->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $product->update($validated);

        // Handle new image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                if ($index === 0 || !$product->image_url) {
                    $product->update(['image_url' => '/storage/' . $path]);
                }
            }
        }

        Cache::tags(['products'])->flush();

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Delete product
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        Cache::tags(['products'])->flush();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully!');
    }
