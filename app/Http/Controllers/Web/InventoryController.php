<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        // Check for export request
        if ($request->has('export')) {
            return $this->exportInventory($request);
        }

        $query = Product::with('category');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Stock level filter
        if ($request->filled('stock_level')) {
            $level = $request->stock_level;
            if ($level === 'out_of_stock') {
                $query->where('stock_quantity', 0);
            } elseif ($level === 'low_stock') {
                $query->where('stock_quantity', '>', 0)
                      ->whereRaw('stock_quantity < low_stock_threshold');
            } elseif ($level === 'in_stock') {
                $query->whereRaw('stock_quantity >= low_stock_threshold');
            }
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Get stats (cached for 5 minutes)
        $stats = Cache::remember('inventory_stats', 300, function() {
            return [
                'total' => Product::count(),
                'in_stock' => Product::whereRaw('stock_quantity >= low_stock_threshold')->count(),
                'low_stock' => Product::where('stock_quantity', '>', 0)
                                     ->whereRaw('stock_quantity < low_stock_threshold')->count(),
                'out_of_stock' => Product::where('stock_quantity', 0)->count(),
            ];
        });

        $products = $query->latest()->paginate(20)->withQueryString();

        return view('inventory.index', compact('products', 'stats'));
    }

    public function adjust(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|not_in:0',
            'reason' => 'required|string|max:500',
        ]);

        DB::transaction(function() use ($validated) {
            $product = Product::findOrFail($validated['product_id']);
            $oldQuantity = $product->stock_quantity;
            $newQuantity = $oldQuantity + $validated['quantity'];

            // Prevent negative stock
            if ($newQuantity < 0) {
                throw new \Exception('Stock cannot be negative');
            }

            // Update product stock
            $product->update(['stock_quantity' => $newQuantity]);

            // Create stock movement record
            StockMovement::create([
                'product_id' => $product->id,
                'quantity' => $validated['quantity'],
                'type' => $validated['quantity'] > 0 ? 'adjustment_in' : 'adjustment_out',
                'reason' => $validated['reason'],
                'old_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity,
                'user_id' => auth()->id(),
            ]);

            // Invalidate cache
            Cache::forget('inventory_stats');
        });

        return redirect()->route('inventory.index')
                         ->with('success', 'Stock adjusted successfully');
    }

    private function exportInventory(Request $request)
    {
        $products = Product::with('category')->get();

        $csv = "Product Name,SKU,Category,Current Stock,Low Stock Alert,Status\n";
        
        foreach ($products as $product) {
            $status = $product->stock_quantity == 0 ? 'Out of Stock' : 
                     ($product->stock_quantity < ($product->low_stock_threshold ?? 10) ? 'Low Stock' : 'In Stock');
            
            $csv .= sprintf(
                "\"%s\",\"%s\",\"%s\",%d,%d,\"%s\"\n",
                $product->name,
                $product->sku ?? 'N/A',
                $product->category->name ?? 'N/A',
                $product->stock_quantity ?? 0,
                $product->low_stock_threshold ?? 10,
                $status
            );
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="inventory-' . now()->format('Y-m-d') . '.csv"');
    }
}
