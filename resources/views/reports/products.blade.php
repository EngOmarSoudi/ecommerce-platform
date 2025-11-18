<!-- Product Performance Summary -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="card">
        <p class="text-sm text-gray-600 dark:text-gray-400">Best Seller</p>
        <p class="text-lg font-bold mt-1 dark:text-white">{{ $data['best_seller']['name'] ?? 'N/A' }}</p>
        <p class="text-xs text-gray-500 mt-1">{{ $data['best_seller']['units'] ?? 0 }} units sold</p>
    </div>
    
    <div class="card">
        <p class="text-sm text-gray-600 dark:text-gray-400">Highest Revenue</p>
        <p class="text-lg font-bold mt-1 dark:text-white">{{ $data['top_revenue']['name'] ?? 'N/A' }}</p>
        <p class="text-xs text-gray-500 mt-1">${{ number_format($data['top_revenue']['amount'] ?? 0, 2) }}</p>
    </div>
    
    <div class="card">
        <p class="text-sm text-gray-600 dark:text-gray-400">Slow Movers</p>
        <p class="text-2xl font-bold mt-1 text-red-600">{{ $data['slow_movers_count'] ?? 0 }}</p>
        <p class="text-xs text-gray-500 mt-1">Need attention</p>
    </div>
    
    <div class="card">
        <p class="text-sm text-gray-600 dark:text-gray-400">Out of Stock</p>
        <p class="text-2xl font-bold mt-1 text-orange-600">{{ $data['out_of_stock_count'] ?? 0 }}</p>
        <p class="text-xs text-gray-500 mt-1">Lost opportunities</p>
    </div>
</div>

<!-- Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="card">
        <h3 class="text-lg font-semibold mb-4 dark:text-white">Top 10 Products by Revenue</h3>
        <canvas id="topProductsChart" height="300"></canvas>
    </div>
    
    <div class="card">
        <h3 class="text-lg font-semibold mb-4 dark:text-white">Stock Status Distribution</h3>
        <canvas id="stockStatusChart" height="300"></canvas>
    </div>
</div>

<!-- Product Performance Table -->
<div class="card">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold dark:text-white">Product Performance Details</h3>
        <select id="sortBy" class="input w-48 text-sm">
            <option value="revenue">Sort by Revenue</option>
            <option value="quantity">Sort by Quantity</option>
            <option value="views">Sort by Views</option>
            <option value="conversion">Sort by Conversion</option>
        </select>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Units Sold</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Views</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Conv. Rate</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                @forelse($data['products'] ?? [] as $product)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ $product['image'] ?? '/assets/placeholder.png' }}" alt="{{ $product['name'] }}" class="w-10 h-10 rounded object-cover">
                            <div>
                                <div class="font-medium dark:text-white">{{ $product['name'] }}</div>
                                <div class="text-xs text-gray-500">{{ $product['sku'] }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-medium dark:text-white">{{ number_format($product['quantity'] ?? 0) }}</td>
                    <td class="px-6 py-4 font-medium dark:text-white">${{ number_format($product['revenue'] ?? 0, 2) }}</td>
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ number_format($product['views'] ?? 0) }}</td>
                    <td class="px-6 py-4">
                        <span class="text-sm {{ ($product['conversion_rate'] ?? 0) > 5 ? 'text-green-600' : 'text-gray-600' }}">
                            {{ number_format($product['conversion_rate'] ?? 0, 1) }}%
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="{{ ($product['stock'] ?? 0) < 10 ? 'text-red-600 font-bold' : 'text-gray-900 dark:text-white' }}">
                            {{ $product['stock'] ?? 0 }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if(($product['stock'] ?? 0) == 0)
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Out of Stock</span>
                        @elseif(($product['stock'] ?? 0) < 10)
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Low Stock</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">In Stock</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">No data available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    // Top Products Chart
    new Chart(document.getElementById('topProductsChart'), {
        type: 'bar',
        data: {
            labels: @json($data['top_10_labels'] ?? []),
            datasets: [{
                label: 'Revenue ($)',
                data: @json($data['top_10_data'] ?? []),
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            scales: {
                x: { beginAtZero: true }
            }
        }
    });

    // Stock Status Chart
    new Chart(document.getElementById('stockStatusChart'), {
        type: 'pie',
        data: {
            labels: ['In Stock', 'Low Stock', 'Out of Stock'],
            datasets: [{
                data: @json($data['stock_distribution'] ?? [0, 0, 0]),
                backgroundColor: [
                    'rgb(34, 197, 94)',
                    'rgb(234, 179, 8)',
                    'rgb(239, 68, 68)'
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
