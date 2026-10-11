<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyReport;
use Carbon\Carbon;

class TeamDailyReportController extends Controller
{
    public function index(Request $request)
    {
        $employee = auth()->guard('employee')->user()->employee;
        
        $date = $request->query('date', Carbon::today()->format('Y-m-d'));

        // Fetch reports for employees in the same department
        $reports = DailyReport::with('employee')
            ->whereHas('employee', function ($query) use ($employee) {
                $query->where('department_id', $employee->department_id)
                      ->where('id', '!=', $employee->id);
            })
            ->where('report_date', $date)
            ->get();

        return view('employee.portal.team-daily-reports.index', compact('reports', 'date'));
    }
}
