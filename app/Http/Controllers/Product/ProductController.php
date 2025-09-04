<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use App\Models\Product\Product;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('supplier_id', auth()->user()->id)->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {

        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'quota_limit' => 'nullable|integer|min:0',
            'region_id'   => 'required|exists:regions,id',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $data = $request->all();
        $data['supplier_id'] = auth()->user()->supplier->id;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }


    public function update(Request $request, $id)
    {
        $product = Product::where('supplier_id', auth()->user()->id)->findOrFail($id);

        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'sometimes|numeric|min:0',
        ]);

        $product->update($data);

        return response()->json($product);
    }
    

    public function destroy($id)
    {
        $product = Product::where('supplier_id', auth()->id())->findOrFail($id);

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
