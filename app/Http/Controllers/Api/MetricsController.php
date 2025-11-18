<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class MetricsController extends Controller
{
    public function index(): JsonResponse
    {
        $metrics = [
            'app' => [
                'name' => config('app.name'),
                'env' => config('app.env'),
                'version' => app()->version(),
            ],
            'counts' => [
                'users' => User::count(),
                'products' => Product::count(),
                'orders' => Order::count(),
                'orders_paid' => Order::where('payment_status', 'paid')->count(),
            ],
            'sales' => [
                'revenue_total' => (float) Order::where('payment_status', 'paid')->sum('total_amount'),
                'revenue_today' => (float) Order::where('payment_status', 'paid')->whereDate('created_at', today())->sum('total_amount'),
            ],
            'timestamp' => now()->toIso8601String(),
        ];

        return response()->json($metrics);
    }
}
