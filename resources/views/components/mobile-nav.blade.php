<!-- Mobile Bottom Navigation -->
<nav class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 z-50 md:hidden">
    <div class="flex items-center justify-around h-16">
        <a href="/" class="flex flex-col items-center justify-center flex-1 h-full {{ request()->is('/') ? 'text-brand.primary' : 'text-gray-600 dark:text-gray-400' }} transition-colors active:bg-gray-100 dark:active:bg-gray-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="text-xs mt-1">Home</span>
        </a>

        <a href="/products" class="flex flex-col items-center justify-center flex-1 h-full {{ request()->is('products*') ? 'text-brand.primary' : 'text-gray-600 dark:text-gray-400' }} transition-colors active:bg-gray-100 dark:active:bg-gray-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <span class="text-xs mt-1">Shop</span>
        </a>

        <a href="/cart" class="flex flex-col items-center justify-center flex-1 h-full {{ request()->is('cart*') ? 'text-brand.primary' : 'text-gray-600 dark:text-gray-400' }} transition-colors active:bg-gray-100 dark:active:bg-gray-800 relative">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span class="text-xs mt-1">Cart</span>
            @if(session('cart_count', 0) > 0)
            <span class="absolute top-1 right-1/4 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                {{ session('cart_count') }}
            </span>
            @endif
        </a>

        <a href="/orders" class="flex flex-col items-center justify-center flex-1 h-full {{ request()->is('orders*') ? 'text-brand.primary' : 'text-gray-600 dark:text-gray-400' }} transition-colors active:bg-gray-100 dark:active:bg-gray-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span class="text-xs mt-1">Orders</span>
        </a>

        <a href="/profile" class="flex flex-col items-center justify-center flex-1 h-full {{ request()->is('profile*') ? 'text-brand.primary' : 'text-gray-600 dark:text-gray-400' }} transition-colors active:bg-gray-100 dark:active:bg-gray-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="text-xs mt-1">Account</span>
        </a>
    </div>
</nav>

<!-- Spacer to prevent content from being hidden behind fixed nav -->
<div class="h-16 md:hidden"></div>
