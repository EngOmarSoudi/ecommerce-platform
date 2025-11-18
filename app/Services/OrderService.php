<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\StockLevel;
use App\Models\StockMovement;
use App\Models\Address;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class OrderService
{
    protected PaymentGatewayInterface $paymentGateway;

    public function __construct(PaymentGatewayInterface $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    /**
     * Create order with transactional guarantees
     * Workflow: Reserve stock → Create order → Process payment → Confirm order
     */
    public function createOrder(Cart $cart, array $orderData): Order
    {
        return DB::transaction(function () use ($cart, $orderData) {
            // Step 1: Reserve stock (stock holds)
            $stockReservations = $this->reserveStock($cart);

            try {
                // Step 2: Create order
                $order = $this->createOrderRecord($cart, $orderData, $stockReservations);

                // Step 3: Process payment
                if ($orderData['payment_method'] !== 'cash') {
                    $payment = $this->processPayment($order, $orderData);
                    
                    if (!$payment || $payment->status === 'failed') {
                        throw new Exception('Payment failed');
                    }
                }

                // Step 4: Confirm order and commit stock
                $this->confirmOrder($order, $stockReservations);

                // Clear cart
                $cart->items()->delete();
                $cart->update(['total_amount' => 0]);

                return $order->load(['items', 'payment', 'shippingAddress', 'billingAddress']);

            } catch (Exception $e) {
                // Rollback stock reservations
                $this->releaseStockReservations($stockReservations);
                throw $e;
            }
        });
    }

    /**
     * Reserve stock for cart items
     */
    protected function reserveStock(Cart $cart): array
    {
        $reservations = [];

        foreach ($cart->items as $item) {
            $stockLevel = StockLevel::where('sku_id', $item->sku_id)
                ->lockForUpdate()
                ->first();

            if (!$stockLevel || $stockLevel->available_quantity < $item->quantity) {
                throw new Exception("Insufficient stock for SKU: {$item->sku->name}");
            }

            // Reserve stock
            $stockLevel->available_quantity -= $item->quantity;
            $stockLevel->reserved_quantity += $item->quantity;
            $stockLevel->save();

            $reservations[] = [
                'stock_level_id' => $stockLevel->id,
                'sku_id' => $item->sku_id,
                'quantity' => $item->quantity,
            ];
        }

        return $reservations;
    }

    /**
     * Create order record
     */
    protected function createOrderRecord(Cart $cart, array $orderData, array $stockReservations): Order
    {
        // Calculate totals
        $subtotal = $cart->items->sum('total_amount');
        $taxRate = 0.10;
        $tax = $subtotal * $taxRate;
        $shipping = $subtotal > 100 ? 0 : 10;
        $total = $subtotal + $tax + $shipping;

        // Apply coupon if provided
        if (isset($orderData['coupon_code'])) {
            // TODO: Implement coupon logic
        }

        $order = Order::create([
            'user_id' => $orderData['user_id'] ?? null,
            'order_number' => $this->generateOrderNumber(),
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => $orderData['payment_method'],
            'shipping_address_id' => $orderData['shipping_address_id'] ?? null,
            'billing_address_id' => $orderData['billing_address_id'] ?? null,
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'shipping_amount' => $shipping,
            'discount_amount' => 0,
            'total_amount' => $total,
            'currency' => 'USD',
            'notes' => $orderData['notes'] ?? null,
        ]);

        // Create order items
        foreach ($cart->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'sku_id' => $item->sku_id,
                'product_name' => $item->sku->product->name,
                'sku_name' => $item->sku->name,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'total_amount' => $item->total_amount,
            ]);
        }

        return $order;
    }

    /**
     * Process payment
     */
    protected function processPayment(Order $order, array $orderData): Payment
    {
        $paymentData = [
            'amount' => $order->total_amount,
            'currency' => $order->currency,
            'description' => "Order #{$order->order_number}",
            'email' => $orderData['email'] ?? null,
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ],
        ];

        $result = $this->paymentGateway->createPayment($paymentData);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method' => $order->payment_method,
            'amount' => $order->total_amount,
            'currency' => $order->currency,
            'status' => $result['status'] === 'succeeded' ? 'completed' : 'pending',
            'transaction_id' => $result['payment_id'],
            'gateway_response' => json_encode($result),
        ]);

        // Update order payment status
        $order->update([
            'payment_status' => $payment->status === 'completed' ? 'paid' : 'pending',
        ]);

        return $payment;
    }

    /**
     * Confirm order and commit stock movements
     */
    protected function confirmOrder(Order $order, array $stockReservations): void
    {
        foreach ($stockReservations as $reservation) {
            $stockLevel = StockLevel::find($reservation['stock_level_id']);
            
            // Move from reserved to committed
            $stockLevel->reserved_quantity -= $reservation['quantity'];
            $stockLevel->save();

            // Create stock movement record
            StockMovement::create([
                'sku_id' => $reservation['sku_id'],
                'warehouse_id' => $stockLevel->warehouse_id,
                'type' => 'sale',
                'quantity' => -$reservation['quantity'],
                'reference_type' => 'order',
                'reference_id' => $order->id,
                'notes' => "Order #{$order->order_number}",
            ]);
        }

        $order->update(['status' => 'confirmed']);
    }

    /**
     * Release stock reservations (on failure)
     */
    protected function releaseStockReservations(array $reservations): void
    {
        foreach ($reservations as $reservation) {
            $stockLevel = StockLevel::find($reservation['stock_level_id']);
            
            if ($stockLevel) {
                $stockLevel->available_quantity += $reservation['quantity'];
                $stockLevel->reserved_quantity -= $reservation['quantity'];
                $stockLevel->save();
            }
        }
    }

    /**
     * Generate unique order number
     */
    protected function generateOrderNumber(): string
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    /**
     * Process webhook for payment events (idempotent)
     */
    public function processPaymentWebhook(array $payload, string $signature): array
    {
        // Verify signature
        if (!$this->paymentGateway->verifyWebhookSignature(json_encode($payload), $signature)) {
            throw new Exception('Invalid webhook signature');
        }

        $event = $this->paymentGateway->processWebhook($payload);
        
        // Find payment by transaction ID
        $payment = Payment::where('transaction_id', $event['payment_id'])->first();
        
        if (!$payment) {
            Log::warning('Payment not found for webhook', ['payment_id' => $event['payment_id']]);
            return ['status' => 'payment_not_found'];
        }

        // Idempotence check - avoid duplicate processing
        if ($payment->status === 'completed' && $event['event_type'] === 'payment_intent.succeeded') {
            return ['status' => 'already_processed'];
        }

        // Update payment status
        $newStatus = match($event['status']) {
            'succeeded' => 'completed',
            'failed' => 'failed',
            'canceled' => 'canceled',
            default => 'pending',
        };

        $payment->update(['status' => $newStatus]);
        
        // Update order payment status
        $payment->order->update([
            'payment_status' => $newStatus === 'completed' ? 'paid' : $newStatus,
        ]);

        return [
            'status' => 'processed',
            'payment_id' => $payment->id,
            'order_id' => $payment->order_id,
        ];
    }
}
