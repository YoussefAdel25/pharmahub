<?php

namespace App\Http\Controllers\Supplier;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product\Product;

class SupplierController extends Controller
{
    public function indexSuppliers()
    {
        $user = auth()->user();


        $suppliers = User::where('role', 'supplier')
            ->where('region_id', $user->region_id)
            ->get();

        return view('Customer.suppliers', compact('suppliers'));
    }

    public function showSupplierProducts($supplier_id)
    {
        $user = auth()->user();
        $supplier = User::where('role', 'supplier')
            ->where('id', $supplier_id)
            ->firstOrFail();

        $products = Product::where('supplier_id', $supplier_id)->get();

        return view('Customer.products', compact('supplier', 'products'));
    }
}
