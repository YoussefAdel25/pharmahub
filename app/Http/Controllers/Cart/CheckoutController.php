<?php

namespace App\Http\Controllers\Cart;

use App\Models\Order\Order;
use App\Models\Order\OrderItem;
use App\Models\Cart\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $userId = Auth::id();
        $cartItems = CartItem::where('user_id', $userId)->with('product')->get();

        if ($cartItems->isEmpty()) {
            Log::info("Checkout failed: Cart is empty for user_id: {$userId}");
            return redirect()->back()->with('error', 'Your cart is empty.');
        }

        DB::beginTransaction();

        try {
            Log::info("Creating order for user_id: {$userId}");
            $order = Order::create([
                'user_id' => $userId,
                'status' => 'pending',
                'total_price' => 0
            ]);

            $total = 0;
            $partialProducts = [];

            $previousOrders = OrderItem::whereHas(
                'order',
                fn($q) =>
                $q->where('user_id', $userId)
                    ->where('status', '!=', 'cancelled')
            )->get()->groupBy('product_id');

            foreach ($cartItems as $item) {
                $product = $item->product;

                if (!$product) {
                    DB::rollBack();
                    return redirect()->back()->with('error', "Product in cart (ID: {$item->product_id}) no longer exists.");
                }

                Log::info("Processing product_id: {$product->id}, quantity: {$item->quantity}");

                if ($item->quantity <= 0) {
                    DB::rollBack();
                    return redirect()->back()->with('error', "Invalid quantity for product: {$product->name}");
                }

                $maxQty = $product->quota_limit ?? $item->quantity;
                $quotaPeriod = $product->quota_period;
                $executedQty = $item->quantity;
                if ($executedQty <= 0) {
                    Log::warning("Cannot execute product_id {$product->id}: stock={$product->stock}, maxQty={$maxQty}, requested={$item->quantity}");
                    continue;
                }


                if ($quotaPeriod && isset($previousOrders[$product->id])) {
                    $orderedQty = $previousOrders[$product->id]->sum('quantity_executed');
                    switch ($quotaPeriod) {
                        case 'daily':
                            $orderedQty = $previousOrders[$product->id]->filter(fn($oi) => $oi->created_at->isToday())->sum('quantity_executed');
                            break;
                        case 'weekly':
                            $orderedQty = $previousOrders[$product->id]->filter(fn($oi) => $oi->created_at->isSameWeek(now()))->sum('quantity_executed');
                            break;
                        case 'monthly':
                            $orderedQty = $previousOrders[$product->id]->filter(fn($oi) => $oi->created_at->isSameMonth(now()))->sum('quantity_executed');
                            break;
                        case 'per_customer':
                            break;
                        case 'per_order':
                            $orderedQty = 0;
                            break;
                    }
                    $maxQty = max(0, $maxQty - $orderedQty);
                }

                $executedQty = min($item->quantity, $maxQty, $product->stock);

                if ($executedQty <= 0) {
                    $partialProducts[] = $product->name;
                    Log::warning("Product {$product->id} skipped due to stock/quota.");
                    continue;
                }


                if ($executedQty < $item->quantity) {
                    $partialProducts[] = $product->name;
                }

                Log::info("Executed quantity for product_id {$product->id}: {$executedQty}");

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity_requested' => $item->quantity,
                    'quantity_executed' => $executedQty,
                    'price' => $item->price,
                    'is_partially_executed' => $item->quantity > $executedQty,
                ]);

                $product->decrement('stock', $executedQty);
                Log::info("Stock decremented for product_id: {$product->id}, by: {$executedQty}");

                $total += $executedQty * $item->price;
            }

            $orderStatus = $order->items()->where('is_partially_executed', true)->exists()
                ? 'partially_completed'
                : 'in_delivery';

            $order->update(['status' => $orderStatus, 'total_price' => $total]);
            Log::info("Order status updated to {$orderStatus}, total: {$total}");

            CartItem::where('user_id', $userId)->delete();
            Log::info("Cart cleared for user_id: {$userId}");

            DB::commit();

            $message = 'Order has been successfully placed!';
            if (!empty($partialProducts)) {
                $message .= ' Note: Partial execution for products: ' . implode(', ', $partialProducts);
            }

            return redirect()->route('orders.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Checkout error for user_id: {$userId}: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An error occurred while processing your order. Please try again.');
        }
    }
}
