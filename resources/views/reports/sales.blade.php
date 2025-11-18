<!-- Sales Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Total Sales</p>
                <p class="text-2xl font-bold mt-1 dark:text-white">${{ number_format($data['total_sales'] ?? 0, 2) }}</p>
                <p class="text-xs text-green-600 mt-1">+{{ number_format($data['growth_rate'] ?? 0, 1) }}% vs last period</p>
            </div>
            <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-full">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Orders</p>
                <p class="text-2xl font-bold mt-1 dark:text-white">{{ number_format($data['total_orders'] ?? 0) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ number_format($data['avg_order_value'] ?? 0, 2) }} avg value</p>
            </div>
            <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-full">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Units Sold</p>
                <p class="text-2xl font-bold mt-1 dark:text-white">{{ number_format($data['units_sold'] ?? 0) }}</p>
                <p class="text-xs text-gray-500 mt-1">Across {{ $data['product_count'] ?? 0 }} products</p>
            </div>
            <div class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-full">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Conversion Rate</p>
                <p class="text-2xl font-bold mt-1 dark:text-white">{{ number_format($data['conversion_rate'] ?? 0, 1) }}%</p>
                <p class="text-xs text-gray-500 mt-1">{{ $data['visitors'] ?? 0 }} visitors</p>
            </div>
            <div class="p-3 bg-orange-100 dark:bg-orange-900/20 rounded-full">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="card">
        <h3 class="text-lg font-semibold mb-4 dark:text-white">Sales Trend</h3>
        <canvas id="salesTrendChart" height="250"></canvas>
    </div>
    
    <div class="card">
        <h3 class="text-lg font-semibold mb-4 dark:text-white">Sales by Category</h3>
        <canvas id="salesByCategoryChart" height="250"></canvas>
    </div>
</div>

<!-- Top Products Table -->
<div class="card">
    <h3 class="text-lg font-semibold mb-4 dark:text-white">Top Selling Products</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Units Sold</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avg Price</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($data['top_products'] ?? [] as $product)
                <tr>
                    <td class="px-6 py-4">{{ $product['name'] }}</td>
                    <td class="px-6 py-4">{{ number_format($product['quantity']) }}</td>
                    <td class="px-6 py-4 font-medium">${{ number_format($product['revenue'], 2) }}</td>
                    <td class="px-6 py-4">${{ number_format($product['avg_price'], 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">No data available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    // Sales Trend Chart
    new Chart(document.getElementById('salesTrendChart'), {
        type: 'line',
        data: {
            labels: @json($data['trend_labels'] ?? []),
            datasets: [{
                label: 'Sales ($)',
                data: @json($data['trend_data'] ?? []),
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

    // Sales by Category Chart
    new Chart(document.getElementById('salesByCategoryChart'), {
        type: 'doughnut',
        data: {
            labels: @json($data['category_labels'] ?? []),
            datasets: [{
                data: @json($data['category_data'] ?? []),
                backgroundColor: [
                    'rgb(59, 130, 246)',
                    'rgb(168, 85, 247)',
                    'rgb(34, 197, 94)',
                    'rgb(249, 115, 22)',
                    'rgb(236, 72, 153)'
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
