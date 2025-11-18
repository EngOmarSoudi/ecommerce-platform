<x-layouts.dashboard>
    @php
        $user = auth()->user();
        $role = $user ? $user->role : 'guest';
        
        // Metrics calculation
        $salesToday = \App\Models\Order::where('payment_status','paid')
            ->whereDate('created_at', today())
            ->sum('total_amount');
        $totalOrders = \App\Models\Order::count();
        $activeProducts = \App\Models\Product::where('is_active', true)->count();
        $totalCustomers = \App\Models\User::where('role','customer')->count();
        
        // Sales last 7 days for chart
        $salesData = [];
        $labels = [];
        for($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $labels[] = $date->format('M d');
            $salesData[] = \App\Models\Order::where('payment_status','paid')
                ->whereDate('created_at', $date)
                ->sum('total_amount');
        }
    @endphp

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
        <p class="text-gray-600 dark:text-gray-400">Welcome back, {{ $user->name ?? 'User' }}!</p>
    </div>

    <!-- Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-dashboard.metric-card
            title="Sales Today"
            :value="'$' . number_format($salesToday, 2)"
            :trend="12.5"
            color="green">
            <x-slot:icon>
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </x-slot:icon>
        </x-dashboard.metric-card>

        <x-dashboard.metric-card
            title="Total Orders"
            :value="$totalOrders"
            :trend="8.3"
            color="blue">
            <x-slot:icon>
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </x-slot:icon>
        </x-dashboard.metric-card>

        <x-dashboard.metric-card
            title="Active Products"
            :value="$activeProducts"
            color="purple">
            <x-slot:icon>
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </x-slot:icon>
        </x-dashboard.metric-card>

        <x-dashboard.metric-card
            title="Customers"
            :value="$totalCustomers"
            :trend="5.2"
            color="orange">
            <x-slot:icon>
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </x-slot:icon>
        </x-dashboard.metric-card>
    </div>

    <!-- Charts Row -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card">
            <h3 class="text-lg font-semibold mb-4">Sales Last 7 Days</h3>
            <canvas id="salesChart" height="200"></canvas>
        </div>
        <div class="card">
            <h3 class="text-lg font-semibold mb-4">Order Status</h3>
            <canvas id="orderChart" height="200"></canvas>
        </div>
    </div>

    <!-- Data Tables -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Recent Orders</h3>
                <a href="/orders" class="text-sm text-brand.primary hover:underline">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse(\App\Models\Order::latest()->limit(8)->get() as $order)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-3 py-3 text-sm">#{{ $order->order_number }}</td>
                                <td class="px-3 py-3 text-sm">
                                    <span class="badge">{{ $order->status }}</span>
                                </td>
                                <td class="px-3 py-3 text-sm text-right font-medium">${{ number_format($order->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-3 py-6 text-center text-gray-500">No orders yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Top Products</h3>
                <a href="/products" class="text-sm text-brand.primary hover:underline">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Stock</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse(\App\Models\Product::where('is_active', true)->latest()->limit(8)->get() as $p)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-3 py-3 text-sm">{{ Str::limit($p->name, 30) }}</td>
                                <td class="px-3 py-3 text-sm text-right font-medium">${{ number_format($p->price, 2) }}</td>
                                <td class="px-3 py-3 text-sm text-right">
                                    <span class="{{ $p->stock_quantity < 10 ? 'text-red-600' : 'text-gray-900 dark:text-gray-100' }}">{{ $p->stock_quantity ?? 0 }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-3 py-6 text-center text-gray-500">No products yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Sales Chart
        new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Sales ($)',
                    data: @json($salesData),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Order Status Chart
        new Chart(document.getElementById('orderChart'), {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Processing', 'Shipped', 'Delivered'],
                datasets: [{
                    data: [
                        {{ \App\Models\Order::where('status', 'pending')->count() }},
                        {{ \App\Models\Order::where('status', 'processing')->count() }},
                        {{ \App\Models\Order::where('status', 'shipped')->count() }},
                        {{ \App\Models\Order::where('status', 'delivered')->count() }}
                    ],
                    backgroundColor: [
                        'rgb(249, 115, 22)',
                        'rgb(59, 130, 246)',
                        'rgb(168, 85, 247)',
                        'rgb(34, 197, 94)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    </script>
    @endpush
</x-layouts.dashboard>
