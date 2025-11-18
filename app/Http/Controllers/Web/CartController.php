<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display cart page
     */
    public function index()
    {
        $cart = $this->cartService->getCart();
        $summary = $this->cartService->getCartSummary();

        return view('cart.index', compact('cart', 'summary'));
    }

    /**
     * Add item to cart (AJAX)
     */
    public function addItem(Request $request)
    {
        $request->validate([
            'sku_id' => 'required|exists:skus,id',
            'quantity' => 'required|integer|min:1',
            'unit_id' => 'nullable|exists:units,id',
        ]);

        $cartItem = $this->cartService->addItem(
            $request->input('sku_id'),
            $request->input('quantity'),
            $request->input('unit_id')
        );

        $summary = $this->cartService->getCartSummary();

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'item' => $cartItem,
            'summary' => $summary,
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function updateItem(Request $request, int $cartItemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $cartItem = $this->cartService->updateItemQuantity(
            $cartItemId,
            $request->input('quantity')
        );

        if ($request->expectsJson()) {
            $summary = $this->cartService->getCartSummary();
            return response()->json([
                'success' => true,
                'message' => 'Cart updated',
                'summary' => $summary,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Cart updated successfully');
    }

    /**
     * Remove item from cart
     */
    public function removeItem(int $cartItemId)
    {
        $this->cartService->removeItem($cartItemId);

        return redirect()->route('cart.index')->with('success', 'Item removed from cart');
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        $this->cartService->clearCart();

        return redirect()->route('cart.index')->with('success', 'Cart cleared successfully');
    }
}
