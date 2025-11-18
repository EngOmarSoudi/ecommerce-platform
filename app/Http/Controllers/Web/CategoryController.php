<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display category listing (admin)
     */
    public function index()
    {
        $categories = Category::withCount(['products', 'children'])
            ->with('parent')
            ->get()
            ->map(function ($cat) {
                $cat->parent_name = $cat->parent ? $cat->parent->name : null;
                return $cat;
            });

        return view('categories.index', compact('categories'));
    }

    /**
     * Show create category form
     */
    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->get();
        return view('categories.create', compact('parentCategories'));
    }

    /**
     * Store new category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'required|boolean',
            'is_featured' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'image' => 'nullable|image|max:5120'
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $validated['image_url'] = '/storage/' . $path;
        }

        $category = Category::create($validated);

        Cache::tags(['categories'])->flush();

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully!');
    }

    /**
     * Show edit category form
     */
    public function edit($id)
    {
        $category = Category::withCount(['products', 'children'])->findOrFail($id);
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $id)
            ->get();
        
        return view('categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update category
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug,' . $id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'required|boolean',
            'is_featured' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'image' => 'nullable|image|max:5120'
        ]);

        // Prevent circular reference
        if ($validated['parent_id'] == $id) {
            return back()->withErrors(['parent_id' => 'A category cannot be its own parent.']);
        }

        // Update slug if name changed
        if ($category->name !== $validated['name'] && empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle new image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $validated['image_url'] = '/storage/' . $path;
        }

        $category->update($validated);

        Cache::tags(['categories'])->flush();

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Delete category
     */
    public function destroy($id)
    {
        $category = Category::withCount('children')->findOrFail($id);

        if ($category->children_count > 0) {
            return back()->withErrors(['category' => 'Cannot delete category with subcategories. Delete subcategories first.']);
        }

        // Reassign products to uncategorized or parent category
        if ($category->products()->count() > 0) {
            $category->products()->update(['category_id' => $category->parent_id]);
        }

        $category->delete();

        Cache::tags(['categories'])->flush();

        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully!');
    }
