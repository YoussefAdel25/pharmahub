<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Models\Product\Product;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Order\SupplierDiscount;
use App\Models\Customer\CustomerAction;

class CustomerController extends Controller
{
    public function productsForCustomer()
    {
        $user = Auth::user();

        if (!$user->isCustomer()) {
            abort(403, 'Unauthorized');
        }

        $supplierIds = Cache::remember("customer:{$user->id}:supplier_ids", 3600, function () use ($user) {
            return DB::table('region_supplier')
                ->where('region_id', $user->region_id)
                ->pluck('supplier_id');
        });

        $allProducts = Cache::remember("customer:{$user->id}:all_products", 300, function () use ($supplierIds) {
            return Product::with('supplier')
                ->whereIn('supplier_id', $supplierIds)
                ->get();
        });

        $discounts = Cache::remember("supplier_discounts", 3600, function () {
            return SupplierDiscount::all()
                ->keyBy(fn($item) => $item->supplier_id . '-' . $item->product_id);
        });

        $purchasedProductIds = DB::table('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->where('o.user_id', $user->id)
            ->pluck('oi.product_id')
            ->unique()
            ->toArray();

        $frequentTogether = [];
        if (!empty($purchasedProductIds)) {
            $frequentTogether = Cache::remember("customer:{$user->id}:frequent_together", 300, function () use ($purchasedProductIds) {
                return DB::table('order_items as oi1')
                    ->join('order_items as oi2', 'oi1.order_id', '=', 'oi2.order_id')
                    ->whereIn('oi1.product_id', $purchasedProductIds)
                    ->whereNotIn('oi2.product_id', $purchasedProductIds)
                    ->select('oi2.product_id', DB::raw('COUNT(*) as freq'))
                    ->groupBy('oi2.product_id')
                    ->orderByDesc('freq')
                    ->pluck('freq', 'oi2.product_id')
                    ->toArray();
            });
        }

        $recommendedProducts = Cache::remember("customer:{$user->id}:recommended_products", 300, function () use ($user, $allProducts, $supplierIds, $frequentTogether) {
            $interactionScores = $this->calculateInteractionScores($user);

            $recommended = collect();

            if (!empty($interactionScores)) {
                $recommended = Product::with('supplier')
                    ->whereIn('id', array_keys($interactionScores))
                    ->whereIn('supplier_id', $supplierIds)
                    ->get()
                    ->map(fn($product) => $this->mapProductWithDiscountAndQuota($product, $user))
                    ->filter(fn($product) => $product->quota_remaining > 0)
                    ->values();
            }

            if ($recommended->isEmpty() && !empty($frequentTogether)) {
                $recommended = Product::with('supplier')
                    ->whereIn('id', array_keys($frequentTogether))
                    ->whereIn('supplier_id', $supplierIds)
                    ->get()
                    ->map(fn($product) => $this->mapProductWithDiscountAndQuota($product, $user))
                    ->filter(fn($product) => $product->quota_remaining > 0)
                    ->values();
            }

            if ($recommended->isEmpty()) {
                $recommended = Product::with('supplier')
                    ->whereIn('supplier_id', $supplierIds)
                    ->get()
                    ->map(fn($product) => $this->mapProductWithDiscountAndQuota($product, $user))
                    ->filter(fn($product) => $product->quota_remaining > 0)
                    ->values();
            }

            return $recommended;
        });

        return view('customer.products.products', compact('allProducts', 'recommendedProducts', 'discounts'));
    }

    private function calculateInteractionScores($user)
    {
        $views = CustomerAction::select('product_id', DB::raw('count(*) as views'))
            ->where('action_type', 'view_product')
            ->groupBy('product_id')
            ->pluck('views', 'product_id')
            ->toArray();

        $adds = CustomerAction::select('product_id', DB::raw('count(*) as adds'))
            ->where('action_type', 'add_to_cart')
            ->groupBy('product_id')
            ->pluck('adds', 'product_id')
            ->toArray();

        $favourites = CustomerAction::select('product_id', DB::raw('count(*) as favs'))
            ->where('action_type', 'favourite')
            ->groupBy('product_id')
            ->pluck('favs', 'product_id')
            ->toArray();

        $scores = [];
        foreach ($views as $pid => $count) $scores[$pid] = ($scores[$pid] ?? 0) + $count;
        foreach ($adds as $pid => $count) $scores[$pid] = ($scores[$pid] ?? 0) + $count * 2;
        foreach ($favourites as $pid => $count) $scores[$pid] = ($scores[$pid] ?? 0) + $count * 3;

        return $scores;
    }


    private function mapProductWithDiscountAndQuota($product, $user)
    {
        $discounts = SupplierDiscount::where('product_id', $product->id)
            ->where('supplier_id', $product->supplier_id)
            ->orderByDesc('discount_rate')
            ->get();

        $appliedDiscount = $discounts->first();

        $purchasedQtyToday = DB::table('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->where('o.user_id', $user->id)
            ->where('oi.product_id', $product->id)
            ->whereDate('o.created_at', today())
            ->sum('oi.quantity_requested');

        $remaining = max($product->quota_limit - $purchasedQtyToday, 0);

        if ($remaining <= 0 && $discounts->count() > 1) {
            $appliedDiscount = $discounts->skip(1)->first();
            $product->second_chance = true;
        } else {
            $product->second_chance = false;
        }

        $product->discount = $appliedDiscount ? $appliedDiscount->discount_rate : 0;
        $product->quota_remaining = $remaining;

        return $product;
    }

    public function productsBySupplier($supplierId)
    {
        $products = Product::with('supplier')
            ->where('supplier_id', $supplierId)
            ->get();

        $discounts = SupplierDiscount::all()->keyBy(
            fn($item) => $item->supplier_id . '-' . $item->product_id
        );

        return view('customer.products.productsBySupplier', compact('products', 'discounts'));
    }

    public function showProduct($id)
    {
        $product = Product::findOrFail($id);

        CustomerAction::create([
            'user_id'    => Auth::id(),
            'session_id' => session()->getId(),
            'action_type' => 'view_product',
            'product_id' => $product->id,
        ]);

        $discount = SupplierDiscount::where('supplier_id', $product->supplier_id)
            ->where('product_id', $id)
            ->first();

        $discount = $discount ? $discount->discount_rate : 0;

        return view('supplier.products.show', compact('product', 'discount'));
    }
}
