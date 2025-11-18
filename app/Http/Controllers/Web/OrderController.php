<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OrderController extends Controller
{
    /**
     * Display orders list with filters
     */
    public function index(Request $request)
    {
        $query = Order::query()->with(['items', 'customer']);

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Payment status filter
        if ($payment = $request->input('payment')) {
            $query->where('payment_status', $payment);
        }

        // Date range filter
        if ($startDate = $request->input('start_date')) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        // Get stats (cached for 5 minutes)
        $stats = Cache::remember('order_stats', 300, function() {
            return [
                'total' => Order::count(),
                'pending' => Order::where('status', 'pending')->count(),
                'processing' => Order::where('status', 'processing')->count(),
                'delivered' => Order::where('status', 'delivered')->count(),
            ];
        });

        $orders = $query->latest()->paginate(20)->withQueryString();

        return view('orders.index', compact('orders', 'stats'));
    }

    /**
     * Display order details
     */
    public function show($id)
    {
        // Cache individual order for 10 minutes
        $order = Cache::remember("order:{$id}", 600, function() use ($id) {
            return Order::with([
                'items.product',
                'shipments',
                'shippingAddress',
                'billingAddress',
                'refund'
            ])->findOrFail($id);
        });

        return view('orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $order = Order::findOrFail($id);
        $order->update([
            'status' => $validated['status'],
            $validated['status'] . '_at' => now()
        ]);

        Cache::forget("order:{$id}");
        Cache::forget('order_stats');

        return redirect()->back()->with('success', 'Order status updated successfully!');
    }
}
