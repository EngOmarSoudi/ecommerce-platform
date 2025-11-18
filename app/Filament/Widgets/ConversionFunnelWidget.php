<?php

namespace App\Filament\Widgets;

use App\Models\UserBehavior;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ConversionFunnelWidget extends BaseWidget
{
    protected static ?int $sort = 6;

    protected function getStats(): array
    {
        $from = now()->subDays(7);
        
        $views = UserBehavior::where('event_type', 'view')->where('event_at', '>=', $from)->count();
        $cartAdds = UserBehavior::where('event_type', 'cart_add')->where('event_at', '>=', $from)->count();
        $purchases = UserBehavior::where('event_type', 'purchase')->where('event_at', '>=', $from)->count();

        $viewToCart = $views ? round(($cartAdds / $views) * 100, 2) : 0;
        $cartToPurchase = $cartAdds ? round(($purchases / $cartAdds) * 100, 2) : 0;

        return [
            Stat::make('Views (7d)', $views)
                ->description('Cart adds: '.$cartAdds)
                ->color('primary'),
            Stat::make('View → Cart', $viewToCart.'%')
                ->description('Conversion rate')
                ->color('warning'),
            Stat::make('Cart → Purchase', $cartToPurchase.'%')
                ->description('Checkout conversion')
                ->color('success'),
        ];
    }
}
