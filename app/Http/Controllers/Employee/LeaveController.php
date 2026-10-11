<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Notifications\LeaveRequestSubmitted;
use App\Services\LeaveAllocationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    protected LeaveAllocationService $leaveService;

    public function __construct(LeaveAllocationService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    public function index()
    {
        $employee = Auth::guard('employee')->user()->employee;
        $requests = LeaveRequest::where('employee_id', $employee->id)
            ->with(['leaveType', 'segments'])
            ->orderByDesc('created_at')
            ->get();

        $balanceStats = $this->leaveService->getEmployeeDashboardBalances($employee);

        return view('employee.portal.leaves', compact('employee', 'requests', 'balanceStats'));
    }

    public function calculateAllocation(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'nullable|date',
            'leave_type_id' => 'nullable|exists:leave_types,id',
            'is_half_day' => 'nullable|boolean',
        ]);

        $employee = Auth::guard('employee')->user()->employee;
        $startDate = Carbon::parse($request->start_date);
        $isHalfDay = filter_var($request->is_half_day, FILTER_VALIDATE_BOOLEAN);
        $endDate = $isHalfDay ? $startDate->copy() : Carbon::parse($request->end_date ?? $request->start_date);
        $leaveTypeId = $request->leave_type_id ? (int)$request->leave_type_id : null;

        $allocation = $this->leaveService->calculateAllocation($employee, $startDate, $endDate, $leaveTypeId, $isHalfDay);

        return response()->json([
            'success' => true,
            'allocation' => $allocation,
        ]);
    }

    public function store(Request $request)
    {
        $isHalfDay = filter_var($request->is_half_day, FILTER_VALIDATE_BOOLEAN);
        if ($isHalfDay) {
            $request->merge(['end_date' => $request->start_date]);
        } else {
            $request->merge(['half_day_type' => null]);
        }

        $systemStartDate = config('hrms.system_start_date', '2026-08-01');
        $request->validate([
            'start_date'    => 'required|date|after_or_equal:' . $systemStartDate,
            'end_date'      => 'required|date|after_or_equal:start_date',
            'leave_type_id' => 'required|exists:leave_types,id',
            'reason'        => 'nullable|string|max:500',
            'is_half_day'   => 'nullable|boolean',
            'half_day_type' => 'required_if:is_half_day,1,true|nullable|string',
        ], [
            'start_date.after_or_equal' => 'Leave tracking starts from August 1, 2026.',
        ]);

        $employee = Auth::guard('employee')->user()->employee;

        $leaveRequest = $this->leaveService->processLeaveSubmission($employee, $request->all());

        // Notify Team Leaders
        $tls = Employee::where('department_id', $employee->department_id)
            ->where('is_teamleader', true)
            ->get();

        foreach ($tls as $tl) {
            if ($tl->employeeLogin) {
                $tl->employeeLogin->notify(new LeaveRequestSubmitted($leaveRequest, "{$employee->name} has applied for leave."));
            }
        }

        return redirect()->route('employee.leaves.index')->with('success', 'Leave request submitted successfully!');
    }

    public function uploadCertificate(Request $request, LeaveRequest $leave)
    {
        $employee = Auth::guard('employee')->user()->employee;
        if ($leave->employee_id !== $employee->id) {
            abort(403);
        }

        $request->validate([
            'certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        if ($request->hasFile('certificate')) {
            $path = $request->file('certificate')->store('medical_certificates', 'public');
            $leave->update([
                'medical_certificate_path' => $path,
                'status' => 'Certificate_Uploaded'
            ]);
        }

        return redirect()->route('employee.leaves.index')->with('success', 'Medical certificate uploaded successfully!');
    }
}
