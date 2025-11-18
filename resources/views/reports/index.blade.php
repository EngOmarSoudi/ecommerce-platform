<x-layouts.dashboard>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reports & Analytics</h1>
        <p class="text-gray-600 dark:text-gray-400">Business insights and performance metrics</p>
    </div>

    <!-- Report Type Tabs -->
    <div class="mb-6" x-data="{ activeTab: '{{ request('tab', 'sales') }}' }">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8 overflow-x-auto">
                <button 
                    @click="activeTab = 'sales'; window.location.search = '?tab=sales'"
                    :class="activeTab === 'sales' ? 'border-brand.primary text-brand.primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Sales Report
                </button>
                <button 
                    @click="activeTab = 'profit'; window.location.search = '?tab=profit'"
                    :class="activeTab === 'profit' ? 'border-brand.primary text-brand.primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Profit & Loss
                </button>
                <button 
                    @click="activeTab = 'products'; window.location.search = '?tab=products'"
                    :class="activeTab === 'products' ? 'border-brand.primary text-brand.primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Product Performance
                </button>
                <button 
                    @click="activeTab = 'customers'; window.location.search = '?tab=customers'"
                    :class="activeTab === 'customers' ? 'border-brand.primary text-brand.primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Customer Insights
                </button>
            </nav>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300">Start Date</label>
                <input type="date" id="startDate" value="{{ request('start_date', now()->subMonth()->format('Y-m-d')) }}" class="input w-full">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300">End Date</label>
                <input type="date" id="endDate" value="{{ request('end_date', now()->format('Y-m-d')) }}" class="input w-full">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300">Quick Select</label>
                <select id="quickDateRange" class="input w-full">
                    <option value="">Custom Range</option>
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="last7days">Last 7 Days</option>
                    <option value="last30days">Last 30 Days</option>
                    <option value="thismonth">This Month</option>
                    <option value="lastmonth">Last Month</option>
                    <option value="thisyear">This Year</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button onclick="applyDateFilter()" class="btn-primary flex-1">Apply</button>
                <button onclick="exportReport()" class="btn-outline">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </button>
            </div>
        </div>
    </div>

    @if(request('tab', 'sales') === 'sales')
        @include('reports.sales')
    @elseif(request('tab') === 'profit')
        @include('reports.profit')
    @elseif(request('tab') === 'products')
        @include('reports.products')
    @elseif(request('tab') === 'customers')
        @include('reports.customers')
    @endif

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Quick date range selector
        document.getElementById('quickDateRange').addEventListener('change', function() {
            const value = this.value;
            const startDate = document.getElementById('startDate');
            const endDate = document.getElementById('endDate');
            const now = new Date();
            
            let start, end;
            switch(value) {
                case 'today':
                    start = end = now;
                    break;
                case 'yesterday':
                    start = end = new Date(now.setDate(now.getDate() - 1));
                    break;
                case 'last7days':
                    start = new Date(now.setDate(now.getDate() - 7));
                    end = new Date();
                    break;
                case 'last30days':
                    start = new Date(now.setDate(now.getDate() - 30));
                    end = new Date();
                    break;
                case 'thismonth':
                    start = new Date(now.getFullYear(), now.getMonth(), 1);
                    end = new Date();
                    break;
                case 'lastmonth':
                    start = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                    end = new Date(now.getFullYear(), now.getMonth(), 0);
                    break;
                case 'thisyear':
                    start = new Date(now.getFullYear(), 0, 1);
                    end = new Date();
                    break;
            }
            
            if (start && end) {
                startDate.value = start.toISOString().split('T')[0];
                endDate.value = end.toISOString().split('T')[0];
                applyDateFilter();
            }
        });

        function applyDateFilter() {
            const start = document.getElementById('startDate').value;
            const end = document.getElementById('endDate').value;
            const tab = new URLSearchParams(window.location.search).get('tab') || 'sales';
            window.location.search = `?tab=${tab}&start_date=${start}&end_date=${end}`;
        }

        function exportReport() {
            const params = new URLSearchParams(window.location.search);
            params.set('export', 'pdf');
            window.location.href = '{{ route("reports.index") }}?' + params.toString();
        }
    </script>
    @endpush
</x-layouts.dashboard>
