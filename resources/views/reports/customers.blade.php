<!-- Customer Summary -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="card">
        <p class="text-sm text-gray-600 dark:text-gray-400">Total Customers</p>
        <p class="text-2xl font-bold mt-1 dark:text-white">{{ number_format($data['total_customers'] ?? 0) }}</p>
        <p class="text-xs text-green-600 mt-1">+{{ $data['new_customers'] ?? 0 }} new this period</p>
    </div>
    
    <div class="card">
        <p class="text-sm text-gray-600 dark:text-gray-400">Avg Customer Value</p>
        <p class="text-2xl font-bold mt-1 dark:text-white">${{ number_format($data['avg_customer_value'] ?? 0, 2) }}</p>
        <p class="text-xs text-gray-500 mt-1">Lifetime value</p>
    </div>
    
    <div class="card">
        <p class="text-sm text-gray-600 dark:text-gray-400">Repeat Rate</p>
        <p class="text-2xl font-bold mt-1 dark:text-white">{{ number_format($data['repeat_rate'] ?? 0, 1) }}%</p>
        <p class="text-xs text-gray-500 mt-1">{{ $data['repeat_customers'] ?? 0 }} repeat buyers</p>
    </div>
    
    <div class="card">
        <p class="text-sm text-gray-600 dark:text-gray-400">Churn Rate</p>
        <p class="text-2xl font-bold mt-1 {{ ($data['churn_rate'] ?? 0) > 20 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($data['churn_rate'] ?? 0, 1) }}%</p>
        <p class="text-xs text-gray-500 mt-1">Last 90 days</p>
    </div>
</div>

<!-- Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="card">
        <h3 class="text-lg font-semibold mb-4 dark:text-white">Customer Acquisition Trend</h3>
        <canvas id="customerAcquisitionChart" height="300"></canvas>
    </div>
    
    <div class="card">
        <h3 class="text-lg font-semibold mb-4 dark:text-white">Customer Segments</h3>
        <canvas id="customerSegmentChart" height="300"></canvas>
    </div>
</div>

<!-- Top Customers Table -->
<div class="card mb-6">
    <h3 class="text-lg font-semibold mb-4 dark:text-white">Top Customers by Spending</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Orders</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Spent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avg Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                @forelse($data['top_customers'] ?? [] as $customer)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4">
                        <div>
                            <div class="font-medium dark:text-white">{{ $customer['name'] }}</div>
                            <div class="text-xs text-gray-500">{{ $customer['email'] }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-medium dark:text-white">{{ $customer['order_count'] }}</td>
                    <td class="px-6 py-4 font-medium dark:text-white">${{ number_format($customer['total_spent'], 2) }}</td>
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">${{ number_format($customer['avg_order'], 2) }}</td>
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $customer['last_order_date'] }}</td>
                    <td class="px-6 py-4">
                        @if($customer['order_count'] >= 10)
                            <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">VIP</span>
                        @elseif($customer['order_count'] >= 3)
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Loyal</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Regular</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">No data available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Customer Geography -->
<div class="card">
    <h3 class="text-lg font-semibold mb-4 dark:text-white">Top Locations</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach(($data['top_locations'] ?? []) as $location)
        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
            <div>
                <p class="font-medium dark:text-white">{{ $location['city'] ?? 'Unknown' }}, {{ $location['state'] ?? 'N/A' }}</p>
                <p class="text-sm text-gray-500">{{ $location['customer_count'] }} customers</p>
            </div>
            <div class="text-right">
                <p class="font-bold text-lg dark:text-white">${{ number_format($location['revenue'], 2) }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
    // Customer Acquisition Chart
    new Chart(document.getElementById('customerAcquisitionChart'), {
        type: 'line',
        data: {
            labels: @json($data['acquisition_labels'] ?? []),
            datasets: [{
                label: 'New Customers',
                data: @json($data['acquisition_data'] ?? []),
                borderColor: 'rgb(168, 85, 247)',
                backgroundColor: 'rgba(168, 85, 247, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Customer Segment Chart
    new Chart(document.getElementById('customerSegmentChart'), {
        type: 'doughnut',
        data: {
            labels: ['VIP (10+ orders)', 'Loyal (3-9 orders)', 'Regular (1-2 orders)', 'Inactive'],
            datasets: [{
                data: @json($data['segment_data'] ?? [0, 0, 0, 0]),
                backgroundColor: [
                    'rgb(168, 85, 247)',
                    'rgb(59, 130, 246)',
                    'rgb(156, 163, 175)',
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
