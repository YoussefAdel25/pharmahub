<?php

namespace App\Http\Controllers\Cart;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart\CartItem;
use Illuminate\Support\Facades\Auth;

class CartController extends \App\Http\Controllers\Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $userId = Auth::id();
        $productId = $request->product_id;

        $cartItem = CartItem::firstOrNew([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        $cartItem->quantity += $request->quantity;
        $cartItem->save();

        return response()->json(['success' => true, 'quantity' => $cartItem->quantity]);
    }

    public function items()
    {
        $userId = auth()->id();
        $cartItems = CartItem::with('product')->where('user_id', $userId)->get();

        $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);
        $count = $cartItems->sum('quantity');

        $html = view('cart.items', compact('cartItems'))->render();

        return response()->json(['html' => $html, 'total' => $total, 'count' => $count]);
    }


    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $userId = Auth::id();
        $cartItem = CartItem::where('user_id', $userId)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity = $request->quantity;
            $cartItem->save();
        }

        $count = CartItem::where('user_id', $userId)->sum('quantity');

        return response()->json([
            'success' => true,
            'quantity' => $cartItem ? $cartItem->quantity : 0,
            'count' => $count,
        ]);
    }
}
