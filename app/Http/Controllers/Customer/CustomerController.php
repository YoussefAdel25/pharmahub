<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Models\Product\Product;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Order\SupplierDiscount;

class CustomerController extends Controller
{

    public function productsForCustomer()
    {
        $user = auth()->user();

        $supplierIds = DB::table('region_supplier')
            ->where('region_id', $user->region_id)
            ->pluck('supplier_id');

        $allProducts = Product::with('supplier')
            ->whereIn('supplier_id', $supplierIds)
            ->get();

        $purchasedProductIds = DB::table('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->where('o.user_id', $user->id)
            ->pluck('oi.product_id')
            ->unique()
            ->toArray();

        $frequentTogether = [];
        if (!empty($purchasedProductIds)) {
            $frequentTogether = DB::table('order_items as oi1')
                ->join('order_items as oi2', 'oi1.order_id', '=', 'oi2.order_id')
                ->whereIn('oi1.product_id', $purchasedProductIds)
                ->whereNotIn('oi2.product_id', $purchasedProductIds)
                ->select('oi2.product_id', DB::raw('COUNT(*) as freq'))
                ->groupBy('oi2.product_id')
                ->orderByDesc('freq')
                ->pluck('freq', 'oi2.product_id')
                ->toArray();
        }

        $recommendedProducts = collect();

        if (!empty($frequentTogether)) {
            $recommendedProducts = Product::with('supplier')
                ->whereIn('id', array_keys($frequentTogether))
                ->whereIn('supplier_id', $supplierIds)
                ->get()
                ->map(fn($product) => $this->mapProductWithDiscountAndQuota($product, $user));
        }

        if ($recommendedProducts->isEmpty() && !empty($purchasedProductIds)) {
            $firstPurchasedProduct = Product::find($purchasedProductIds[0]);
            $recommendedProducts = Product::with('supplier')
                ->whereIn('supplier_id', $supplierIds)
                ->where('id', '!=', $firstPurchasedProduct->id)
                ->where('type', $firstPurchasedProduct->type) 
                ->limit(5)
                ->get()
                ->map(fn($product) => $this->mapProductWithDiscountAndQuota($product, $user));
        }

        if ($recommendedProducts->isEmpty()) {
            $recommendedProducts = Product::with('supplier')
                ->whereIn('supplier_id', $supplierIds)
                ->limit(5)
                ->get()
                ->map(fn($product) => $this->mapProductWithDiscountAndQuota($product, $user));
        }

        $discounts = SupplierDiscount::all()->keyBy(fn($item) => $item->supplier_id . '-' . $item->product_id);

        return view('customer.products', compact('allProducts', 'recommendedProducts', 'discounts'));
    }

    private function mapProductWithDiscountAndQuota($product, $user)
    {
        $discount = SupplierDiscount::where('product_id', $product->id)
            ->where('supplier_id', $product->supplier_id)
            ->first();
        $product->discount = $discount ? $discount->discount_rate : 0;

        $purchasedQtyToday = DB::table('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->where('o.user_id', $user->id)
            ->where('oi.product_id', $product->id)
            ->whereDate('o.created_at', today())
            ->sum('oi.quantity_requested');

        $product->quota_remaining = max($product->quota_limit - $purchasedQtyToday, 0);

        return $product;
    }

    public function productsBySupplier($supplierId)
    {
        $products = Product::with('supplier')
            ->where('supplier_id', $supplierId)
            ->get();
        $discounts = SupplierDiscount::all()->keyBy(function ($item) {
            return $item->supplier_id . '-' . $item->product_id;
        });

        return view('customer.productsBySupplier', compact('products', 'discounts'));
    }
    public function showProduct($id)
    {
        $product = Product::findOrFail($id);
        $discount = SupplierDiscount::where('supplier_id', $product->supplier_id)
            ->where('product_id', $id)
            ->first();
        $discount = $discount ? $discount->discount_rate : 0;

        return view('products.show', compact('product', 'discount'));
    }
}
