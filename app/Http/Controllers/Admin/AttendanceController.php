<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        $attendances = Attendance::with('employee')->where('date', $date)->get();
        $employees = Employee::where('status', 'Active')->get();
        return view('admin.attendance.index', compact('attendances', 'employees', 'date'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'status' => 'required|in:Present,Absent,Half-Day,Leave,Late',
            'check_in' => 'nullable',
            'check_out' => 'nullable',
        ]);

        Attendance::updateOrCreate(
            ['employee_id' => $request->employee_id, 'date' => $request->date],
            ['status' => $request->status, 'check_in' => $request->check_in, 'check_out' => $request->check_out]
        );

        return back()->with('success', 'Attendance recorded successfully');
    }

    public function destroy($id)
    {
        Attendance::destroy($id);
        return back()->with('success', 'Attendance record deleted');
    }
}
