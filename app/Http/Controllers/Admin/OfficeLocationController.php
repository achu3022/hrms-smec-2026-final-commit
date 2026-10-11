<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OfficeLocation;

class OfficeLocationController extends Controller
{
    public function index(Request $request)
    {
        $locations = OfficeLocation::all();
        
        if ($request->has('id')) {
            $location = $locations->where('id', $request->id)->first();
        } else {
            $location = $locations->first(); 
        }
        
        return view('admin.office-locations.index', compact('location', 'locations'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|integer|min:10',
            'status' => 'boolean',
        ]);

        $location = OfficeLocation::findOrFail($id);
        
        $location->update([
            'name' => $request->name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius,
            'status' => $request->has('status'),
        ]);

        return redirect()->route('admin.office-locations.index')->with('success', 'Office Location updated successfully.');
    }
}
