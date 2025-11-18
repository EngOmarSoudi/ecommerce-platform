<x-layouts.dashboard>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Seller Dashboard</h1>
        <p class="text-gray-600 dark:text-gray-400">Manage your products and track sales</p>
    </div>

    <!-- Seller Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <x-dashboard.metric-card
            title="My Products"
            :value="$sellerProducts"
            color="purple">
            <x-slot:icon>
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </x-slot:icon>
        </x-dashboard.metric-card>

        <x-dashboard.metric-card
            title="Orders Received"
            :value="$sellerOrders"
            :trend="15.2"
            color="blue">
            <x-slot:icon>
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </x-slot:icon>
        </x-dashboard.metric-card>

        <x-dashboard.metric-card
            title="Pending Payouts"
            value="$0.00"
            color="green">
            <x-slot:icon>
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </x-slot:icon>
        </x-dashboard.metric-card>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6">
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Recent Orders</h3>
                <a href="/orders" class="text-sm text-brand.primary hover:underline">View all</a>
            </div>
            <p class="text-gray-500 text-center py-8">No recent orders</p>
        </div>

        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Quick Actions</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="/products/create" class="btn-primary text-center">Add New Product</a>
                <a href="/products" class="btn-outline text-center">Manage Products</a>
                <a href="/orders" class="btn-outline text-center">View Orders</a>
            </div>
        </div>
    </div>
</x-layouts.dashboard>
