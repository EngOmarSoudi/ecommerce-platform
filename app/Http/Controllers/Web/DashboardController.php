<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $role = $user ? $user->role : 'guest';

        // Role-based dashboard variations
        if ($role === 'seller') {
            return $this->sellerDashboard($user);
        } elseif ($role === 'staff') {
            return $this->staffDashboard($user);
        }

        // Default admin/general dashboard
        return view('dashboard.index');
    }

    protected function sellerDashboard($user)
    {
        // Seller-specific metrics
        $sellerProducts = Product::where('seller_id', $user->id)->count();
        $sellerOrders = Order::whereHas('items', function ($q) use ($user) {
            $q->whereHas('product', function ($pq) use ($user) {
                $pq->where('seller_id', $user->id);
            });
        })->count();

        return view('dashboard.seller', compact('sellerProducts', 'sellerOrders'));
    }

    protected function staffDashboard($user)
    {
        // Staff-specific metrics (POS focused)
        $todayTransactions = Order::where('created_by', $user->id)
            ->whereDate('created_at', today())
            ->count();

        return view('dashboard.staff', compact('todayTransactions'));
    }
}
