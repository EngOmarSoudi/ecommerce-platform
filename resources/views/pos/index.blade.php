<x-layouts.dashboard>
    <div class="fixed inset-0 bg-gray-100 dark:bg-gray-950 md:ml-64 pt-16 md:pt-20" x-data="posApp()">
        <!-- POS Header -->
        <div class="bg-white dark:bg-gray-900 shadow-sm px-4 py-3 flex items-center justify-between">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Point of Sale</h1>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-600 dark:text-gray-400">Staff: <strong>{{ auth()->user()->name }}</strong></span>
                <button @click="clearCart()" class="btn-outline text-sm">Clear Cart</button>
            </div>
        </div>

        <div class="flex h-[calc(100vh-8rem)] overflow-hidden">
            <!-- Left Panel: Products -->
            <div class="flex-1 flex flex-col bg-gray-50 dark:bg-gray-900 overflow-hidden">
                <!-- Category Selector -->
                <div class="bg-white dark:bg-gray-800 p-3 border-b dark:border-gray-700">
                    <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-thin">
                        <button 
                            @click="selectedCategory = null; selectedSubcategory = null"
                            :class="selectedCategory === null ? 'bg-brand.primary text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                            class="px-6 py-3 rounded-lg font-medium whitespace-nowrap touch-manipulation min-w-[120px] transition-all hover:scale-105">
                            All Products
                        </button>
                        <template x-for="cat in categories" :key="cat.id">
                            <button 
                                @click="selectCategory(cat)"
                                :class="selectedCategory?.id === cat.id ? 'bg-brand.primary text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                                class="px-6 py-3 rounded-lg font-medium whitespace-nowrap touch-manipulation min-w-[120px] transition-all hover:scale-105"
                                x-text="cat.name">
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Subcategory Bar (shown when category selected) -->
                <div x-show="selectedCategory && subcategories.length > 0" 
                     x-transition
                     class="bg-white dark:bg-gray-800 px-3 py-2 border-b dark:border-gray-700">
                    <div class="flex gap-2 overflow-x-auto scrollbar-thin">
                        <button 
                            @click="selectedSubcategory = null"
                            :class="selectedSubcategory === null ? 'bg-blue-500 text-white' : 'bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                            class="px-4 py-2 rounded-md text-sm font-medium whitespace-nowrap touch-manipulation">
                            All
                        </button>
                        <template x-for="sub in subcategories" :key="sub.id">
                            <button 
                                @click="selectedSubcategory = sub"
                                :class="selectedSubcategory?.id === sub.id ? 'bg-blue-500 text-white' : 'bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                                class="px-4 py-2 rounded-md text-sm font-medium whitespace-nowrap touch-manipulation"
                                x-text="sub.name">
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="bg-white dark:bg-gray-800 px-3 py-2 border-b dark:border-gray-700">
                    <div class="relative">
                        <input 
                            type="text" 
                            x-model="searchQuery"
                            @input="filterProducts()"
                            placeholder="Search products by name or SKU..."
                            class="input w-full pl-10 text-lg">
                        <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="flex-1 overflow-y-auto p-4">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                        <template x-for="product in filteredProducts" :key="product.id">
                            <button 
                                @click="addToCart(product)"
                                class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow hover:shadow-lg transition-all touch-manipulation active:scale-95 border-2 border-transparent hover:border-brand.primary">
                                <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-md mb-2 overflow-hidden">
                                    <img :src="product.image || '/assets/placeholder.png'" :alt="product.name" class="w-full h-full object-cover">
                                </div>
                                <h3 class="font-semibold text-sm text-gray-900 dark:text-white line-clamp-2 mb-1" x-text="product.name"></h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2" x-text="product.unit || 'Unit'"></p>
                                <p class="text-lg font-bold text-brand.primary" x-text="'$' + parseFloat(product.price).toFixed(2)"></p>
                                <span x-show="product.stock_quantity < 10" class="inline-block mt-1 text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded">Low Stock</span>
                            </button>
                        </template>
                    </div>
                    <div x-show="filteredProducts.length === 0" class="text-center py-12 text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        <p class="text-lg font-medium">No products found</p>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Cart -->
            <div class="w-96 bg-white dark:bg-gray-800 border-l dark:border-gray-700 flex flex-col">
                <!-- Cart Header -->
                <div class="p-4 border-b dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Current Order</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400" x-text="'Items: ' + cartItems.length"></p>
                </div>

                <!-- Cart Items -->
                <div class="flex-1 overflow-y-auto p-4 space-y-2">
                    <template x-for="(item, index) in cartItems" :key="index">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900 dark:text-white text-sm" x-text="item.name"></h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="'$' + parseFloat(item.price).toFixed(2) + ' / ' + (item.unit || 'unit')"></p>
                                </div>
                                <button @click="removeFromCart(index)" class="text-red-500 hover:text-red-700 touch-manipulation">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <button @click="decrementQuantity(index)" class="w-8 h-8 rounded bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 touch-manipulation flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                    </button>
                                    <input 
                                        type="number" 
                                        x-model.number="item.quantity"
                                        @change="updateCart()"
                                        min="1"
                                        class="w-16 text-center border dark:border-gray-600 rounded px-2 py-1 text-sm dark:bg-gray-800 dark:text-white">
                                    <button @click="incrementQuantity(index)" class="w-8 h-8 rounded bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 touch-manipulation flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    </button>
                                </div>
                                <p class="font-bold text-gray-900 dark:text-white" x-text="'$' + (item.price * item.quantity).toFixed(2)"></p>
                            </div>
                        </div>
                    </template>

                    <div x-show="cartItems.length === 0" class="text-center py-12 text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <p>Cart is empty</p>
                    </div>
                </div>

                <!-- Cart Footer: Totals & Checkout -->
                <div class="border-t dark:border-gray-700 p-4 space-y-3 bg-gray-50 dark:bg-gray-900">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                            <span class="font-medium dark:text-white" x-text="'$' + subtotal.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Tax (10%)</span>
                            <span class="font-medium dark:text-white" x-text="'$' + tax.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t dark:border-gray-700 pt-2">
                            <span class="dark:text-white">Total</span>
                            <span class="text-brand.primary" x-text="'$' + total.toFixed(2)"></span>
                        </div>
                    </div>

                    <button 
                        @click="openCheckout()"
                        :disabled="cartItems.length === 0"
                        class="w-full btn-primary py-4 text-lg disabled:opacity-50 disabled:cursor-not-allowed touch-manipulation">
                        Checkout
                    </button>
                </div>
            </div>
        </div>

        <!-- Checkout Modal -->
        <div x-show="showCheckout" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
             @click.self="showCheckout = false">
            <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full max-h-[90vh] overflow-y-auto"
                 @click.stop>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold dark:text-white">Checkout</h2>
                        <button @click="showCheckout = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Customer Name (Optional)</label>
                            <input type="text" x-model="customerName" class="input w-full" placeholder="Enter customer name">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Payment Method</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button 
                                    @click="paymentMethod = 'cash'"
                                    :class="paymentMethod === 'cash' ? 'bg-brand.primary text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                                    class="py-3 rounded-lg font-medium touch-manipulation">
                                    Cash
                                </button>
                                <button 
                                    @click="paymentMethod = 'card'"
                                    :class="paymentMethod === 'card' ? 'bg-brand.primary text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                                    class="py-3 rounded-lg font-medium touch-manipulation">
                                    Card
                                </button>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                                <span class="font-medium dark:text-white" x-text="'$' + subtotal.toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Tax</span>
                                <span class="font-medium dark:text-white" x-text="'$' + tax.toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between text-xl font-bold border-t dark:border-gray-700 pt-2">
                                <span class="dark:text-white">Total</span>
                                <span class="text-brand.primary" x-text="'$' + total.toFixed(2)"></span>
                            </div>
                        </div>

                        <div x-show="paymentMethod === 'cash'">
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Cash Received</label>
                            <input 
                                type="number" 
                                x-model.number="cashReceived"
                                @input="calculateChange()"
                                step="0.01"
                                class="input w-full text-lg"
                                placeholder="0.00">
                            <p class="mt-2 text-sm" :class="change >= 0 ? 'text-green-600' : 'text-red-600'">
                                <span x-show="change >= 0">Change: $<span x-text="change.toFixed(2)"></span></span>
                                <span x-show="change < 0">Insufficient amount</span>
                            </p>
                        </div>

                        <div class="flex gap-3">
                            <button @click="showCheckout = false" class="flex-1 btn-outline">Cancel</button>
                            <button 
                                @click="completeOrder()"
                                :disabled="paymentMethod === 'cash' && change < 0"
                                class="flex-1 btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                                Complete Order
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function posApp() {
            return {
                categories: @json(\App\Models\Category::whereNull('parent_id')->get()),
                products: @json(\App\Models\Product::where('is_active', true)->get()),
                selectedCategory: null,
                selectedSubcategory: null,
                subcategories: [],
                filteredProducts: [],
                searchQuery: '',
                cartItems: [],
                showCheckout: false,
                customerName: '',
                paymentMethod: 'cash',
                cashReceived: 0,
                change: 0,

                init() {
                    this.filteredProducts = this.products;
                },

                selectCategory(category) {
                    this.selectedCategory = category;
                    this.selectedSubcategory = null;
                    // Load subcategories (in real app, fetch from API)
                    this.subcategories = @json(\App\Models\Category::all()).filter(c => c.parent_id === category.id);
                    this.filterProducts();
                },

                filterProducts() {
                    let filtered = this.products;

                    if (this.selectedCategory) {
                        if (this.selectedSubcategory) {
                            filtered = filtered.filter(p => p.category_id === this.selectedSubcategory.id);
                        } else {
                            const categoryIds = [this.selectedCategory.id, ...this.subcategories.map(s => s.id)];
                            filtered = filtered.filter(p => categoryIds.includes(p.category_id));
                        }
                    }

                    if (this.searchQuery) {
                        const query = this.searchQuery.toLowerCase();
                        filtered = filtered.filter(p => 
                            p.name.toLowerCase().includes(query) || 
                            (p.sku && p.sku.toLowerCase().includes(query))
                        );
                    }

                    this.filteredProducts = filtered;
                },

                addToCart(product) {
                    const existing = this.cartItems.find(item => item.id === product.id);
                    if (existing) {
                        existing.quantity++;
                    } else {
                        this.cartItems.push({
                            id: product.id,
                            name: product.name,
                            price: product.price,
                            quantity: 1,
                            unit: product.unit || 'unit'
                        });
                    }
                    this.updateCart();
                },

                removeFromCart(index) {
                    this.cartItems.splice(index, 1);
                    this.updateCart();
                },

                incrementQuantity(index) {
                    this.cartItems[index].quantity++;
                    this.updateCart();
                },

                decrementQuantity(index) {
                    if (this.cartItems[index].quantity > 1) {
                        this.cartItems[index].quantity--;
                        this.updateCart();
                    }
                },

                updateCart() {
                    // Trigger reactivity
                    this.cartItems = [...this.cartItems];
                },

                get subtotal() {
                    return this.cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                },

                get tax() {
                    return this.subtotal * 0.10;
                },

                get total() {
                    return this.subtotal + this.tax;
                },

                clearCart() {
                    if (confirm('Clear all items from cart?')) {
                        this.cartItems = [];
                    }
                },

                openCheckout() {
                    this.showCheckout = true;
                    this.cashReceived = 0;
                    this.change = 0;
                },

                calculateChange() {
                    this.change = this.cashReceived - this.total;
                },

                async completeOrder() {
                    if (this.paymentMethod === 'cash' && this.change < 0) {
                        alert('Insufficient cash received');
                        return;
                    }

                    // In production, send to backend
                    const orderData = {
                        items: this.cartItems,
                        customer_name: this.customerName,
                        payment_method: this.paymentMethod,
                        subtotal: this.subtotal,
                        tax: this.tax,
                        total: this.total,
                        cash_received: this.cashReceived,
                        change: this.change
                    };

                    // For now, open receipt in new window
                    this.printReceipt(orderData);
                    
                    this.showCheckout = false;
                    this.cartItems = [];
                    this.customerName = '';
                    this.cashReceived = 0;
                    alert('Order completed successfully!');
                },

                printReceipt(orderData) {
                    const receiptWindow = window.open('/pos/receipt', '_blank', 'width=300,height=600');
                    receiptWindow.orderData = orderData;
                }
            }
        }
    </script>
    @endpush
</x-layouts.dashboard>
