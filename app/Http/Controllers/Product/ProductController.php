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
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'discount'    => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'quota_limit' => 'nullable|integer|min:0|lte:stock',
            'quota_period'=> 'nullable|in:per_order,daily,weekly,monthly,total',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();
        $data['supplier_id'] = auth()->id();

        if($request->hasFile('image')){
            $data['image'] = $request->file('image')->store('products','public');
        }

        $product = Product::create($data);

        SupplierDiscount::updateOrCreate(
            ['supplier_id' => auth()->id(), 'product_id' => $product->id],
            ['discount_rate' => $request->discount]
        );

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    public function update(Request $request, $id)
    {
        $product = Product::where('supplier_id', auth()->id())->findOrFail($id);

        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'sometimes|numeric|min:0',
            'wholesale_price' => 'sometimes|numeric|min:0',
            'discount'    => 'sometimes|numeric|min:0',
            'stock'       => 'sometimes|integer|min:0',
            'quota_limit' => 'sometimes|integer|min:0|lte:stock',
            'quota_period'=> 'sometimes|in:per_order,daily,weekly,monthly,total',
        ]);

        $product->update($data);

        if(isset($data['discount'])){
            SupplierDiscount::updateOrCreate(
                ['supplier_id' => auth()->id(), 'product_id' => $product->id],
                ['discount_rate' => $data['discount']]
            );
        }

        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::where('supplier_id', auth()->id())->findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
