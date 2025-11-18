<x-layouts.dashboard>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Inventory Management</h1>
            <p class="text-gray-600 dark:text-gray-400">Monitor and manage stock levels</p>
        </div>
        <div class="flex gap-2">
            <button onclick="exportInventory()" class="btn-outline">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-6">
        <div class="flex items-center gap-4 flex-wrap">
            <div class="flex-1 min-w-[300px]">
                <div class="relative">
                    <input 
                        type="text" 
                        id="searchInput"
                        placeholder="Search products..."
                        class="input w-full pl-10">
                    <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
            <select id="stockFilter" class="input w-48">
                <option value="">All Stock Levels</option>
                <option value="in_stock">In Stock</option>
                <option value="low_stock">Low Stock</option>
                <option value="out_of_stock">Out of Stock</option>
            </select>
            <select id="categoryFilter" class="input w-48">
                <option value="">All Categories</option>
                @foreach(\App\Models\Category::whereNull('parent_id')->get() as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card">
            <p class="text-sm text-gray-600 dark:text-gray-400">Total Products</p>
            <p class="text-2xl font-bold mt-1 dark:text-white">{{ $stats['total'] ?? 0 }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-600 dark:text-gray-400">In Stock</p>
            <p class="text-2xl font-bold mt-1 text-green-600">{{ $stats['in_stock'] ?? 0 }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-600 dark:text-gray-400">Low Stock</p>
            <p class="text-2xl font-bold mt-1 text-yellow-600">{{ $stats['low_stock'] ?? 0 }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-600 dark:text-gray-400">Out of Stock</p>
            <p class="text-2xl font-bold mt-1 text-red-600">{{ $stats['out_of_stock'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="card overflow-hidden" x-data="{ showAdjustModal: false, selectedProduct: null, adjustmentQty: 0, adjustmentReason: '' }">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Current Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Low Stock Alert</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded overflow-hidden flex-shrink-0">
                                    <img src="{{ $product->image_url ?? '/assets/placeholder.png' }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $product->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $product->category->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm dark:text-gray-300">{{ $product->sku ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-lg font-bold {{ $product->stock_quantity < 10 ? 'text-red-600' : ($product->stock_quantity < ($product->low_stock_threshold ?? 10) ? 'text-yellow-600' : 'text-gray-900 dark:text-white') }}">
                                {{ $product->stock_quantity ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $product->low_stock_threshold ?? 10 }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($product->stock_quantity == 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Out of Stock
                                </span>
                            @elseif($product->stock_quantity < ($product->low_stock_threshold ?? 10))
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Low Stock
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    In Stock
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button 
                                @click="selectedProduct = {{ $product->toJson() }}; showAdjustModal = true; adjustmentQty = 0; adjustmentReason = ''"
                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400">
                                Adjust Stock
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <p class="text-lg font-medium">No products found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
        <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-t dark:border-gray-700">
            {{ $products->links() }}
        </div>
        @endif

        <!-- Stock Adjustment Modal -->
        <div x-show="showAdjustModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
             @click.self="showAdjustModal = false"
             style="display: none;">
            <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full"
                 @click.stop>
                <form action="{{ route('inventory.adjust') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" :value="selectedProduct?.id">
                    
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold dark:text-white">Adjust Stock</h2>
                            <button type="button" @click="showAdjustModal = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300" x-text="selectedProduct?.name"></p>
                                <p class="text-xs text-gray-500">Current Stock: <span x-text="selectedProduct?.stock_quantity || 0"></span></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2 dark:text-gray-300">Adjustment Quantity *</label>
                                <input 
                                    type="number" 
                                    name="quantity"
                                    x-model.number="adjustmentQty"
                                    required
                                    class="input w-full"
                                    placeholder="e.g., +10 or -5">
                                <p class="mt-1 text-xs text-gray-500">Use + for increase, - for decrease</p>
                                <p class="mt-1 text-sm font-medium" :class="{ 'text-green-600': adjustmentQty > 0, 'text-red-600': adjustmentQty < 0 }">
                                    New Stock: <span x-text="(selectedProduct?.stock_quantity || 0) + adjustmentQty"></span>
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2 dark:text-gray-300">Reason *</label>
                                <textarea 
                                    name="reason"
                                    x-model="adjustmentReason"
                                    rows="3"
                                    required
                                    class="input w-full"
                                    placeholder="Explain the reason for this adjustment..."></textarea>
                            </div>

                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                                <p class="text-sm text-blue-800 dark:text-blue-200">
                                    ℹ️ This will create a stock movement record for audit purposes.
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-3 mt-6">
                            <button type="button" @click="showAdjustModal = false" class="flex-1 btn-outline">Cancel</button>
                            <button 
                                type="submit"
                                :disabled="!adjustmentQty || !adjustmentReason"
                                class="flex-1 btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                                Update Stock
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function exportInventory() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '{{ route("inventory.index") }}?export=1&' + params.toString();
        }
    </script>
    @endpush
</x-layouts.dashboard>
