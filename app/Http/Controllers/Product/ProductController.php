<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use App\Models\Product\Product;
use App\Http\Controllers\Controller;
use App\Models\Order\SupplierDiscount;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::where('supplier_id', auth()->id())->get();

        $discounts = SupplierDiscount::all()->keyBy(function ($item) {
            return $item->supplier_id . '-' . $item->product_id;
        });

        return view('products.index', compact('products', 'discounts'));
    }


    public function create()
    {
        return view('products.create');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0',
            'discount'     => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'quota_limit'  => 'nullable|integer|min:0|lte:stock',
            'quota_period' => 'required|in:per_order,daily,weekly,monthly,per_customer',
            'type'         => 'nullable|in:single,package,kit',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['supplier_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        SupplierDiscount::updateOrCreate(
            ['supplier_id' => auth()->id(), 'product_id' => $product->id],
            ['discount_rate' => $validated['discount']]
        );

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully!');
    }


    public function edit($id)
    {
        $product  = Product::where('supplier_id', auth()->id())->findOrFail($id);
        $discount = SupplierDiscount::where('supplier_id', auth()->id())
            ->where('product_id', $id)
            ->first();
        $discount = $discount ? $discount->discount_rate : 0;

        return view('products.edit', compact('product', 'discount'));
    }


    public function update(Request $request, $id)
    {
        $product = Product::where('supplier_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'name'         => 'sometimes|string|max:255',
            'description'  => 'nullable|string',
            'price'        => 'sometimes|numeric|min:0',
            'discount'     => 'sometimes|numeric|min:0',
            'stock'        => 'sometimes|integer|min:0',
            'quota_limit'  => 'sometimes|integer|min:0|lte:stock',
            'quota_period' => 'sometimes|in:per_order,daily,weekly,monthly,per_customer',
            'type'         => 'sometimes|in:single,package,kit',
        ]);

        $product->update($validated);

        if (isset($validated['discount'])) {
            SupplierDiscount::updateOrCreate(
                ['supplier_id' => auth()->id(), 'product_id' => $product->id],
                ['discount_rate' => $validated['discount']]
            );
        }

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully!');
    }


    public function show($id)
    {
        $product  = Product::where('supplier_id', auth()->id())->findOrFail($id);
        $discount = SupplierDiscount::where('supplier_id', auth()->id())
            ->where('product_id', $id)
            ->first();
        $discount = $discount ? $discount->discount_rate : 0;

        return view('products.show', compact('product', 'discount'));
    }


    public function destroy($id)
    {
        $product = Product::where('supplier_id', auth()->id())->findOrFail($id);
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully!');
    }
}
