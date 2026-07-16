<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\Employee;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::with(['assignments' => function($q) {
            $q->whereNull('returned_date')->with('employee');
        }])->latest()->get();
        $employees = Employee::where('status', 'Active')->get();
        return view('admin.assets.index', compact('assets', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'serial_number' => 'required|string|unique:assets',
            'purchase_date' => 'nullable|date'
        ]);

        Asset::create($request->all());
        return back()->with('success', 'Asset added');
    }

    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'serial_number' => 'required|string|unique:assets,serial_number,' . $asset->id,
            'purchase_date' => 'nullable|date',
            'status' => 'required|string'
        ]);

        $asset->update($request->all());
        return back()->with('success', 'Asset updated');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return back()->with('success', 'Asset deleted');
    }

    public function assign(Request $request, Asset $asset)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'assigned_date' => 'required|date',
        ]);

        AssetAssignment::create([
            'asset_id' => $asset->id,
            'employee_id' => $request->employee_id,
            'assigned_date' => $request->assigned_date,
            'condition_given' => $request->condition_given
        ]);

        $asset->update(['status' => 'Assigned']);
        return back()->with('success', 'Asset assigned');
    }

    public function returnAsset(Request $request, AssetAssignment $assignment)
    {
        $request->validate(['returned_date' => 'required|date']);

        $assignment->update([
            'returned_date' => $request->returned_date,
            'condition_returned' => $request->condition_returned
        ]);

        $assignment->asset->update(['status' => 'Available']);
        return back()->with('success', 'Asset returned');
    }
}
