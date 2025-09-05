<?php

namespace App\Http\Controllers\Region;

use Illuminate\Http\Request;
use App\Models\Region\Region;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RegionController extends Controller
{

    public function index()
    {
        $regions = Region::paginate(20);
        return view('admin.region.index', compact('regions'));
    }


    public function supplier_regions()
    {
        $supplier = Auth::user();
        $supplierRegions = $supplier->deliveryRegions()->get();

        return view('supplier.regions.index', compact('supplierRegions'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $region = Region::create($request->all());

        return response()->json($region, 201);
    }


    public function edit($id)
    {
        $region = Region::findOrFail($id);
        return response()->json($region);
    }


    public function update(Request $request, $id)
    {
        $region = Region::findOrFail($id);
        $region->update($request->all());

        return response()->json($region);
    }


    public function destroy($id)
    {
        $region = Region::findOrFail($id);
        $region->delete();

        return response()->json(['message' => 'Region deleted successfully'], 200);
    }


    public function updateRegions(Request $request)
    {
        $request->validate([
            'regions' => 'nullable|array',
            'regions.*' => 'exists:regions,id',
        ]);

        $supplier = Auth::user();
        $supplier->deliveryRegions()->sync($request->regions ?? []);

        if ($request->ajax()) {
            return response()->json(['message' => 'Delivery regions updated successfully.']);
        }

        return redirect()->route('regions.suppliers')->with('success', 'Delivery regions updated successfully.');
    }


    public function destroySupplierRegion($regionId)
    {
        $supplier = Auth::user();
        $supplier->deliveryRegions()->detach($regionId);

        return response()->json(['message' => 'Region removed successfully']);
    }
}
