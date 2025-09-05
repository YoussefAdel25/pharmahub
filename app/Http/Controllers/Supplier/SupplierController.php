<?php

namespace App\Http\Controllers\Supplier;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Product\Product;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Order\SupplierDiscount;

class SupplierController extends Controller
{
    public function indexSuppliers()
    {
        $user = auth()->user();

        $suppliers = DB::table('region_supplier')
            ->where('region_supplier.region_id', $user->region_id)
            ->join('users', 'region_supplier.supplier_id', '=', 'users.id')
            ->select('users.*')
            ->get();


        return view('customer.suppliers', compact('suppliers'));
    }

    public function showSupplierProducts($supplier_id)
    {
        $user = auth()->user();
        $supplier = User::where('role', 'supplier')
            ->where('id', $supplier_id)
            ->firstOrFail();

        $products = Product::where('supplier_id', $supplier_id)->get();

        return view('customer.products', compact('supplier', 'products'));
    }
    public function showProduct($id)
    {
        $product= Product::findOrFail($id);
                $discount = SupplierDiscount::where('supplier_id', $product->supplier_id)
            ->where('product_id', $id)
            ->first();
        $discount = $discount ? $discount->discount_rate : 0;


        return view('products.show', compact('product', 'discount'));
    }
}
