<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#3b82f6">
    <title>Checkout - {{ config('app.name') }}</title>
    
    <link rel="manifest" href="/manifest.json">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900 pb-mobile-nav">
    <!-- Mobile Header -->
    <header class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-40">
        <div class="flex items-center justify-between px-4 py-3">
            <button onclick="history.back()" class="p-2 -ml-2 touch-manipulation">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <h1 class="text-lg font-semibold dark:text-white">Checkout</h1>
            <div class="w-6"></div>
        </div>
    </header>

    <!-- Progress Steps -->
    <div class="bg-white dark:bg-gray-800 border-b dark:border-gray-700" x-data="{ step: {{ $step ?? 1 }} }">
        <div class="container py-4">
            <div class="flex items-center justify-between">
                <div class="flex flex-col items-center flex-1">
                    <div :class="step >= 1 ? 'bg-brand.primary text-white' : 'bg-gray-300 text-gray-600'" class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold mb-2">1</div>
                    <span class="text-xs text-gray-600 dark:text-gray-400 hidden sm:block">Shipping</span>
                </div>
                <div class="flex-1 h-1" :class="step >= 2 ? 'bg-brand.primary' : 'bg-gray-300'"></div>
                <div class="flex flex-col items-center flex-1">
                    <div :class="step >= 2 ? 'bg-brand.primary text-white' : 'bg-gray-300 text-gray-600'" class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold mb-2">2</div>
                    <span class="text-xs text-gray-600 dark:text-gray-400 hidden sm:block">Payment</span>
                </div>
                <div class="flex-1 h-1" :class="step >= 3 ? 'bg-brand.primary' : 'bg-gray-300'"></div>
                <div class="flex flex-col items-center flex-1">
                    <div :class="step >= 3 ? 'bg-brand.primary text-white' : 'bg-gray-300 text-gray-600'" class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold mb-2">3</div>
                    <span class="text-xs text-gray-600 dark:text-gray-400 hidden sm:block">Review</span>
                </div>
            </div>
        </div>
    </div>

    <main class="container py-4 space-y-4">
        <!-- Order Summary (Sticky on Mobile) -->
        <div class="lg:hidden mobile-card">
            <button @click="showSummary = !showSummary" class="w-full flex items-center justify-between" x-data="{ showSummary: false }">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span class="font-medium">{{ $cartItemsCount ?? 3 }} items</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-lg">${{ number_format($total ?? 299.99, 2) }}</span>
                    <svg :class="showSummary ? 'rotate-180' : ''" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </button>
            
            <div x-show="showSummary" x-collapse class="mt-4 pt-4 border-t space-y-3">
                @foreach($cartItems ?? [] as $item)
                <div class="flex items-center gap-3">
                    <img src="{{ $item['image'] ?? '/assets/placeholder.png' }}" class="w-16 h-16 object-cover rounded">
                    <div class="flex-1 min-w-0">
                        <p class="font-medium truncate">{{ $item['name'] ?? 'Product' }}</p>
                        <p class="text-sm text-gray-500">Qty: {{ $item['quantity'] ?? 1 }}</p>
                    </div>
                    <p class="font-medium">${{ number_format($item['price'] ?? 99.99, 2) }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Checkout Form -->
        <div class="mobile-card">
            <h2 class="text-xl font-semibold mb-4 dark:text-white">Shipping Information</h2>
            
            <form action="{{ route('checkout.process') }}" method="POST" class="space-y-4">
                @csrf
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2 dark:text-gray-300">First Name *</label>
                        <input type="text" name="first_name" required class="input w-full touch-manipulation" autocomplete="given-name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2 dark:text-gray-300">Last Name *</label>
                        <input type="text" name="last_name" required class="input w-full touch-manipulation" autocomplete="family-name">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Email *</label>
                    <input type="email" name="email" required class="input w-full touch-manipulation" autocomplete="email">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Phone *</label>
                    <input type="tel" name="phone" required class="input w-full touch-manipulation" autocomplete="tel">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Address *</label>
                    <input type="text" name="address" required class="input w-full touch-manipulation" autocomplete="street-address">
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2 dark:text-gray-300">City *</label>
                        <input type="text" name="city" required class="input w-full touch-manipulation" autocomplete="address-level2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2 dark:text-gray-300">State *</label>
                        <input type="text" name="state" required class="input w-full touch-manipulation" autocomplete="address-level1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2 dark:text-gray-300">ZIP *</label>
                        <input type="text" name="zip" required class="input w-full touch-manipulation" autocomplete="postal-code">
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="pt-4 border-t dark:border-gray-700">
                    <h3 class="text-lg font-semibold mb-4 dark:text-white">Payment Method</h3>
                    
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors touch-manipulation">
                            <input type="radio" name="payment_method" value="card" checked class="radio">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span class="font-medium">Credit/Debit Card</span>
                        </label>

                        <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors touch-manipulation">
                            <input type="radio" name="payment_method" value="paypal" class="radio">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944 3.72a.77.77 0 0 1 .76-.638h8.167c2.76 0 4.922.895 5.94 2.456.93 1.423.991 3.158.18 5.16-.984 2.425-2.939 3.84-5.362 3.84H12.3a.77.77 0 0 0-.76.639l-.602 3.817a.641.641 0 0 1-.633.54z"/></svg>
                            <span class="font-medium">PayPal</span>
                        </label>

                        <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors touch-manipulation">
                            <input type="radio" name="payment_method" value="cod" class="radio">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span class="font-medium">Cash on Delivery</span>
                        </label>
                    </div>
                </div>

                <!-- Sticky Bottom Button -->
                <div class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t dark:border-gray-700 p-4 md:relative md:border-0 md:p-0 md:mt-6 mobile-safe-area">
                    <button type="submit" class="btn-primary w-full btn-touch text-lg">
                        Place Order - ${{ number_format($total ?? 299.99, 2) }}
                    </button>
                </div>
            </form>
        </div>
    </main>

    <x-mobile-nav />

    <script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js');
    }
    </script>
</body>
</html>
