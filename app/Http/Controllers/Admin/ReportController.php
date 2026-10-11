<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EarlyGoingRequest;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function leaves(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $employeeId = $request->input('employee_id', 'all');

        $allEmployees = Employee::where('status', 'Active')->orderBy('name')->get();

        $leavesQuery = LeaveRequest::with('employee', 'leaveType')
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->where('status', 'Approved');

        if ($employeeId !== 'all') {
            $leavesQuery->where('employee_id', $employeeId);
        }

        $leaves = $leavesQuery->get();

        return view('admin.reports.leaves', compact('leaves', 'month', 'year', 'allEmployees', 'employeeId'));
    }

    public function attendance(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $employeeId = $request->input('employee_id', 'all');

        $allEmployees = Employee::where('status', 'Active')->orderBy('name')->get();

        $employeesQuery = Employee::where('status', 'Active');
        if ($employeeId !== 'all') {
            $employeesQuery->where('id', $employeeId);
        }
        $employees = $employeesQuery->get();
        
        $reportData = [];

        foreach ($employees as $employee) {
            // Get attendance for the month
            $attendances = Attendance::where('employee_id', $employee->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get();

            $totalLateMinutes = 0;
            $totalEarlyMinutes = 0;

            foreach ($attendances as $att) {
                if ($att->check_in) {
                    $checkInTime = Carbon::parse($att->check_in);
                    $standardStart = Carbon::parse('09:00:00');
                    if ($checkInTime->gt($standardStart)) {
                        $totalLateMinutes += $standardStart->diffInMinutes($checkInTime);
                    }
                }
                
                if ($att->check_out) {
                    $checkOutTime = Carbon::parse($att->check_out);
                    $standardEnd = Carbon::parse('18:00:00');
                    if ($checkOutTime->lt($standardEnd)) {
                        $totalEarlyMinutes += $checkOutTime->diffInMinutes($standardEnd);
                    }
                }
            }

            // Early going requests
            $earlyGoings = EarlyGoingRequest::where('employee_id', $employee->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('status', 'Approved')
                ->get();
            
            $approvedEarlyGoingCount = $earlyGoings->count();

            if ($totalLateMinutes > 0 || $totalEarlyMinutes > 0 || $approvedEarlyGoingCount > 0) {
                $reportData[] = [
                    'employee' => $employee,
                    'total_late_hours' => floor($totalLateMinutes / 60) . 'h ' . ($totalLateMinutes % 60) . 'm',
                    'total_early_hours' => floor($totalEarlyMinutes / 60) . 'h ' . ($totalEarlyMinutes % 60) . 'm',
                    'approved_early_goings' => $approvedEarlyGoingCount,
                ];
            }
        }

        return view('admin.reports.attendance', compact('reportData', 'month', 'year', 'allEmployees', 'employeeId'));
    }

    public function tasks(Request $request)
    {
        $allEmployees = Employee::where('status', 'Active')->orderBy('name')->get();
        
        $employeeId = $request->input('employee_id');
        $dateFilter = $request->input('date_filter', 'today'); // 'today', 'week', 'month', 'custom'
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $statusFilter = $request->input('status', 'All'); // 'All', 'Pending', 'In Progress', 'Completed'

        // Calculate Date Range
        $queryStartDate = null;
        $queryEndDate = null;

        if ($dateFilter === 'today') {
            $queryStartDate = Carbon::today()->toDateString();
            $queryEndDate = Carbon::today()->toDateString();
        } elseif ($dateFilter === 'week') {
            $queryStartDate = Carbon::now()->startOfWeek()->toDateString();
            $queryEndDate = Carbon::now()->endOfWeek()->toDateString();
        } elseif ($dateFilter === 'month') {
            $queryStartDate = Carbon::now()->startOfMonth()->toDateString();
            $queryEndDate = Carbon::now()->endOfMonth()->toDateString();
        } elseif ($dateFilter === 'custom') {
            $queryStartDate = $startDate;
            $queryEndDate = $endDate;
        }

        $reportData = [];
        $totalHours = 0;
        $totalTasks = 0;

        if ($employeeId) {
            $query = \App\Models\DailyReport::where('employee_id', $employeeId);
            
            if ($queryStartDate && $queryEndDate) {
                $query->whereBetween('report_date', [$queryStartDate, $queryEndDate]);
            }
            
            $dailyReports = $query->orderBy('report_date', 'desc')->get();

            foreach ($dailyReports as $report) {
                if (is_array($report->tasks)) {
                    foreach ($report->tasks as $taskRow) {
                        // Skip empty rows
                        if (empty($taskRow) || (!isset($taskRow[0]) && !isset($taskRow[1]) && !isset($taskRow[2]))) {
                            continue;
                        }

                        $taskStatus = $taskRow[2] ?? 'Pending';
                        
                        // Apply status filter
                        if ($statusFilter !== 'All' && $taskStatus !== $statusFilter) {
                            continue;
                        }

                        $taskHours = floatval($taskRow[1] ?? 0);
                        $totalHours += $taskHours;
                        $totalTasks++;

                        $reportData[] = [
                            'date' => Carbon::parse($report->report_date)->format('Y-m-d'),
                            'description' => $taskRow[0] ?? '',
                            'hours' => $taskHours,
                            'status' => $taskStatus,
                            'remarks' => $taskRow[3] ?? ''
                        ];
                    }
                }
            }
        }

        return view('admin.reports.tasks', compact(
            'allEmployees', 'employeeId', 'dateFilter', 'startDate', 'endDate', 'statusFilter',
            'reportData', 'totalHours', 'totalTasks', 'queryStartDate', 'queryEndDate'
        ));
    }
}
