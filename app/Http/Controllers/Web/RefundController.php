<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
    /**
     * Display refunds list
     */
    public function index(Request $request)
    {
        $query = Refund::query()->with('order');

        // Search filter
        if ($search = $request->input('search')) {
            $query->whereHas('order', function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $refunds = $query->latest()->paginate(20)->withQueryString();

        return view('refunds.index', compact('refunds'));
    }

    /**
     * Store refund
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:500',
            'refund_method' => 'required|in:original,store_credit,manual'
        ]);

        $order = Order::findOrFail($validated['order_id']);

        // Validate refund amount
        if ($validated['amount'] > $order->total_amount) {
            return back()->withErrors(['amount' => 'Refund amount cannot exceed order total.']);
        }

        // Check if refund already exists
        if ($order->refund) {
            return back()->withErrors(['order' => 'This order already has a refund.']);
        }

        DB::transaction(function() use ($validated, $order) {
            // Create refund
            $refund = Refund::create([
                'order_id' => $validated['order_id'],
                'amount' => $validated['amount'],
                'reason' => $validated['reason'],
                'refund_method' => $validated['refund_method'],
                'status' => 'completed',
                'processed_by' => auth()->id(),
                'processed_at' => now()
            ]);

            // Update order payment status
            $order->update([
                'payment_status' => 'refunded'
            ]);
        });

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Refund processed successfully!');
    }

    /**
     * Display refund receipt
     */
    public function receipt($id)
    {
        $refund = Refund::with('order')->findOrFail($id);
        return view('refunds.receipt', compact('refund'));
    }
}
