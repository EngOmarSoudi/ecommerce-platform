<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'sales');
        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Check for export request
        if ($request->has('export')) {
            return $this->exportReport($tab, $startDate, $endDate);
        }

        $data = match($tab) {
            'sales' => $this->getSalesData($startDate, $endDate),
            'profit' => $this->getProfitData($startDate, $endDate),
            'products' => $this->getProductsData($startDate, $endDate),
            'customers' => $this->getCustomersData($startDate, $endDate),
            default => $this->getSalesData($startDate, $endDate),
        };

        return view('reports.index', compact('data'));
    }

    private function getSalesData($startDate, $endDate)
    {
        $cacheKey = "report_sales_{$startDate}_{$endDate}";
        
        return Cache::remember($cacheKey, 600, function() use ($startDate, $endDate) {
            $orders = Order::whereBetween('created_at', [$startDate, $endDate])
                          ->where('payment_status', 'paid')
                          ->get();

            $totalSales = $orders->sum('total_amount');
            $totalOrders = $orders->count();
            
            // Get previous period for growth calculation
            $previousOrders = Order::whereBetween('created_at', [
                Carbon::parse($startDate)->subDays(30),
                Carbon::parse($startDate)
            ])->where('payment_status', 'paid')->sum('total_amount');
            
            $growthRate = $previousOrders > 0 ? (($totalSales - $previousOrders) / $previousOrders) * 100 : 0;

            // Top products
            $topProducts = OrderItem::select('products.name', DB::raw('SUM(quantity) as quantity'), DB::raw('SUM(price * quantity) as revenue'), DB::raw('AVG(price) as avg_price'))
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->where('orders.payment_status', 'paid')
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('revenue')
                ->limit(10)
                ->get()
                ->toArray();

            // Trend data (daily)
            $trendData = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('payment_status', 'paid')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Sales by category
            $categoryData = OrderItem::select('categories.name', DB::raw('SUM(order_items.price * order_items.quantity) as total'))
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->where('orders.payment_status', 'paid')
                ->groupBy('categories.id', 'categories.name')
                ->get();

            return [
                'total_sales' => $totalSales,
                'total_orders' => $totalOrders,
                'avg_order_value' => $totalOrders > 0 ? $totalSales / $totalOrders : 0,
                'units_sold' => $orders->sum(fn($o) => $o->items->sum('quantity')),
                'product_count' => OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
                                            ->whereBetween('orders.created_at', [$startDate, $endDate])
                                            ->distinct('product_id')->count(),
                'conversion_rate' => 12.5, // Mock data - integrate with analytics
                'visitors' => 2500, // Mock data - integrate with analytics
                'growth_rate' => $growthRate,
                'top_products' => $topProducts,
                'trend_labels' => $trendData->pluck('date')->toArray(),
                'trend_data' => $trendData->pluck('total')->toArray(),
                'category_labels' => $categoryData->pluck('name')->toArray(),
                'category_data' => $categoryData->pluck('total')->toArray(),
            ];
        });
    }

    private function getProfitData($startDate, $endDate)
    {
        $cacheKey = "report_profit_{$startDate}_{$endDate}";
        
        return Cache::remember($cacheKey, 600, function() use ($startDate, $endDate) {
            $orders = Order::with('items.product')
                          ->whereBetween('created_at', [$startDate, $endDate])
                          ->where('payment_status', 'paid')
                          ->get();

            $totalRevenue = $orders->sum('total_amount');
            $cogs = $orders->sum(fn($o) => $o->items->sum(fn($i) => ($i->product->cost ?? 0) * $i->quantity));
            $grossProfit = $totalRevenue - $cogs;
            
            // Mock expense data
            $shippingCosts = $orders->sum('shipping_fee');
            $refunds = \App\Models\Refund::whereBetween('created_at', [$startDate, $endDate])->sum('amount');
            $totalCosts = $cogs + $shippingCosts + $refunds;
            $netProfit = $totalRevenue - $totalCosts;

            // Trend data
            $trendData = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('payment_status', 'paid')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $profitTrend = $trendData->map(function($item) use ($cogs, $totalRevenue) {
                $ratio = $totalRevenue > 0 ? $cogs / $totalRevenue : 0;
                return $item->revenue - ($item->revenue * $ratio);
            });

            return [
                'gross_profit' => $grossProfit,
                'profit_margin' => $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0,
                'total_costs' => $totalCosts,
                'net_profit' => $netProfit,
                'net_margin' => $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0,
                'product_revenue' => $totalRevenue - $shippingCosts,
                'shipping_revenue' => $shippingCosts,
                'tax_collected' => $orders->sum('tax_amount'),
                'total_revenue' => $totalRevenue,
                'cogs' => $cogs,
                'shipping_costs' => $shippingCosts,
                'refunds' => $refunds,
                'trend_labels' => $trendData->pluck('date')->toArray(),
                'revenue_trend' => $trendData->pluck('revenue')->toArray(),
                'profit_trend' => $profitTrend->toArray(),
            ];
        });
    }

    private function getProductsData($startDate, $endDate)
    {
        $cacheKey = "report_products_{$startDate}_{$endDate}";
        
        return Cache::remember($cacheKey, 600, function() use ($startDate, $endDate) {
            $products = OrderItem::select(
                    'products.id',
                    'products.name',
                    'products.sku',
                    'products.image_url',
                    'products.stock_quantity',
                    DB::raw('SUM(order_items.quantity) as quantity'),
                    DB::raw('SUM(order_items.price * order_items.quantity) as revenue')
                )
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->where('orders.payment_status', 'paid')
                ->groupBy('products.id', 'products.name', 'products.sku', 'products.image_url', 'products.stock_quantity')
                ->orderByDesc('revenue')
                ->get()
                ->map(function($product) {
                    $product->views = rand(100, 5000); // Mock - integrate with analytics
                    $product->conversion_rate = rand(10, 150) / 10; // Mock
                    $product->image = $product->image_url;
                    $product->stock = $product->stock_quantity;
                    return $product;
                });

            $bestSeller = $products->sortByDesc('quantity')->first();
            $topRevenue = $products->sortByDesc('revenue')->first();

            $stockDistribution = [
                Product::whereRaw('stock_quantity >= low_stock_threshold')->count(),
                Product::where('stock_quantity', '>', 0)->whereRaw('stock_quantity < low_stock_threshold')->count(),
                Product::where('stock_quantity', 0)->count(),
            ];

            return [
                'best_seller' => [
                    'name' => $bestSeller->name ?? 'N/A',
                    'units' => $bestSeller->quantity ?? 0,
                ],
                'top_revenue' => [
                    'name' => $topRevenue->name ?? 'N/A',
                    'amount' => $topRevenue->revenue ?? 0,
                ],
                'slow_movers_count' => Product::whereRaw('stock_quantity > 0')->whereDoesntHave('orderItems')->count(),
                'out_of_stock_count' => Product::where('stock_quantity', 0)->count(),
                'products' => $products->toArray(),
                'top_10_labels' => $products->take(10)->pluck('name')->toArray(),
                'top_10_data' => $products->take(10)->pluck('revenue')->toArray(),
                'stock_distribution' => $stockDistribution,
            ];
        });
    }

    private function getCustomersData($startDate, $endDate)
    {
        $cacheKey = "report_customers_{$startDate}_{$endDate}";
        
        return Cache::remember($cacheKey, 600, function() use ($startDate, $endDate) {
            $customers = User::where('role', 'customer')
                ->withCount(['orders' => fn($q) => $q->whereBetween('created_at', [$startDate, $endDate])])
                ->withSum(['orders' => fn($q) => $q->whereBetween('created_at', [$startDate, $endDate])], 'total_amount')
                ->having('orders_count', '>', 0)
                ->get();

            $topCustomers = $customers->sortByDesc('orders_sum_total_amount')
                ->take(10)
                ->map(fn($c) => [
                    'name' => $c->name,
                    'email' => $c->email,
                    'order_count' => $c->orders_count,
                    'total_spent' => $c->orders_sum_total_amount ?? 0,
                    'avg_order' => $c->orders_count > 0 ? ($c->orders_sum_total_amount / $c->orders_count) : 0,
                    'last_order_date' => $c->orders()->latest()->first()->created_at->format('M d, Y') ?? 'N/A',
                ])
                ->toArray();

            $newCustomers = User::where('role', 'customer')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $repeatCustomers = User::where('role', 'customer')
                ->has('orders', '>=', 2)
                ->count();

            $totalCustomers = User::where('role', 'customer')->count();

            // Customer segments
            $segments = [
                User::where('role', 'customer')->has('orders', '>=', 10)->count(),
                User::where('role', 'customer')->has('orders', '>=', 3)->has('orders', '<', 10)->count(),
                User::where('role', 'customer')->has('orders', '>=', 1)->has('orders', '<', 3)->count(),
                User::where('role', 'customer')->doesntHave('orders')->count(),
            ];

            // Acquisition trend
            $acquisitionData = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('role', 'customer')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return [
                'total_customers' => $totalCustomers,
                'new_customers' => $newCustomers,
                'avg_customer_value' => $customers->avg('orders_sum_total_amount') ?? 0,
                'repeat_rate' => $totalCustomers > 0 ? ($repeatCustomers / $totalCustomers) * 100 : 0,
                'repeat_customers' => $repeatCustomers,
                'churn_rate' => 15.5, // Mock - calculate based on last purchase date
                'top_customers' => $topCustomers,
                'acquisition_labels' => $acquisitionData->pluck('date')->toArray(),
                'acquisition_data' => $acquisitionData->pluck('count')->toArray(),
                'segment_data' => $segments,
                'top_locations' => [], // Mock - would need addresses table
            ];
        });
    }

    private function exportReport($tab, $startDate, $endDate)
    {
        $data = match($tab) {
            'sales' => $this->getSalesData($startDate, $endDate),
            'profit' => $this->getProfitData($startDate, $endDate),
            'products' => $this->getProductsData($startDate, $endDate),
            'customers' => $this->getCustomersData($startDate, $endDate),
            default => $this->getSalesData($startDate, $endDate),
        };

        // Generate CSV
        $csv = $this->generateCSV($tab, $data);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$tab}-report-{$startDate}-to-{$endDate}.csv\"");
    }

    private function generateCSV($tab, $data)
    {
        $csv = '';
        
        if ($tab === 'sales') {
            $csv = "Product Name,Units Sold,Revenue,Average Price\n";
            foreach ($data['top_products'] as $product) {
                $csv .= sprintf("\"%s\",%d,%.2f,%.2f\n", 
                    $product['name'], 
                    $product['quantity'], 
                    $product['revenue'], 
                    $product['avg_price']
                );
            }
        } elseif ($tab === 'products') {
            $csv = "Product Name,SKU,Units Sold,Revenue,Stock,Status\n";
            foreach ($data['products'] as $product) {
                $status = $product['stock'] == 0 ? 'Out of Stock' : ($product['stock'] < 10 ? 'Low Stock' : 'In Stock');
                $csv .= sprintf("\"%s\",\"%s\",%d,%.2f,%d,\"%s\"\n",
                    $product['name'],
                    $product['sku'],
                    $product['quantity'],
                    $product['revenue'],
                    $product['stock'],
                    $status
                );
            }
        } elseif ($tab === 'customers') {
            $csv = "Customer Name,Email,Orders,Total Spent,Avg Order,Last Order\n";
            foreach ($data['top_customers'] as $customer) {
                $csv .= sprintf("\"%s\",\"%s\",%d,%.2f,%.2f,\"%s\"\n",
                    $customer['name'],
                    $customer['email'],
                    $customer['order_count'],
                    $customer['total_spent'],
                    $customer['avg_order'],
                    $customer['last_order_date']
                );
            }
        }

        return $csv;
    }
}
