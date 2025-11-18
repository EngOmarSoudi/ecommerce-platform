<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    protected OrderService $orderService;
    protected CartService $cartService;

    public function __construct(OrderService $orderService, CartService $cartService)
    {
        $this->orderService = $orderService;
        $this->cartService = $cartService;
    }

    /**
     * Get user's orders
     */
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['items', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($orders);
    }

    /**
     * Get order details
     */
    public function show(Request $request, int $orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', $request->user()->id)
            ->with(['items.sku.product', 'payment', 'shippingAddress', 'billingAddress'])
            ->firstOrFail();

        return response()->json(['data' => $order]);
    }

    /**
     * Create order from cart
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:cash,card,stripe,apple_pay,stc_pay',
            'shipping_address_id' => 'required|exists:addresses,id',
            'billing_address_id' => 'required|exists:addresses,id',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $cart = $this->cartService->getCart();

        if ($cart->items->count() === 0) {
            return response()->json([
                'message' => 'Cart is empty'
            ], 400);
        }

        try {
            $order = $this->orderService->createOrder($cart, [
                'user_id' => $request->user()->id,
                'payment_method' => $request->input('payment_method'),
                'shipping_address_id' => $request->input('shipping_address_id'),
                'billing_address_id' => $request->input('billing_address_id'),
                'notes' => $request->input('notes'),
                'email' => $request->user()->email,
            ]);

            return response()->json([
                'message' => 'Order created successfully',
                'data' => $order,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Order creation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process payment webhook (Stripe/other gateways)
     */
    public function webhook(Request $request)
    {
        $payload = $request->all();
        $signature = $request->header('Stripe-Signature') ?? $request->header('X-Webhook-Signature');

        try {
            $result = $this->orderService->processPaymentWebhook($payload, $signature);

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
