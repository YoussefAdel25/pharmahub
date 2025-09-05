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
        $products = Product::where('supplier_id', auth()->user()->id)->get();
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
        // dd($request->all());
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'discount'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'quota_limit' => 'nullable|integer|min:0|lte:stock',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required'        => 'Product name is required.',
            'name.string'          => 'Product name must be a string.',
            'name.max'             => 'Product name cannot exceed 255 characters.',

            'description.string'   => 'Description must be a valid text.',

            'price.required'       => 'Price is required.',
            'price.numeric'        => 'Price must be a number.',
            'price.min'            => 'Price must be at least 0.',

            'stock.required'       => 'Stock is required.',
            'stock.integer'        => 'Stock must be an integer.',
            'stock.min'            => 'Stock cannot be less than 0.',

            'quota_limit.integer'  => 'Quota limit must be an integer.',
            'quota_limit.min'      => 'Quota limit cannot be less than 0.',
            'quota_limit.lte'      => 'Quota limit cannot be greater than the stock.',

            'image.image'          => 'Uploaded file must be an image.',
            'image.mimes'          => 'Image must be a file of type: jpeg, png, jpg, gif.',
            'image.max'            => 'Image size cannot exceed 2MB.',
        ]);


        $data = $request->all();
        $data['supplier_id'] = auth()->user()->id;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        SupplierDiscount::create([
            'supplier_id' => auth()->user()->id,
            'product_id' => $product->id,
            'discount_rate' => $request->discount,
        ]);

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
