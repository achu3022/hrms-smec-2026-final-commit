<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Employee;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $requests = LeaveRequest::with(['employee', 'leaveType'])->latest()->get();
        $employees = Employee::where('status', 'Active')->get();
        $leaveTypes = LeaveType::where('is_active', true)->get();
        return view('admin.leave.requests.index', compact('requests', 'employees', 'leaveTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string'
        ]);

        $start = \Carbon\Carbon::parse($request->start_date);
        $end = \Carbon\Carbon::parse($request->end_date);
        $total_days = $start->diffInDays($end) + 1;

        // Auto approve if HR logs it directly
        LeaveRequest::create([
            'employee_id' => $request->employee_id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $total_days,
            'reason' => $request->reason,
            'status' => 'Approved'
        ]);

        return back()->with('success', 'Leave logged successfully');
    }

    public function destroy($id)
    {
        LeaveRequest::destroy($id);
        return back()->with('success', 'Leave Request deleted');
    }
}
