<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;

class AttendanceController extends Controller
{
    // Helper method to apply filters and generate combined dataset
    private function getFilteredAttendanceData(Request $request)
    {
        // 1. Sync all approved leaves into attendance records
        try {
            $leaveService = app(\App\Services\LeaveAllocationService::class);
            if (method_exists($leaveService, 'syncAllApprovedLeavesToAttendance')) {
                $leaveService->syncAllApprovedLeavesToAttendance();
            } elseif (method_exists($leaveService, 'syncLeaveRequestAttendance')) {
                $approvedLeaves = \App\Models\LeaveRequest::where('status', 'Approved')->get();
                foreach ($approvedLeaves as $leave) {
                    $leaveService->syncLeaveRequestAttendance($leave);
                }
            } else {
                $approvedLeaves = \App\Models\LeaveRequest::where('status', 'Approved')->get();
                $holidays = \App\Models\Holiday::pluck('date')->toArray();
                foreach ($approvedLeaves as $leave) {
                    if (!$leave->employee_id || !$leave->start_date || !$leave->end_date) continue;
                    $startDate = \Carbon\Carbon::parse($leave->start_date);
                    $endDate = \Carbon\Carbon::parse($leave->end_date);
                    for ($d = $startDate->copy(); $d->lte($endDate); $d->addDay()) {
                        if ($d->isSunday()) continue;
                        $dateStr = $d->format('Y-m-d');
                        if (in_array($dateStr, $holidays)) continue;
                        $att = Attendance::where('employee_id', $leave->employee_id)->where('date', $dateStr)->first();
                        if (!$att) {
                            Attendance::create(['employee_id' => $leave->employee_id, 'date' => $dateStr, 'status' => 'Leave']);
                        } elseif (!$att->check_in) {
                            $att->update(['status' => 'Leave']);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore any errors during leave sync
        }

        // 2. Base query for actual attendance records in database
        $baseQuery = Attendance::with(['employee.departmentRel', 'employee.designationRel']);

        if ($request->filled('department_id')) {
            $baseQuery->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('employee_id')) {
            $baseQuery->where('employee_id', $request->employee_id);
        }

        $type = $request->get('date_filter_type');
        if (!$type) {
            if ($request->filled('start_date') || $request->filled('end_date')) {
                $type = 'custom';
            } else {
                $type = 'date';
            }
        } elseif ($type === 'date' && ($request->filled('start_date') || $request->filled('end_date'))) {
            $type = 'custom';
        }

        $targetDate = null;
        $datesToCover = [];
        $holidays = \App\Models\Holiday::pluck('date')->toArray();

        if ($type === 'date') {
            $targetDate = $request->get('date', date('Y-m-d'));
            $baseQuery->where('date', $targetDate);
            $tCarbon = \Carbon\Carbon::parse($targetDate);
            if (!$tCarbon->isSunday() && !in_array($targetDate, $holidays)) {
                $datesToCover[] = $targetDate;
            }
        } elseif ($type === 'month') {
            $month = $request->get('month', date('m'));
            $year = $request->get('year', date('Y'));
            $baseQuery->whereYear('date', $year)->whereMonth('date', $month);

            $sDate = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfDay();
            $eDate = $sDate->copy()->endOfMonth();
            $today = \Carbon\Carbon::today();
            if ($eDate->gt($today)) {
                $eDate = $today;
            }
            for ($d = $sDate->copy(); $d->lte($eDate); $d->addDay()) {
                if ($d->isSunday()) continue;
                $dateStr = $d->format('Y-m-d');
                if (in_array($dateStr, $holidays)) continue;
                $datesToCover[] = $dateStr;
            }
        } elseif ($type === 'year') {
            $year = $request->get('year', date('Y'));
            $baseQuery->whereYear('date', $year);

            $sDate = \Carbon\Carbon::createFromDate($year, 1, 1)->startOfDay();
            $eDate = \Carbon\Carbon::createFromDate($year, 12, 31)->startOfDay();
            $today = \Carbon\Carbon::today();
            if ($eDate->gt($today)) {
                $eDate = $today;
            }
            for ($d = $sDate->copy(); $d->lte($eDate); $d->addDay()) {
                if ($d->isSunday()) continue;
                $dateStr = $d->format('Y-m-d');
                if (in_array($dateStr, $holidays)) continue;
                $datesToCover[] = $dateStr;
            }
        } elseif ($type === 'custom') {
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $baseQuery->whereBetween('date', [$request->start_date, $request->end_date]);
                $sDate = \Carbon\Carbon::parse($request->start_date);
                $eDate = \Carbon\Carbon::parse($request->end_date);
                for ($d = $sDate->copy(); $d->lte($eDate); $d->addDay()) {
                    if ($d->isSunday()) continue;
                    $dateStr = $d->format('Y-m-d');
                    if (in_array($dateStr, $holidays)) continue;
                    $datesToCover[] = $dateStr;
                }
            } elseif ($request->filled('start_date')) {
                $baseQuery->where('date', '>=', $request->start_date);
                $sDate = \Carbon\Carbon::parse($request->start_date);
                $eDate = \Carbon\Carbon::today();
                if ($sDate->lte($eDate)) {
                    for ($d = $sDate->copy(); $d->lte($eDate); $d->addDay()) {
                        if ($d->isSunday()) continue;
                        $dateStr = $d->format('Y-m-d');
                        if (in_array($dateStr, $holidays)) continue;
                        $datesToCover[] = $dateStr;
                    }
                } else {
                    $sCarbon = \Carbon\Carbon::parse($request->start_date);
                    if (!$sCarbon->isSunday() && !in_array($request->start_date, $holidays)) {
                        $datesToCover[] = $request->start_date;
                    }
                }
            } elseif ($request->filled('end_date')) {
                $baseQuery->where('date', '<=', $request->end_date);
                $eCarbon = \Carbon\Carbon::parse($request->end_date);
                if (!$eCarbon->isSunday() && !in_array($request->end_date, $holidays)) {
                    $datesToCover[] = $request->end_date;
                }
            }
        }
        // if type === 'all', no date constraint added

        $existingAttendances = $baseQuery->get();

        // Build quick lookup map for existing attendance records (employee_id + '_' + date)
        $existingMap = [];
        foreach ($existingAttendances as $att) {
            $existingMap[$att->employee_id . '_' . $att->date] = true;
        }

        // 3. Get active employees matching filters
        $empQuery = Employee::with(['departmentRel', 'designationRel'])->where('status', 'Active');
        if ($request->filled('department_id')) {
            $empQuery->where('department_id', $request->department_id);
        }
        if ($request->filled('employee_id')) {
            $empQuery->where('id', $request->employee_id);
        }
        $activeEmployees = $empQuery->orderBy('name')->get();

        $dynamicRecords = collect();

        if (!empty($datesToCover)) {
            foreach ($datesToCover as $dateStr) {
                foreach ($activeEmployees as $emp) {
                    $key = $emp->id . '_' . $dateStr;
                    if (!isset($existingMap[$key])) {
                        $virtualAtt = new Attendance([
                            'employee_id' => $emp->id,
                            'date' => $dateStr,
                            'status' => 'Not Punched In',
                            'check_in' => null,
                            'check_out' => null,
                            'location_verified' => false,
                        ]);
                        $virtualAtt->setRelation('employee', $emp);
                        $dynamicRecords->push($virtualAtt);
                    }
                }
            }
        } elseif ($type === 'all') {
            $distinctDates = $existingAttendances->pluck('date')->unique()->toArray();
            if (empty($distinctDates)) {
                $distinctDates[] = date('Y-m-d');
            }
            foreach ($distinctDates as $dateStr) {
                foreach ($activeEmployees as $emp) {
                    $key = $emp->id . '_' . $dateStr;
                    if (!isset($existingMap[$key])) {
                        $virtualAtt = new Attendance([
                            'employee_id' => $emp->id,
                            'date' => $dateStr,
                            'status' => 'Not Punched In',
                            'check_in' => null,
                            'check_out' => null,
                            'location_verified' => false,
                        ]);
                        $virtualAtt->setRelation('employee', $emp);
                        $dynamicRecords->push($virtualAtt);
                    }
                }
            }
        }

        $allCollection = $existingAttendances->concat($dynamicRecords);

        // 4. Apply status filter if requested
        if ($request->filled('status')) {
            $selectedStatus = $request->status;
            if ($selectedStatus === 'Absent') {
                $allCollection = $allCollection->filter(fn($att) => in_array($att->status, ['Absent', 'Not Punched In']));
            } else {
                $allCollection = $allCollection->filter(fn($att) => $att->status === $selectedStatus);
            }
        }

        // Sort records by date descending, then employee name ascending
        return $allCollection->sort(function ($a, $b) {
            if ($a->date === $b->date) {
                $nameA = $a->employee->name ?? '';
                $nameB = $b->employee->name ?? '';
                return strcmp($nameA, $nameB);
            }
            return strcmp($b->date, $a->date);
        })->values();
    }

    public function index(Request $request)
    {
        // Get Results
        $attendances = $this->getFilteredAttendanceData($request);

        // Summary Statistics (calculated from the dataset)
        $stats = [
            'total' => $attendances->count(),
            'present' => $attendances->where('status', 'Present')->count(),
            'absent' => $attendances->whereIn('status', ['Absent', 'Not Punched In'])->count(),
            'late' => $attendances->where('status', 'Late')->count(),
            'half_day' => $attendances->where('status', 'Half-Day')->count(),
            'leave' => $attendances->where('status', 'Leave')->count(),
            'not_punched_in' => $attendances->where('status', 'Not Punched In')->count()
        ];

        // Filter Options
        $departments = \App\Models\Department::all();
        $employees = Employee::where('status', 'Active')->orderBy('name')->get();

        return view('admin.attendance.index', compact('attendances', 'stats', 'departments', 'employees', 'request'));
    }

    public function export(Request $request)
    {
        $attendances = $this->getFilteredAttendanceData($request);
        $filename = "attendance_report_" . date('Y-m-d_H-i-s') . ".xlsx";

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\AttendanceExport($attendances), $filename);
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

        $checkIn = !empty($request->check_in) ? $request->check_in : null;
        $checkOut = !empty($request->check_out) ? $request->check_out : null;

        Attendance::updateOrCreate(
            ['employee_id' => $request->employee_id, 'date' => $request->date],
            [
                'status' => $request->status,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
            ]
        );

        return back()->with('success', 'Attendance record saved successfully');
    }

    public function destroy($id)
    {
        Attendance::destroy($id);
        return back()->with('success', 'Attendance record deleted');
    }
}
