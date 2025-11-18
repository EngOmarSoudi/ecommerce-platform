<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class SalesByDayWidget extends ChartWidget
{
    protected static ?string $heading = 'Sales by Day (30d)';
    protected static ?int $sort = 5;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $labels = [];
        $data = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $labels[] = $day->format('M d');
            $data[] = (float) Order::where('payment_status', 'paid')
                ->whereDate('created_at', $day->toDateString())
                ->sum('total_amount');
        }

        return [
            'datasets' => [[
                'label' => 'Revenue',
                'data' => $data,
                'backgroundColor' => 'rgba(16, 185, 129, 0.3)',
                'borderColor' => 'rgb(16, 185, 129)',
            ]],
            'labels' => $labels,
        ];
    }
}
