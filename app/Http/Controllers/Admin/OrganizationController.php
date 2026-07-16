<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Designation;
use App\Models\CompanyBranch;

class OrganizationController extends Controller
{
    private function getModel($type)
    {
        return match($type) {
            'departments' => Department::class,
            'designations' => Designation::class,
            'branches' => CompanyBranch::class,
            default => abort(404)
        };
    }

    public function index($type)
    {
        $model = $this->getModel($type);
        $items = $model::all();
        $title = ucfirst($type);
        return view('admin.organization.index', compact('items', 'type', 'title'));
    }

    public function store(Request $request, $type)
    {
        $request->validate(['name' => 'required|string|max:255', 'address' => 'nullable|string']);
        $model = $this->getModel($type);
        $data = $request->only('name', 'address');
        $data['is_active'] = $request->has('is_active') || $request->is_active == '1';
        $model::create($data);
        return back()->with('success', 'Item added successfully.');
    }

    public function update(Request $request, $type, $id)
    {
        $request->validate(['name' => 'required|string|max:255', 'address' => 'nullable|string']);
        $model = $this->getModel($type);
        $item = $model::findOrFail($id);
        
        $data = $request->only('name', 'address');
        $data['is_active'] = $request->has('is_active');
        $item->update($data);
        
        return back()->with('success', 'Item updated successfully.');
    }

    public function destroy($type, $id)
    {
        $model = $this->getModel($type);
        $model::destroy($id);
        return back()->with('success', 'Item deleted successfully.');
    }
}
