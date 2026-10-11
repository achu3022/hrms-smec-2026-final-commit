<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function leaves()
    {
        $employee = auth()->guard('employee')->user()->employee;
        $departmentId = $employee->department_id;
        
        $requests = \App\Models\LeaveRequest::whereHas('employee', function ($q) use ($departmentId, $employee) {
            $q->where('department_id', $departmentId)
              ->where('id', '!=', $employee->id);
        })->orderByDesc('created_at')->paginate(15)->withQueryString();
        
        return view('employee.portal.team.leaves', compact('employee', 'requests'));
    }

    public function updateLeaveStatus(Request $request, $id)
    {
        $employee = auth()->guard('employee')->user()->employee;
        $departmentId = $employee->department_id;
        
        $leaveRequest = \App\Models\LeaveRequest::whereHas('employee', function ($q) use ($departmentId) {
            $q->where('department_id', $departmentId);
        })->findOrFail($id);

        $request->validate([
            'status' => 'required|in:TL Approved,Rejected',
        ]);

        $leaveRequest->update(['status' => $request->status]);

        // Notify Employee and Management
        $requester = $leaveRequest->employee;
        if ($request->status === 'TL Approved') {
            if ($requester->employeeLogin) {
                $requester->employeeLogin->notify(new \App\Notifications\LeaveRequestStatusUpdated($leaveRequest, "Your leave request was TL Approved. Awaiting Management approval."));
            }
            // Notify Admin
            $admins = \App\Models\User::role(['Super Admin', 'HR Manager'])->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\LeaveRequestStatusUpdated($leaveRequest, "Team Leader {$employee->name} approved leave for {$requester->name}."));
            }
        } else {
            if ($requester->employeeLogin) {
                $requester->employeeLogin->notify(new \App\Notifications\LeaveRequestStatusUpdated($leaveRequest, "Your leave request was Rejected by Team Leader {$employee->name}."));
            }
        }

        return back()->with('success', 'Leave request status updated.');
    }

    public function attendance(Request $request)
    {
        $employee = auth()->guard('employee')->user()->employee;
        $departmentId = $employee->department_id;
        
        $date = $request->input('date', now()->format('Y-m-d'));

        $teamMembers = \App\Models\Employee::where('department_id', $departmentId)
            ->where('id', '!=', $employee->id)
            ->where('status', 'Active')
            ->get();
            
        $attendances = \App\Models\Attendance::whereIn('employee_id', $teamMembers->pluck('id'))
            ->where('date', $date)
            ->get()
            ->keyBy('employee_id');

        return view('employee.portal.team.attendance', compact('employee', 'teamMembers', 'attendances', 'date'));
    }

    public function updateAttendance(Request $request)
    {
        $employee = auth()->guard('employee')->user()->employee;
        $departmentId = $employee->department_id;
        
        $request->validate([
            'date' => 'required|date',
            'employee_id' => 'required|exists:employees,id',
            'status' => 'required|in:Present,Absent,Half-Day,Leave,Late',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
        ]);

        // Check if target employee belongs to the team leader's department
        $targetEmployee = \App\Models\Employee::where('department_id', $departmentId)->findOrFail($request->employee_id);

        \App\Models\Attendance::updateOrCreate(
            ['employee_id' => $targetEmployee->id, 'date' => $request->date],
            [
                'status' => $request->status,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
            ]
        );

        return back()->with('success', 'Attendance updated successfully.');
    }

    public function earlyGoing()
    {
        $employee = auth()->guard('employee')->user()->employee;
        $departmentId = $employee->department_id;
        
        $requests = \App\Models\EarlyGoingRequest::whereHas('employee', function ($q) use ($departmentId, $employee) {
            $q->where('department_id', $departmentId)
              ->where('id', '!=', $employee->id);
        })->orderByDesc('created_at')->get();
        
        return view('employee.portal.team.early_going', compact('employee', 'requests'));
    }

    public function updateEarlyGoingStatus(Request $request, $id)
    {
        $employee = auth()->guard('employee')->user()->employee;
        $departmentId = $employee->department_id;
        
        $earlyRequest = \App\Models\EarlyGoingRequest::whereHas('employee', function ($q) use ($departmentId) {
            $q->where('department_id', $departmentId);
        })->findOrFail($id);

        $request->validate([
            'status' => 'required|in:TL Approved,Rejected',
        ]);

        $earlyRequest->update([
            'status' => $request->status,
            'tl_approved_at' => $request->status === 'TL Approved' ? now() : null,
        ]);

        // Notify Employee and Management
        $requester = $earlyRequest->employee;
        if ($request->status === 'TL Approved') {
            if ($requester->employeeLogin) {
                $requester->employeeLogin->notify(new \App\Notifications\EarlyGoingStatusUpdated($earlyRequest, "Your early going request was TL Approved. Awaiting Management approval."));
            }
            // Notify Admin
            $admins = \App\Models\User::role(['Super Admin', 'HR Manager'])->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\EarlyGoingStatusUpdated($earlyRequest, "Team Leader {$employee->name} approved early going request for {$requester->name}."));
            }
        } else {
            if ($requester->employeeLogin) {
                $requester->employeeLogin->notify(new \App\Notifications\EarlyGoingStatusUpdated($earlyRequest, "Your early going request was Rejected by Team Leader {$employee->name}."));
            }
        }

        return back()->with('success', 'Early going request status updated.');
    }
}
