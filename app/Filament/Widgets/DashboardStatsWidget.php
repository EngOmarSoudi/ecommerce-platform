<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalRevenue = Order::where('payment_status', 'paid')
            ->sum('total_amount');

        $todayRevenue = Order::where('payment_status', 'paid')
            ->whereDate('created_at', today())
            ->sum('total_amount');

        $pendingOrders = Order::whereIn('status', ['pending', 'processing'])
            ->count();

        $totalOrders = Order::count();
        
        $totalProducts = Product::where('is_active', true)->count();
        
        $totalCustomers = User::where('role', 'customer')->count();

        return [
            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 2))
                ->description('Today: $' . number_format($todayRevenue, 2))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 12, 18, 15, 22, 28, 35]),

            Stat::make('Pending Orders', $pendingOrders)
                ->description($totalOrders . ' total orders')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('warning'),

            Stat::make('Active Products', $totalProducts)
                ->description('Listed products')
                ->descriptionIcon('heroicon-m-cube')
                ->color('info'),

            Stat::make('Total Customers', $totalCustomers)
                ->description('Registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
        ];
    }
}
