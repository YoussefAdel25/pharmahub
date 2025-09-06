<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use App\Models\Product\Product;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Order\SupplierDiscount;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::where('supplier_id', Auth::id())->get();

        $discounts = SupplierDiscount::all()->keyBy(function ($item) {
            return $item->supplier_id . '-' . $item->product_id;
        });

        return view('supplier.products.index', compact('products', 'discounts'));
    }


    public function create()
    {
        return view('supplier.products.create');
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

        $validated['supplier_id'] = Auth::id();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        SupplierDiscount::updateOrCreate(
            ['supplier_id' => Auth::id(), 'product_id' => $product->id],
            ['discount_rate' => $validated['discount']]
        );
        if (Auth::user()->role === 'admin') {
            return redirect()->route('allProducts.index')
                ->with('success', 'Product created successfully!');
        } else {
            return redirect()->route('products.index')
                ->with('success', 'Product created successfully!');
        }
    }


    public function edit($id)
    {


        if (Auth::user()->role === 'admin') {
            $product = Product::with('supplier')->findOrFail($id);
            $discount = SupplierDiscount::where('product_id', $id)->first();
            $discount = $discount ? $discount->discount_rate : 0;
            return view('supplier.products.edit', compact('product', 'discount'));
        } else {
            $product  = Product::where('supplier_id', Auth::id())->findOrFail($id);
            $discount = SupplierDiscount::where('supplier_id', Auth::id())
                ->where('product_id', $id)
                ->first();
            $discount = $discount ? $discount->discount_rate : 0;
            return view('supplier.products.edit', compact('product', 'discount'));
        }
    }


    public function update(Request $request, $id)
    {
        if (Auth::user()->role === 'admin') {
            $product = Product::with('supplier')->findOrFail($id);
        } else {
            $product = Product::where('supplier_id', Auth::id())->findOrFail($id);
        }

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
                ['supplier_id' => Auth::id(), 'product_id' => $product->id],
                ['discount_rate' => $validated['discount']]
            );
        }
        if (Auth::user()->role === 'admin') {
            return redirect()->route('allProducts.index')
                ->with('success', 'Product updated successfully!');
        } else {

            return redirect()->route('products.index')
                ->with('success', 'Product updated successfully!');
        }
    }


    public function show($id)
    {
        if (Auth::user()->role === 'admin') {
            $product = Product::with('supplier')->findOrFail($id);
            $discount = SupplierDiscount::where('product_id', $id)->first();
            $discount = $discount ? $discount->discount_rate : 0;
            return view('supplier.products.show', compact('product', 'discount'));
        } else {
            $product  = Product::where('supplier_id', Auth::id())->findOrFail($id);
            $discount = SupplierDiscount::where('supplier_id', Auth::id())
                ->where('product_id', $id)
                ->first();
            $discount = $discount ? $discount->discount_rate : 0;
            return view('supplier.products.show', compact('product', 'discount'));
        }
    }


    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        if (Auth::user()->role === 'admin') {
            return redirect()->route('allProducts.index')
                ->with('success', 'Product deleted successfully!');
        } else {
            return redirect()->route('products.index')
                ->with('success', 'Product deleted successfully!');
        }
    }

    public function getByRegion(Request $request)
    {
        $regionId = $request->region_id;

        $products = Product::where('region_id', $regionId)->get();

        $products = $products->map(function ($p) {
            return [
                'name' => $p->name,
                'price' => $p->price,
                'image_url' => $p->image ? asset('storage/' . $p->image) : 'https://via.placeholder.com/200x200.png',
            ];
        });

        return response()->json($products);
    }

    public function allProducts()
    {
        $products = Product::with('supplier')->get();

        $discounts = SupplierDiscount::all()->keyBy(function ($item) {
            return $item->supplier_id . '-' . $item->product_id;
        });

        return view('admin.products.index', compact('products', 'discounts'));
    }
}
