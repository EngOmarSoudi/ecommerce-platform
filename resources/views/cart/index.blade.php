<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Shopping Cart') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if($cart->items->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold mb-4">Cart Items ({{ $summary['item_count'] }})</h3>
                                
                                @foreach($cart->items as $item)
                                    <div class="flex items-center border-b border-gray-200 py-4 last:border-b-0">
                                        <!-- Product Image -->
                                        <div class="w-24 h-24 flex-shrink-0">
                                            @if($item->sku->product->media->first())
                                                <img 
                                                    src="{{ $item->sku->product->media->first()->url }}" 
                                                    alt="{{ $item->sku->product->name }}" 
                                                    class="w-full h-full object-cover rounded"
                                                >
                                            @else
                                                <div class="w-full h-full bg-gray-200 flex items-center justify-center rounded">
                                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Product Info -->
                                        <div class="ml-4 flex-1">
                                            <h4 class="text-base font-medium text-gray-900">
                                                <a href="{{ route('products.show', $item->sku->product->slug) }}" class="hover:text-indigo-600">
                                                    {{ $item->sku->product->name }}
                                                </a>
                                            </h4>
                                            @if($item->sku->name)
                                                <p class="text-sm text-gray-500">SKU: {{ $item->sku->name }}</p>
                                            @endif
                                            @if($item->unit)
                                                <p class="text-sm text-gray-500">Unit: {{ $item->unit->name }}</p>
                                            @endif
                                            <p class="text-sm text-gray-900 font-semibold mt-1">${{ number_format($item->price, 2) }} each</p>
                                        </div>

                                        <!-- Quantity Controls -->
                                        <div class="flex items-center space-x-2 mx-4">
                                            <form method="POST" action="{{ route('cart.updateItem', $item->id) }}" class="flex items-center space-x-2">
                                                @csrf
                                                @method('PUT')
                                                <input 
                                                    type="number" 
                                                    name="quantity" 
                                                    value="{{ $item->quantity }}" 
                                                    min="1" 
                                                    class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                >
                                                <button type="submit" class="px-3 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">
                                                    Update
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Item Total -->
                                        <div class="text-right w-24">
                                            <p class="text-base font-semibold text-gray-900">${{ number_format($item->total_amount, 2) }}</p>
                                        </div>

                                        <!-- Remove Button -->
                                        <div class="ml-4">
                                            <form method="POST" action="{{ route('cart.removeItem', $item->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Clear Cart -->
                                <div class="mt-6 pt-4 border-t border-gray-200">
                                    <form method="POST" action="{{ route('cart.clear') }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm" onclick="return confirm('Are you sure you want to clear your cart?')">
                                            Clear Cart
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cart Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg sticky top-4">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold mb-4">Order Summary</h3>
                                
                                <div class="space-y-2 mb-4">
                                    <div class="flex justify-between text-gray-700">
                                        <span>Subtotal</span>
                                        <span>${{ number_format($summary['subtotal'], 2) }}</span>
                                    </div>
                                    <div class="flex justify-between text-gray-700">
                                        <span>Tax ({{ $summary['tax_rate'] * 100 }}%)</span>
                                        <span>${{ number_format($summary['tax'], 2) }}</span>
                                    </div>
                                    <div class="flex justify-between text-gray-700">
                                        <span>Shipping</span>
                                        <span>{{ $summary['shipping'] > 0 ? '$' . number_format($summary['shipping'], 2) : 'FREE' }}</span>
                                    </div>
                                    @if($summary['subtotal'] < 100)
                                        <p class="text-xs text-gray-500">Free shipping on orders over $100</p>
                                    @endif
                                </div>

                                <div class="border-t border-gray-200 pt-4 mb-6">
                                    <div class="flex justify-between text-lg font-bold text-gray-900">
                                        <span>Total</span>
                                        <span>${{ number_format($summary['total'], 2) }}</span>
                                    </div>
                                </div>

                                <a href="{{ route('checkout.index') }}" class="w-full block text-center bg-indigo-600 text-white py-3 px-6 rounded-lg hover:bg-indigo-700 transition-colors font-semibold">
                                    Proceed to Checkout
                                </a>

                                <a href="{{ route('products.index') }}" class="mt-3 w-full block text-center text-indigo-600 hover:text-indigo-800 text-sm">
                                    Continue Shopping
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty Cart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Your cart is empty</h3>
                        <p class="text-gray-600 mb-6">Add some products to get started!</p>
                        <a href="{{ route('products.index') }}" class="inline-block bg-indigo-600 text-white py-2 px-6 rounded-lg hover:bg-indigo-700 transition-colors">
                            Browse Products
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
