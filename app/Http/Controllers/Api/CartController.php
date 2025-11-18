<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Get current cart
     */
    public function index()
    {
        $cart = $this->cartService->getCart();
        $summary = $this->cartService->getCartSummary();

        return response()->json([
            'cart' => $cart,
            'summary' => $summary,
        ]);
    }

    /**
     * Add item to cart
     */
    public function addItem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sku_id' => 'required|exists:skus,id',
            'quantity' => 'required|integer|min:1',
            'unit_id' => 'nullable|exists:units,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $cartItem = $this->cartService->addItem(
            $request->input('sku_id'),
            $request->input('quantity'),
            $request->input('unit_id')
        );

        $summary = $this->cartService->getCartSummary();

        return response()->json([
            'message' => 'Item added to cart',
            'item' => $cartItem,
            'summary' => $summary,
        ], 201);
    }

    /**
     * Update cart item quantity
     */
    public function updateItem(Request $request, int $cartItemId)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $cartItem = $this->cartService->updateItemQuantity(
            $cartItemId,
            $request->input('quantity')
        );

        $summary = $this->cartService->getCartSummary();

        return response()->json([
            'message' => 'Cart item updated',
            'item' => $cartItem,
            'summary' => $summary,
        ]);
    }

    /**
     * Remove item from cart
     */
    public function removeItem(int $cartItemId)
    {
        $this->cartService->removeItem($cartItemId);
        $summary = $this->cartService->getCartSummary();

        return response()->json([
            'message' => 'Item removed from cart',
            'summary' => $summary,
        ]);
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        $this->cartService->clearCart();

        return response()->json([
            'message' => 'Cart cleared successfully',
        ]);
    }

    /**
     * Get cart summary
     */
    public function summary()
    {
        $summary = $this->cartService->getCartSummary();

        return response()->json($summary);
    }
}
