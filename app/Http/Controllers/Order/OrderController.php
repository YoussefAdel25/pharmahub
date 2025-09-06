<?php

namespace App\Http\Controllers\Order;

use App\Models\Order\Order;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer\CustomerAction;

class OrderController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $orders = Order::with('items.product')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.orders.index', compact('orders'));
    }

    public function cancel($orderId)
    {
        $userId = Auth::id();
        $order = Order::where('user_id', $userId)
            ->where('id', $orderId)
            ->firstOrFail();

        DB::beginTransaction();

        try {
            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product) {
                    $product->increment('stock', $item->quantity_executed);
                }

                CustomerAction::create([
                    'user_id' => $order->user_id,
                    'session_id' => session()->getId(),
                    'action_type' => 'order_cancelled',
                    'product_id' => $item->product_id,
                ]);
            }

            $order->update(['status' => 'cancelled']);

            DB::commit();

            return redirect()->back()->with('success', 'Order has been cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();


            return redirect()->back()->with('error', 'Failed to cancel the order. Please check the logs.');
        }
    }

    public function allOrders()
    {
            $orders = Order::with('items.product')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('admin.orders.index', compact('orders'));
    }
}
