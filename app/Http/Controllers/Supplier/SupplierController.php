<?php

namespace App\Http\Controllers\Supplier;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Product\Product;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Order\SupplierDiscount;

class SupplierController extends Controller
{

    public function showSupplierProducts($supplier_id)
    {
        $user = Auth::user();
        $supplier = User::where('role', 'supplier')
            ->where('id', $supplier_id)
            ->firstOrFail();

        $products = Product::where('supplier_id', $supplier_id)->get();

        return view('customer.products.products', compact('supplier', 'products'));
    }
    public function showProduct($id)
    {
        $product= Product::findOrFail($id);
                $discount = SupplierDiscount::where('supplier_id', $product->supplier_id)
            ->where('product_id', $id)
            ->first();
        $discount = $discount ? $discount->discount_rate : 0;


        return view('supplier.products.show', compact('product', 'discount'));
    }
}
