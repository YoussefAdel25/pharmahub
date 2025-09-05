<?php

namespace App\Http\Controllers\order;

use App\Models\Order\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SupplierOrdersController extends Controller
{
    public function index()
    {
        $supplierId = Auth::id();

        $orders = Order::whereHas('items.product', function ($q) use ($supplierId) {
            $q->where('supplier_id', $supplierId);
        })->with(['user', 'user.region', 'items.product'])->orderBy('created_at', 'desc')->paginate(10);

        return view('supplier.orders.index', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        if (!$order) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        $order->load('items');
        foreach ($request->quantities as $itemId => $addedQty) {
            $orderItem = $order->items->firstWhere('id', $itemId);
            if ($orderItem) {
                $orderItem->quantity_executed += (int)$addedQty;

                if ($orderItem->quantity_executed > $orderItem->quantity_requested) {
                    $orderItem->quantity_executed = $orderItem->quantity_requested;
                }

                $orderItem->save();
            }
        }

        $allExecuted = $order->items->every(fn($i) => $i->quantity_executed >= $i->quantity_requested);

        if ($allExecuted) {
            $order->status = 'in_delivery';
        }

        $order->save();

        return redirect()->back()->with('success', 'Order updated successfully!');
    }

    public function changeStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,partially_completed,in_delivery,delivered,cancelled'
        ]);

        $order->status = $request->status;
        $order->save();

        return redirect()->back()->with('success', 'Order status changed successfully!');
    }

    public function cancel(Order $order)
    {
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
