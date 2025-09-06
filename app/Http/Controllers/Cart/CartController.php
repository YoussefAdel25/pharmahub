<?php

namespace App\Http\Controllers\Cart;

use App\Models\Product\Product;
use Illuminate\Http\Request;
use App\Models\Cart\CartItem;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer\CustomerAction;

class CartController extends \App\Http\Controllers\Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'price'      => 'nullable|numeric|min:0',
        ]);

        $userId = Auth::id();
        $product = Product::findOrFail($request->product_id);

        $cartItem = CartItem::firstOrNew([
            'user_id'    => $userId,
            'product_id' => $product->id,
        ]);

        $newQuantity = $cartItem->exists ? $cartItem->quantity + (int) $request->quantity : (int) $request->quantity;

        if ($product->quota_period && $newQuantity > $product->quota_period) {
            $alternative = $this->getAlternativeProduct($product);

            return response()->json([
                'success' => false,
                'message' => "You exceeded the quota for {$product->name}.",
                'alternative' => $alternative,
            ]);
        }

        $cartItem->quantity = $newQuantity;
        $cartItem->price = $request->price ?? $product->price;
        $cartItem->save();

        CustomerAction::create([
            'user_id'    => $userId,
            'session_id' => session()->getId(),
            'action_type' => 'add_to_cart',
            'product_id' => $product->id,
        ]);

        $count = CartItem::where('user_id', $userId)->sum('quantity');

        return response()->json([
            'success'  => true,
            'quantity' => $cartItem->quantity,
            'count'    => $count,
        ]);
    }

    public function items()
    {
        $userId = auth()->id();
        $cartItems = CartItem::with('product')->where('user_id', $userId)->get();

        $total = $cartItems->sum(fn($item) => $item->price * $item->quantity);
        $count = $cartItems->sum('quantity');

        $html = view('cart.items', compact('cartItems'))->render();

        return response()->json(['html' => $html, 'total' => $total, 'count' => $count]);
    }

 public function update(Request $request)
{
    \Log::info('Update cart called', $request->all());

    $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity'   => 'required|integer|min:1',
    ]);

    $userId = Auth::id();
    $product = Product::findOrFail($request->product_id);

    $cartItem = CartItem::where('user_id', $userId)
        ->where('product_id', $product->id)
        ->first();

    \Log::info('Cart item', [$cartItem]);

    if (!$cartItem) {
        return response()->json([
            'success' => false,
            'message' => 'Cart item not found.',
        ]);
    }

    if ($product->quota_period && $request->quantity > $product->quota_period) {
        $alternative = $this->getAlternativeProduct($product);

        return response()->json([
            'success' => false,
            'message' => "You exceeded the quota for {$product->name}.",
            'alternative' => $alternative ?? [
                'id' => null,
                'name' => 'No alternative available',
                'image' => asset('images/no-product.png'),
            ],
        ]);
    }

    $cartItem->quantity = (int) $request->quantity;
    $cartItem->price = $request->price ?? $cartItem->price;
    $cartItem->save();

    $count = CartItem::where('user_id', $userId)->sum('quantity');

    return response()->json([
        'success'  => true,
        'quantity' => $cartItem->quantity,
        'count'    => $count,
    ]);
}


    private function getAlternativeProduct(Product $product)
    {
        $alt = Product::where('type', $product->type)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->first();
        \Log::info('Alternative product', [$alt]);


        return $alt ? [
            'id'    => $alt->id,
            'name'  => $alt->name,
            'price' => $alt->price,
            'image' => $alt->image ? asset('storage/' . $alt->image) : null,
        ] : null;
    }
}
