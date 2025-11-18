<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UI Component Library - E-Commerce Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-30">
        <div class="container py-4 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">UI Component Library</h1>
            <div class="flex items-center gap-4">
                <button @click="darkMode = !darkMode" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg x-show="!darkMode" class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg x-show="darkMode" class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>
                <a href="/" class="btn-primary">Back to Dashboard</a>
            </div>
        </div>
    </header>

    <main class="container py-8 space-y-12">
        <!-- Buttons Section -->
        <section class="card animate-fade-in">
            <h2 class="text-xl font-bold mb-6 dark:text-white">Buttons</h2>
            <div class="flex flex-wrap gap-4">
                <button class="btn-primary">Primary Button</button>
                <button class="btn-secondary">Secondary Button</button>
                <button class="btn-outline">Outline Button</button>
                <button class="btn-ghost">Ghost Button</button>
                <button class="btn-success">Success Button</button>
                <button class="btn-danger">Danger Button</button>
                <button class="btn-primary" disabled>Disabled Button</button>
                <button class="btn-primary btn-loading">Loading Button</button>
            </div>
        </section>

        <!-- Loading States Section -->
        <section class="card animate-fade-in" style="animation-delay: 0.1s">
            <h2 class="text-xl font-bold mb-6 dark:text-white">Loading States</h2>
            
            <h3 class="text-lg font-semibold mb-4 dark:text-white">Spinners</h3>
            <div class="flex items-center gap-6 mb-8">
                <x-loading-spinner size="xs" />
                <x-loading-spinner size="sm" />
                <x-loading-spinner size="md" />
                <x-loading-spinner size="lg" />
                <x-loading-spinner size="xl" />
            </div>

            <h3 class="text-lg font-semibold mb-4 dark:text-white">Skeleton Cards</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <x-loading-skeleton type="card" />
                <x-loading-skeleton type="stats" />
                <x-loading-skeleton type="product" />
            </div>

            <h3 class="text-lg font-semibold mb-4 dark:text-white">Skeleton List</h3>
            <x-loading-skeleton type="list" :count="3" />
        </section>

        <!-- Alerts Section -->
        <section class="card animate-fade-in" style="animation-delay: 0.2s">
            <h2 class="text-xl font-bold mb-6 dark:text-white">Alerts</h2>
            
            <x-alert type="success" :dismissible="true" title="Success!">
                Your changes have been saved successfully.
            </x-alert>

            <x-alert type="error" :dismissible="true" title="Error!">
                There was an error processing your request. Please try again.
            </x-alert>

            <x-alert type="warning" :dismissible="true" title="Warning!">
                This action cannot be undone. Please proceed with caution.
            </x-alert>

            <x-alert type="info" :dismissible="true" title="Information">
                Please review the terms and conditions before proceeding.
            </x-alert>
        </section>

        <!-- Empty States Section -->
        <section class="card animate-fade-in" style="animation-delay: 0.3s">
            <h2 class="text-xl font-bold mb-6 dark:text-white">Empty States</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                    <x-empty-state 
                        icon="shopping-cart" 
                        title="Your cart is empty" 
                        description="Add some products to get started with your shopping experience."
                        actionText="Browse Products"
                        actionUrl="/products"
                    />
                </div>

                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                    <x-empty-state 
                        icon="clipboard" 
                        title="No orders yet" 
                        description="You haven't placed any orders. Start shopping to see your order history here."
                        actionText="Start Shopping"
                        actionUrl="/products"
                    />
                </div>

                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                    <x-empty-state 
                        icon="search" 
                        title="No results found" 
                        description="Try adjusting your search or filter to find what you're looking for."
                    />
                </div>

                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                    <x-empty-state 
                        icon="package" 
                        title="No products available" 
                        description="There are no products in this category yet. Check back later!"
                    />
                </div>
            </div>
        </section>

        <!-- Cards Section -->
        <section class="card animate-fade-in" style="animation-delay: 0.4s">
            <h2 class="text-xl font-bold mb-6 dark:text-white">Cards with Hover Effects</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="card card-hover cursor-pointer">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Total Sales</h3>
                    <p class="text-3xl font-bold text-brand.primary mb-2">$24,500</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">+12% from last month</p>
                </div>

                <div class="card card-hover cursor-pointer">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Orders</h3>
                    <p class="text-3xl font-bold text-brand.primary mb-2">1,234</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">+8% from last month</p>
                </div>

                <div class="card card-hover cursor-pointer">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Customers</h3>
                    <p class="text-3xl font-bold text-brand.primary mb-2">8,456</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">+24% from last month</p>
                </div>
            </div>
        </section>

        <!-- Badges Section -->
        <section class="card animate-fade-in" style="animation-delay: 0.5s">
            <h2 class="text-xl font-bold mb-6 dark:text-white">Badges & Tags</h2>
            
            <div class="flex flex-wrap gap-3 mb-6">
                <span class="badge">Default</span>
                <span class="badge-success">Success</span>
                <span class="badge-warning">Warning</span>
                <span class="badge-error">Error</span>
            </div>

            <div class="flex flex-wrap gap-2">
                <span class="tag">Tag 1</span>
                <span class="tag">Tag 2</span>
                <span class="tag">Tag 3</span>
                <span class="tag">Tag 4</span>
            </div>
        </section>

        <!-- Animation Examples -->
        <section class="card animate-fade-in" style="animation-delay: 0.6s">
            <h2 class="text-xl font-bold mb-6 dark:text-white">Animations</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <button @click="$el.classList.add('animate-pulse-soft'); setTimeout(() => $el.classList.remove('animate-pulse-soft'), 2000)" class="btn-outline">Pulse</button>
                <button @click="$el.classList.add('animate-shake'); setTimeout(() => $el.classList.remove('animate-shake'), 500)" class="btn-outline">Shake</button>
                <button @click="$el.classList.add('animate-scale-in'); setTimeout(() => $el.classList.remove('animate-scale-in'), 200)" class="btn-outline">Scale In</button>
                <button @click="$el.classList.add('animate-slide-in-right'); setTimeout(() => $el.classList.remove('animate-slide-in-right'), 300)" class="btn-outline">Slide Right</button>
            </div>
        </section>

        <!-- Toast Demo -->
        <section class="card animate-fade-in" style="animation-delay: 0.7s">
            <h2 class="text-xl font-bold mb-6 dark:text-white">Toast Notifications (Click to Show)</h2>
            
            <div class="flex flex-wrap gap-4">
                <button @click="window.showToast('success')" class="btn-success">Show Success Toast</button>
                <button @click="window.showToast('error')" class="btn-danger">Show Error Toast</button>
                <button @click="window.showToast('warning')" class="btn-outline">Show Warning Toast</button>
                <button @click="window.showToast('info')" class="btn-primary">Show Info Toast</button>
            </div>
        </section>

        <!-- Typography -->
        <section class="card animate-fade-in" style="animation-delay: 0.8s">
            <h2 class="text-xl font-bold mb-6 dark:text-white">Typography</h2>
            
            <h1 class="text-4xl font-bold mb-2 dark:text-white">Heading 1</h1>
            <h2 class="text-3xl font-bold mb-2 dark:text-white">Heading 2</h2>
            <h3 class="text-2xl font-bold mb-2 dark:text-white">Heading 3</h3>
            <h4 class="text-xl font-bold mb-2 dark:text-white">Heading 4</h4>
            <p class="text-gray-600 dark:text-gray-400 mb-4">Regular paragraph text with proper spacing and color contrast for readability.</p>
            <p class="text-sm text-gray-500 dark:text-gray-500">Small text for captions and secondary information.</p>
            <p class="gradient-text text-2xl font-bold mt-4">Gradient Text Effect</p>
        </section>
    </main>

    <!-- Toast Container -->
    <div id="toast-container"></div>

    <script>
        window.showToast = function(type) {
            const messages = {
                success: 'Operation completed successfully!',
                error: 'An error occurred. Please try again.',
                warning: 'Please review your changes before proceeding.',
                info: 'You have 3 new notifications.'
            };

            const toast = document.createElement('div');
            toast.innerHTML = `
                <x-toast type="${type}" message="${messages[type]}" />
            `;
            
            const container = document.getElementById('toast-container');
            container.appendChild(toast.firstElementChild);
            
            setTimeout(() => {
                toast.firstElementChild.remove();
            }, 5500);
        };
    </script>
</body>
</html>
