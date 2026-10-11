<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EarlyGoingRequest;

class EarlyGoingController extends Controller
{
    public function index()
    {
        $employeeId = auth()->guard('employee')->user()->employee_id;
        $requests = EarlyGoingRequest::where('employee_id', $employeeId)->orderByDesc('created_at')->get();
        return view('employee.portal.early_going', compact('requests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required',
            'reason' => 'required|string',
        ]);

        $earlyGoingRequest = EarlyGoingRequest::create([
            'employee_id' => auth()->guard('employee')->user()->employee_id,
            'date' => $request->date,
            'time' => $request->time,
            'reason' => $request->reason,
            'status' => 'Pending',
        ]);

        $employee = auth()->guard('employee')->user()->employee;
        $tls = \App\Models\Employee::where('department_id', $employee->department_id)
            ->where('is_teamleader', true)
            ->get();
            
        foreach ($tls as $tl) {
            if ($tl->employeeLogin) {
                $tl->employeeLogin->notify(new \App\Notifications\EarlyGoingRequestSubmitted($earlyGoingRequest, "{$employee->name} has requested early going on {$request->date}."));
            }
        }

        return back()->with('success', 'Early going request submitted successfully.');
    }
}
