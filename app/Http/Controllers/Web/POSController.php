<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class POSController extends Controller
{
    public function index()
    {
        return view('pos.index');
    }

    public function receipt()
    {
        return view('pos.receipt');
    }

    public function createOrder(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'customer_name' => 'nullable|string|max:255',
            'payment_method' => 'required|in:cash,card',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'cash_received' => 'nullable|numeric',
            'change' => 'nullable|numeric',
        ]);

        // In production: Create order in database, process payment, update inventory
        // For now, return success
        
        return response()->json([
            'success' => true,
            'order_number' => 'POS' . time(),
            'message' => 'Order created successfully'
        ]);
    }
}
