<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveType;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::all();
        return view('admin.leave.types.index', compact('leaveTypes'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string', 'days_allowed' => 'required|integer']);
        $data = $request->all();
        $data['is_active'] = $request->has('is_active') || $request->is_active == '1';
        LeaveType::create($data);
        return back()->with('success', 'Leave Type added');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string', 'days_allowed' => 'required|integer']);
        $leaveType = LeaveType::findOrFail($id);
        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $leaveType->update($data);
        return back()->with('success', 'Leave Type updated');
    }

    public function destroy($id)
    {
        LeaveType::destroy($id);
        return back()->with('success', 'Leave Type deleted');
    }
}
