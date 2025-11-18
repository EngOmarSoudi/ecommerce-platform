<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Sku;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Get or create cart for current user/session
     */
    public function getCart(): Cart
    {
        if (Auth::check()) {
            // For authenticated users
            $cart = Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['currency' => 'USD', 'total_amount' => 0]
            );

            // Merge session cart if exists
            $sessionId = Session::getId();
            $sessionCart = Cart::where('session_id', $sessionId)->first();
            if ($sessionCart) {
                $this->mergeCarts($sessionCart, $cart);
            }
        } else {
            // For guest users
            $sessionId = Session::getId();
            $cart = Cart::firstOrCreate(
                ['session_id' => $sessionId],
                ['currency' => 'USD', 'total_amount' => 0]
            );
        }

        return $cart->load('items.sku.product', 'items.unit');
    }

    /**
     * Add item to cart
     */
    public function addItem(int $skuId, int $quantity = 1, ?int $unitId = null): CartItem
    {
        $cart = $this->getCart();
        $sku = Sku::with('product')->findOrFail($skuId);

        // Check if item already exists in cart
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('sku_id', $skuId)
            ->where('unit_id', $unitId)
            ->first();

        if ($cartItem) {
            // Update quantity
            $cartItem->quantity += $quantity;
            $cartItem->total_amount = $cartItem->quantity * $cartItem->price;
            $cartItem->save();
        } else {
            // Create new cart item
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'sku_id' => $skuId,
                'unit_id' => $unitId,
                'quantity' => $quantity,
                'price' => $sku->price ?? $sku->product->price,
                'total_amount' => $quantity * ($sku->price ?? $sku->product->price),
            ]);
        }

        $this->updateCartTotal($cart);

        return $cartItem->load('sku.product', 'unit');
    }

    /**
     * Update cart item quantity
     */
    public function updateItemQuantity(int $cartItemId, int $quantity): CartItem
    {
        $cart = $this->getCart();
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('id', $cartItemId)
            ->firstOrFail();

        if ($quantity <= 0) {
            return $this->removeItem($cartItemId);
        }

        $cartItem->quantity = $quantity;
        $cartItem->total_amount = $cartItem->quantity * $cartItem->price;
        $cartItem->save();

        $this->updateCartTotal($cart);

        return $cartItem->load('sku.product', 'unit');
    }

    /**
     * Remove item from cart
     */
    public function removeItem(int $cartItemId): CartItem
    {
        $cart = $this->getCart();
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('id', $cartItemId)
            ->firstOrFail();

        $cartItem->delete();
        $this->updateCartTotal($cart);

        return $cartItem;
    }

    /**
     * Clear entire cart
     */
    public function clearCart(): bool
    {
        $cart = $this->getCart();
        $cart->items()->delete();
        $cart->total_amount = 0;
        $cart->save();

        return true;
    }

    /**
     * Get cart summary (subtotal, tax, shipping, total)
     */
    public function getCartSummary(): array
    {
        $cart = $this->getCart();
        $subtotal = $cart->items->sum('total_amount');
        $taxRate = 0.10; // 10% tax (configurable)
        $tax = $subtotal * $taxRate;
        $shipping = $subtotal > 100 ? 0 : 10; // Free shipping over $100
        $total = $subtotal + $tax + $shipping;

        return [
            'subtotal' => round($subtotal, 2),
            'tax' => round($tax, 2),
            'tax_rate' => $taxRate,
            'shipping' => round($shipping, 2),
            'total' => round($total, 2),
            'item_count' => $cart->items->sum('quantity'),
        ];
    }

    /**
     * Get cart item count
     */
    public function getItemCount(): int
    {
        $cart = $this->getCart();
        return $cart->items->sum('quantity');
    }

    /**
     * Update cart total amount
     */
    protected function updateCartTotal(Cart $cart): void
    {
        $cart->total_amount = $cart->items()->sum('total_amount');
        $cart->save();
    }

    /**
     * Merge session cart into user cart (when user logs in)
     */
    protected function mergeCarts(Cart $sessionCart, Cart $userCart): void
    {
        DB::transaction(function () use ($sessionCart, $userCart) {
            foreach ($sessionCart->items as $sessionItem) {
                $existingItem = CartItem::where('cart_id', $userCart->id)
                    ->where('sku_id', $sessionItem->sku_id)
                    ->where('unit_id', $sessionItem->unit_id)
                    ->first();

                if ($existingItem) {
                    // Merge quantities
                    $existingItem->quantity += $sessionItem->quantity;
                    $existingItem->total_amount = $existingItem->quantity * $existingItem->price;
                    $existingItem->save();
                } else {
                    // Move item to user cart
                    $sessionItem->cart_id = $userCart->id;
                    $sessionItem->save();
                }
            }

            // Delete session cart
            $sessionCart->delete();
        });

        $this->updateCartTotal($userCart);
    }
}
