<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Revenue Overview';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = $this->getRevenuePerMonth();

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data['revenue'],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
            ],
            'labels' => $data['months'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getRevenuePerMonth(): array
    {
        $months = collect(range(1, 12))->map(function ($month) {
            return Carbon::create(null, $month, 1)->format('M');
        })->toArray();

        $revenue = collect(range(1, 12))->map(function ($month) {
            return Order::where('payment_status', 'paid')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount');
        })->toArray();

        return [
            'months' => $months,
            'revenue' => $revenue,
        ];
    }
}
