<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyReport;
use Carbon\Carbon;

class DailyReportController extends Controller
{
    public function index(Request $request)
    {
        $employee = auth()->guard('employee')->user()->employee;

        if (!$employee->requires_daily_report) {
            return redirect()->route('employee.dashboard')->with('error', 'You are not required to submit daily reports.');
        }

        $date = $request->query('date', Carbon::today()->format('Y-m-d'));
        
        $report = DailyReport::where('employee_id', $employee->id)
                             ->where('report_date', $date)
                             ->first();

        // If no report exists yet, or it's empty, populate with carry forward tasks
        if (!$report || empty($report->tasks)) {
            $pastReports = DailyReport::where('employee_id', $employee->id)
                                      ->where('report_date', '<', $date)
                                      ->orderBy('report_date', 'asc')
                                      ->get();
            
            $taskMap = [];
            foreach ($pastReports as $pastReport) {
                foreach ($pastReport->tasks as $task) {
                    $taskName = $task[0] ?? '';
                    if (empty($taskName)) continue;
                    
                    $status = $task[3] ?? ($task[2] ?? 'Pending');
                    $carryForwardDate = $task[5] ?? null;

                    if ($status === 'Pending' && $carryForwardDate && $carryForwardDate >= $date) {
                        $taskMap[$taskName] = $task;
                    } elseif ($status === 'Completed') {
                        unset($taskMap[$taskName]);
                    }
                }
            }
            
            $carryForwardTasks = array_values($taskMap);
            // Reset actual time to 0 for a new day
            foreach ($carryForwardTasks as &$cft) {
                $cft[2] = 0;
            }

            if (!$report) {
                $report = new DailyReport();
                $report->tasks = $carryForwardTasks;
            } else {
                $report->tasks = $carryForwardTasks;
            }
        }

        return view('employee.portal.daily-reports.index', compact('employee', 'date', 'report'));
    }

    public function store(Request $request)
    {
        $employee = auth()->guard('employee')->user()->employee;

        if (!$employee->requires_daily_report) {
            return response()->json(['success' => false, 'message' => 'Not required to submit reports.'], 403);
        }

        $request->validate([
            'report_date' => 'required|date',
            'tasks' => 'nullable|array',
        ]);

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

        DailyReport::updateOrCreate(
            ['employee_id' => $employee->id, 'report_date' => $request->report_date],
            ['tasks' => $tasks]
        );

        return response()->json(['success' => true, 'message' => 'Daily report saved successfully.']);
    }
}
