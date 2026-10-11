<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyReport;
use App\Models\Employee;
use Carbon\Carbon;

class DailyReportController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date', Carbon::today()->format('Y-m-d'));
        $tab = $request->query('tab', 'reports');
        $employeeId = $request->query('employee_id');

        $employees = Employee::with('departmentRel')->get();
        
        $query = DailyReport::with('employee')->where('report_date', $date);
        
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }
        
        $reports = $query->get();

        return view('admin.daily-reports.index', compact('employees', 'reports', 'date', 'tab', 'employeeId'));
    }

    public function toggleRequirement(Request $request, $id)
    {
        // Only Admin (not HR) should modify settings, let's verify via middleware or simple check
        if (!auth()->user()->hasRole('Super Admin')) {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        $employee = Employee::findOrFail($id);
        $employee->update(['requires_daily_report' => !$employee->requires_daily_report]);
        
        return redirect()->back()->with('success', 'Setting updated for ' . $employee->name);
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasRole('Super Admin')) {
            return response()->json(['success' => false, 'message' => 'HR cannot edit reports.'], 403);
        }
        
        $tasks = $request->tasks ?? [];
        $totalEst = 0;
        $totalAct = 0;
        foreach ($tasks as $task) {
            $totalEst += floatval($task[1] ?? 0);
            $totalAct += floatval($task[2] ?? 0);
            
            $status = $task[3] ?? null;
            $carryFwdDate = $task[5] ?? null;
            if ($status === 'Pending' && empty($carryFwdDate)) {
                return response()->json(['success' => false, 'message' => 'All tasks marked as "Pending" strictly require a Carry Forward Date.'], 422);
            }
        }

        if ($totalEst != 9 || $totalAct != 9) {
            return response()->json(['success' => false, 'message' => 'Total estimated time and actual time must both equal exactly 9 hours.'], 422);
        }

        $report = DailyReport::findOrFail($id);
        $report->update(['tasks' => $tasks]);

        return response()->json(['success' => true, 'message' => 'Report updated successfully.']);
    }
}
