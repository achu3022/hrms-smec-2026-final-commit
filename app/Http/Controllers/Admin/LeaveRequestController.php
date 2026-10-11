<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class LeaveRequestController extends Controller
{
    // Helper method to apply filters
    private function applyFilters($query, Request $request)
    {
        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }
        
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date Filtering Logic
        if ($request->filled('date_filter_type')) {
            $type = $request->date_filter_type;
            
            if ($type === 'year' && $request->filled('year')) {
                $query->whereYear('start_date', $request->year);
            } 
            elseif ($type === 'month' && $request->filled('month') && $request->filled('year')) {
                $query->whereYear('start_date', $request->year)
                      ->whereMonth('start_date', $request->month);
            }
            elseif ($type === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date]);
            }
        } else {
            // Default to current year if no date filter type is selected
            if (!$request->has('clear_filters')) {
                $query->whereYear('start_date', date('Y'));
            }
        }

        return $query;
    }

    public function index(Request $request)
    {
        $baseQuery = LeaveRequest::with(['employee.departmentRel', 'employee.designationRel']);
        
        // Apply Filters
        $query = $this->applyFilters(clone $baseQuery, $request);
        
        // Get Stats Query (Clone before pagination)
        $statsQuery = clone $query;
        
        // Get Paginated Results
        $leaves = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Summary Statistics (calculated from the filtered dataset)
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'pending' => (clone $statsQuery)->whereIn('status', ['Pending', 'TL Approved', 'Certificate_Uploaded'])->count(),
            'tl_approved' => (clone $statsQuery)->where('status', 'TL Approved')->count(),
            'approved' => (clone $statsQuery)->where('status', 'Approved')->count(),
            'rejected' => (clone $statsQuery)->where('status', 'Rejected')->count(),
            'on_leave_today' => LeaveRequest::where('status', 'Approved')
                                ->whereDate('start_date', '<=', today())
                                ->whereDate('end_date', '>=', today())
                                ->count()
        ];

        // Filter Options
        $departments = \App\Models\Department::all();
        $employees = Employee::where('status', 'Active')->orderBy('name')->get();

        return view('admin.leave.index', compact('leaves', 'stats', 'departments', 'employees', 'request'));
    }

    public function export(Request $request)
    {
        $query = LeaveRequest::with(['employee.departmentRel', 'employee.designationRel']);
        $query = $this->applyFilters($query, $request);
        $leaves = $query->orderBy('start_date', 'desc')->get();

        $filename = "leave_report_" . date('Y-m-d_H-i-s') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Employee ID', 'Employee Name', 'Department', 'Start Date', 'End Date', 'Total Days', 'Status', 'Reason'];

        $callback = function() use($leaves, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($leaves as $leave) {
                fputcsv($file, [
                    $leave->employee->employee_id ?? 'N/A',
                    $leave->employee->name ?? 'N/A',
                    $leave->employee->departmentRel->name ?? 'N/A',
                    $leave->start_date,
                    $leave->end_date,
                    $leave->total_days,
                    $leave->status,
                    $leave->reason
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function approve($id)
    {
        $leave = LeaveRequest::findOrFail($id);

        if ($leave->leaveType && $leave->leaveType->name === 'Medical' && !$leave->medical_certificate_path) {
            return back()->with('error', 'Cannot approve Medical Leave without a certificate.');
        }

        $leaveAllocationService = app(\App\Services\LeaveAllocationService::class);
        $leaveAllocationService->approveLeaveRequest($leave);
        
        $requester = $leave->employee;
        if ($requester && $requester->employeeLogin) {
            $requester->employeeLogin->notify(new \App\Notifications\LeaveRequestStatusUpdated($leave, "Your leave request was Approved by Management."));
        }
        
        return back()->with('success', 'Leave request fully approved.');
    }

    public function reject($id)
    {
        $leave = LeaveRequest::findOrFail($id);
        $leave->update(['status' => 'Rejected']);
        
        $leaveAllocationService = app(\App\Services\LeaveAllocationService::class);
        $leaveAllocationService->syncLeaveRequestBalances($leave);
        $leaveAllocationService->removeLeaveRequestAttendance($leave);

        $requester = $leave->employee;
        if ($requester && $requester->employeeLogin) {
            $requester->employeeLogin->notify(new \App\Notifications\LeaveRequestStatusUpdated($leave, "Your leave request was Rejected by Management."));
        }
        
        return back()->with('success', 'Leave request rejected.');
    }

    public function destroy($id)
    {
        $leave = LeaveRequest::findOrFail($id);
        $leaveAllocationService = app(\App\Services\LeaveAllocationService::class);
        $leaveAllocationService->removeLeaveRequestAttendance($leave);
        LeaveRequest::destroy($id);
        
        return back()->with('success', 'Leave Request deleted.');
    }
}
