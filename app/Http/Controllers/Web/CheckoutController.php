<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display checkout page
     */
    public function index()
    {
        $cart = $this->cartService->getCart();
        $summary = $this->cartService->getCartSummary();

        // Redirect if cart is empty
        if ($cart->items->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        $shippingAddresses = [];
        $billingAddresses = [];

        if (Auth::check()) {
            $shippingAddresses = Address::where('user_id', Auth::id())
                ->where('type', 'shipping')
                ->get();
            $billingAddresses = Address::where('user_id', Auth::id())
                ->where('type', 'billing')
                ->get();
        }

        return view('checkout.index', compact('cart', 'summary', 'shippingAddresses', 'billingAddresses'));
    }

    /**
     * Store new address
     */
    public function storeAddress(Request $request)
    {
        $this->middleware('auth');

        $request->validate([
            'type' => 'required|in:shipping,billing',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset other defaults
        if ($request->input('is_default', false)) {
            Address::where('user_id', Auth::id())
                ->where('type', $request->input('type'))
                ->update(['is_default' => false]);
        }

        $address = Address::create([
            'user_id' => Auth::id(),
            'type' => $request->input('type'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'phone' => $request->input('phone'),
            'address_line_1' => $request->input('address_line_1'),
            'address_line_2' => $request->input('address_line_2'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'postal_code' => $request->input('postal_code'),
            'country' => $request->input('country'),
            'is_default' => $request->input('is_default', false),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Address saved successfully',
                'address' => $address,
            ]);
        }

        return redirect()->route('checkout.index')->with('success', 'Address saved successfully');
    }
}
