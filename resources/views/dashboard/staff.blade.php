<x-layouts.dashboard>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Staff Dashboard</h1>
        <p class="text-gray-600 dark:text-gray-400">POS and daily operations</p>
    </div>

    <!-- Staff Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <x-dashboard.metric-card
            title="Transactions Today"
            :value="$todayTransactions"
            color="blue">
            <x-slot:icon>
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </x-slot:icon>
        </x-dashboard.metric-card>

        <x-dashboard.metric-card
            title="Sales Today"
            value="$0.00"
            color="green">
            <x-slot:icon>
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </x-slot:icon>
        </x-dashboard.metric-card>

        <x-dashboard.metric-card
            title="Items Sold"
            value="0"
            color="purple">
            <x-slot:icon>
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </x-slot:icon>
        </x-dashboard.metric-card>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6">
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Quick Actions</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="/pos" class="btn-primary text-center text-lg py-4">
                    <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Open POS
                </a>
                <a href="/orders" class="btn-outline text-center text-lg py-4">View Orders</a>
            </div>
        </div>

        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Recent Activity</h3>
            </div>
            <p class="text-gray-500 text-center py-8">No activity today</p>
        </div>
    </div>
</x-layouts.dashboard>
