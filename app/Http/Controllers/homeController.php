<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order\Order;
use Illuminate\Http\Request;
use App\Models\Product\Product;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Customer\CustomerController;

class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->isCustomer()) {
            return app(CustomerController::class)->productsForCustomer();
        }
        if ($user->isAdmin()) {
            $topProducts = Product::withCount('orders')
                ->orderByDesc('orders_count')
                ->take(5)
                ->get();

            $activeCustomers = User::where('role', 'customer')
                ->withCount('orders')
                ->orderByDesc('orders_count')
                ->take(5)
                ->get();

            $orderStats = Order::selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');
        } elseif ($user->isSupplier()) {
            $topProducts = Product::where('supplier_id', $user->id)
                ->withCount('orderItems')
                ->orderByDesc('order_items_count')
                ->take(5)
                ->get();

            $activeCustomers = User::where('role', 'customer')
                ->whereHas('orders.orderItems.product', function ($q) use ($user) {
                    $q->where('supplier_id', $user->id);
                })
                ->withCount(['orders as orders_count' => function ($q) use ($user) {
                    $q->whereHas('orderItems.product', function ($q2) use ($user) {
                        $q2->where('supplier_id', $user->id);
                    });
                }])
                ->orderByDesc('orders_count')
                ->take(5)
                ->get();

            $orderStats = Order::whereHas('orderItems.product', function ($q) use ($user) {
                $q->where('supplier_id', $user->id);
            })
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');
        } else {
            abort(403, 'Unauthorized');
        }

        return view('dashboard', compact('topProducts', 'activeCustomers', 'orderStats'));
    }
}
