<!-- Profit Summary -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="card">
        <p class="text-sm text-gray-600 dark:text-gray-400">Gross Profit</p>
        <p class="text-2xl font-bold mt-1 text-green-600">${{ number_format($data['gross_profit'] ?? 0, 2) }}</p>
        <p class="text-xs text-gray-500 mt-1">{{ number_format(($data['profit_margin'] ?? 0), 1) }}% margin</p>
    </div>
    
    <div class="card">
        <p class="text-sm text-gray-600 dark:text-gray-400">Total Costs</p>
        <p class="text-2xl font-bold mt-1 text-red-600">${{ number_format($data['total_costs'] ?? 0, 2) }}</p>
        <p class="text-xs text-gray-500 mt-1">COGS + Expenses</p>
    </div>
    
    <div class="card">
        <p class="text-sm text-gray-600 dark:text-gray-400">Net Profit</p>
        <p class="text-2xl font-bold mt-1 dark:text-white">${{ number_format($data['net_profit'] ?? 0, 2) }}</p>
        <p class="text-xs text-gray-500 mt-1">{{ number_format(($data['net_margin'] ?? 0), 1) }}% margin</p>
    </div>
</div>

<!-- Profit Chart -->
<div class="card mb-6">
    <h3 class="text-lg font-semibold mb-4 dark:text-white">Profit vs Revenue</h3>
    <canvas id="profitChart" height="300"></canvas>
</div>

<!-- Breakdown Table -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card">
        <h3 class="text-lg font-semibold mb-4 dark:text-white">Revenue Breakdown</h3>
        <div class="space-y-3">
            <div class="flex justify-between py-2 border-b dark:border-gray-700">
                <span class="text-gray-600 dark:text-gray-400">Product Sales</span>
                <span class="font-medium dark:text-white">${{ number_format($data['product_revenue'] ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between py-2 border-b dark:border-gray-700">
                <span class="text-gray-600 dark:text-gray-400">Shipping Fees</span>
                <span class="font-medium dark:text-white">${{ number_format($data['shipping_revenue'] ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between py-2 border-b dark:border-gray-700">
                <span class="text-gray-600 dark:text-gray-400">Tax Collected</span>
                <span class="font-medium dark:text-white">${{ number_format($data['tax_collected'] ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between py-2 font-bold text-lg">
                <span class="dark:text-white">Total Revenue</span>
                <span class="text-green-600">${{ number_format($data['total_revenue'] ?? 0, 2) }}</span>
            </div>
        </div>
    </div>
    
    <div class="card">
        <h3 class="text-lg font-semibold mb-4 dark:text-white">Cost Breakdown</h3>
        <div class="space-y-3">
            <div class="flex justify-between py-2 border-b dark:border-gray-700">
                <span class="text-gray-600 dark:text-gray-400">Cost of Goods</span>
                <span class="font-medium dark:text-white">${{ number_format($data['cogs'] ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between py-2 border-b dark:border-gray-700">
                <span class="text-gray-600 dark:text-gray-400">Shipping Costs</span>
                <span class="font-medium dark:text-white">${{ number_format($data['shipping_costs'] ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between py-2 border-b dark:border-gray-700">
                <span class="text-gray-600 dark:text-gray-400">Refunds</span>
                <span class="font-medium dark:text-white">${{ number_format($data['refunds'] ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between py-2 font-bold text-lg">
                <span class="dark:text-white">Total Costs</span>
                <span class="text-red-600">${{ number_format($data['total_costs'] ?? 0, 2) }}</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    new Chart(document.getElementById('profitChart'), {
        type: 'bar',
        data: {
            labels: @json($data['trend_labels'] ?? []),
            datasets: [
                {
                    label: 'Revenue',
                    data: @json($data['revenue_trend'] ?? []),
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                },
                {
                    label: 'Profit',
                    data: @json($data['profit_trend'] ?? []),
                    backgroundColor: 'rgba(34, 197, 94, 0.5)',
                    borderColor: 'rgb(34, 197, 94)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endpush
