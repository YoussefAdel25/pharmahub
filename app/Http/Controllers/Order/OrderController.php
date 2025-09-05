<?php

namespace App\Http\Controllers\Order;

use App\Models\Order\Order;
use Illuminate\Http\Request;
use App\Models\Order\OrderItem;
use App\Models\Product\Product;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $orders = Order::with('items.product')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('orders.index', compact('orders'));
    }

    public function cancel($orderId)
    {
        $userId = Auth::id();
        $order = Order::where('id', $orderId)->where('user_id', $userId)->firstOrFail();

        // if (in_array($order->status, ['completed', 'partially_completed', 'cancelled'])) {
        //     return redirect()->back()->with('error', 'Cannot cancel this order.');
        // }

        DB::beginTransaction();

        try {
            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product) {
                    $product->increment('stock', $item->quantity_executed);
                }
            }

            $order->update(['status' => 'cancelled']);

            DB::commit();

            return redirect()->back()->with('success', 'Order has been cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to cancel the order. Please try again.');
        }
    }
}
