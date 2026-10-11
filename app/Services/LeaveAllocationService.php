<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeLeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveSegment;
use App\Models\LeaveType;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class LeaveAllocationService
{
    /**
     * Get or compute the monthly leave balance for an employee for a specific year and month.
     * Sequentially calculates and stores prior months if they do not exist.
     */
    public function getOrCalculateMonthlyBalance(Employee $employee, int $year, int $month): EmployeeLeaveBalance
    {
        $cacheKey = "emp_balance_{$employee->id}_{$year}_{$month}";
        
        // We can cache balance in request-level memory or query DB
        $existing = EmployeeLeaveBalance::where('employee_id', $employee->id)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        if ($existing) {
            return $existing;
        }

        // Determine tracking start date: system_start_date (2026-08-01) or joining date if joined after system_start_date
        $systemStartDate = Carbon::parse(config('hrms.system_start_date', '2026-08-01'))->startOfMonth();
        $joiningDate = $employee->joining_date ? Carbon::parse($employee->joining_date)->startOfMonth() : Carbon::parse($employee->created_at)->startOfMonth();

        $effectiveStartDate = $joiningDate->gt($systemStartDate) ? $joiningDate->copy() : $systemStartDate->copy();
        $targetDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();

        if ($targetDate->lt($effectiveStartDate)) {
            $startDate = $targetDate->copy();
        } else {
            $startDate = $effectiveStartDate->copy();
        }

        $currentDate = $startDate->copy();
        $lastBalance = null;

        // If starting at month M, check if M-1 balance exists (only if M-1 is >= effectiveStartDate)
        $prevMonthDate = $startDate->copy()->subMonth();
        if ($prevMonthDate->gte($effectiveStartDate)) {
            $prevBal = EmployeeLeaveBalance::where('employee_id', $employee->id)
                ->where('year', (int)$prevMonthDate->format('Y'))
                ->where('month', (int)$prevMonthDate->format('m'))
                ->first();
            if ($prevBal) {
                $lastBalance = $prevBal;
            }
        }

        while ($currentDate->lte($targetDate)) {
            $cYear = (int)$currentDate->format('Y');
            $cMonth = (int)$currentDate->format('m');

            $bal = EmployeeLeaveBalance::where('employee_id', $employee->id)
                ->where('year', $cYear)
                ->where('month', $cMonth)
                ->first();

            if (!$bal) {
                // Calculate opening balance from previous month closing
                $openingCl = $lastBalance ? $lastBalance->closing_cl : 0.0;
                $openingMl = $lastBalance ? $lastBalance->closing_ml : 0.0;

                // Monthly credit from dynamic settings
                $maxCl = (float)\App\Models\Setting::get('max_balance_cl', config('hrms.max_balance_cl', 12.0));
                $maxMl = (float)\App\Models\Setting::get('max_balance_ml', config('hrms.max_balance_ml', 12.0));
                $monthlyCreditCl = (float)\App\Models\Setting::get('monthly_credit_cl', config('hrms.monthly_credit_cl', 1.0));
                $monthlyCreditMl = (float)\App\Models\Setting::get('monthly_credit_ml', config('hrms.monthly_credit_ml', 1.0));

                // If opening is already at max limit, credit is 0, else credit up to max limit
                $creditedCl = max(0.0, min($monthlyCreditCl, $maxCl - $openingCl));
                $creditedMl = max(0.0, min($monthlyCreditMl, $maxMl - $openingMl));

                // Used leaves in this month from approved requests
                $used = $this->getApprovedUsageForMonth($employee->id, $cYear, $cMonth);
                $usedCl = $used['casual'];
                $usedMl = $used['medical'];
                $lopDays = $used['lop'];

                $closingCl = max(0.0, min($maxCl, $openingCl + $creditedCl - $usedCl));
                $closingMl = max(0.0, min($maxMl, $openingMl + $creditedMl - $usedMl));

                $bal = EmployeeLeaveBalance::create([
                    'employee_id' => $employee->id,
                    'year' => $cYear,
                    'month' => $cMonth,
                    'opening_cl' => $openingCl,
                    'credited_cl' => $creditedCl,
                    'used_cl' => $usedCl,
                    'closing_cl' => $closingCl,
                    'opening_ml' => $openingMl,
                    'credited_ml' => $creditedMl,
                    'used_ml' => $usedMl,
                    'closing_ml' => $closingMl,
                    'lop_days' => $lopDays,
                ]);
            } else {
                // Refresh values based on current approved usage
                $this->recalculateSingleMonthBalance($bal, $lastBalance);
            }

            $lastBalance = $bal;
            $currentDate->addMonth();
        }

        return $lastBalance;
    }

    /**
     * Recalculate balance for a month given the updated usage and previous month's balance.
     */
    public function recalculateSingleMonthBalance(EmployeeLeaveBalance $bal, ?EmployeeLeaveBalance $previousBal = null): void
    {
        $openingCl = $previousBal ? $previousBal->closing_cl : $bal->opening_cl;
        $openingMl = $previousBal ? $previousBal->closing_ml : $bal->opening_ml;

        $maxCl = (float)\App\Models\Setting::get('max_balance_cl', config('hrms.max_balance_cl', 12.0));
        $maxMl = (float)\App\Models\Setting::get('max_balance_ml', config('hrms.max_balance_ml', 12.0));
        $monthlyCreditCl = (float)\App\Models\Setting::get('monthly_credit_cl', config('hrms.monthly_credit_cl', 1.0));
        $monthlyCreditMl = (float)\App\Models\Setting::get('monthly_credit_ml', config('hrms.monthly_credit_ml', 1.0));

        $creditedCl = max(0.0, min($monthlyCreditCl, $maxCl - $openingCl));
        $creditedMl = max(0.0, min($monthlyCreditMl, $maxMl - $openingMl));

        $used = $this->getApprovedUsageForMonth($bal->employee_id, $bal->year, $bal->month);
        $usedCl = $used['casual'];
        $usedMl = $used['medical'];
        $lopDays = $used['lop'];

        $closingCl = max(0.0, min($maxCl, $openingCl + $creditedCl - $usedCl));
        $closingMl = max(0.0, min($maxMl, $openingMl + $creditedMl - $usedMl));

        $bal->update([
            'opening_cl' => $openingCl,
            'credited_cl' => $creditedCl,
            'used_cl' => $usedCl,
            'closing_cl' => $closingCl,
            'opening_ml' => $openingMl,
            'credited_ml' => $creditedMl,
            'used_ml' => $usedMl,
            'closing_ml' => $closingMl,
            'lop_days' => $lopDays,
        ]);
    }

    /**
     * Recalculate and cascade forward balances for an employee starting from a given year and month.
     */
    public function syncBalancesFromMonth(Employee $employee, int $year, int $month): void
    {
        $balances = EmployeeLeaveBalance::where('employee_id', $employee->id)
            ->where(function ($q) use ($year, $month) {
                $q->where('year', '>', $year)
                  ->orWhere(function ($q2) use ($year, $month) {
                      $q2->where('year', $year)->where('month', '>=', $month);
                  });
            })
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        foreach ($balances as $bal) {
            $prevMonth = Carbon::createFromDate($bal->year, $bal->month, 1)->subMonth();
            $prevBal = EmployeeLeaveBalance::where('employee_id', $employee->id)
                ->where('year', (int)$prevMonth->format('Y'))
                ->where('month', (int)$prevMonth->format('m'))
                ->first();

            $this->recalculateSingleMonthBalance($bal, $prevBal);
        }
    }

    /**
     * Get active usage in casual, medical, and LOP days for an employee in a given month.
     * Counts all non-rejected requests (Pending, TL Approved, Certificate Uploaded, Approved).
     */
    protected function getApprovedUsageForMonth(int $employeeId, int $year, int $month): array
    {
        $segments = LeaveSegment::whereHas('leaveRequest', function ($q) use ($employeeId) {
            $q->where('employee_id', $employeeId)
              ->whereNotIn('status', ['Rejected']);
        })
        ->where('year', $year)
        ->where('month', $month)
        ->get();

        return [
            'casual' => (float)$segments->sum('casual_days'),
            'medical' => (float)$segments->sum('medical_days'),
            'lop' => (float)$segments->sum('lop_days'),
        ];
    }

    /**
     * Split a date range into monthly segments with start and end dates.
     */
    public function splitDateRange(Carbon $startDate, Carbon $endDate): array
    {
        $segments = [];
        $current = $startDate->copy()->startOfDay();
        $targetEnd = $endDate->copy()->startOfDay();

        while ($current->lte($targetEnd)) {
            $monthEnd = $current->copy()->endOfMonth()->startOfDay();
            $segmentEnd = $monthEnd->lt($targetEnd) ? $monthEnd : $targetEnd->copy();

            // Calculate business days (weekdays) and calendar days for this segment
            $workingDays = 0;
            $calendarDays = 0;
            $temp = $current->copy()->startOfDay();
            while ($temp->lte($segmentEnd)) {
                $calendarDays++;
                if (!$temp->isWeekend()) {
                    $workingDays++;
                }
                $temp->addDay();
            }

            $isSandwichRule = \App\Models\Setting::get('sandwich_leave_rule', false);
            $days = $isSandwichRule ? $calendarDays : ($workingDays > 0 ? $workingDays : $calendarDays);

            $segments[] = [
                'year' => (int)$current->format('Y'),
                'month' => (int)$current->format('m'),
                'month_name' => $current->format('F Y'),
                'start_date' => $current->format('Y-m-d'),
                'end_date' => $segmentEnd->format('Y-m-d'),
                'days' => $days,
                'working_days' => $workingDays,
                'calendar_days' => $calendarDays,
            ];

            $current = $segmentEnd->copy()->addDay()->startOfDay();
        }

        return $segments;
    }

    /**
     * Calculate month-wise allocation and available leave types for a date range.
     */
    public function calculateAllocation(Employee $employee, Carbon $startDate, Carbon $endDate, ?int $leaveTypeId = null, bool $isHalfDay = false): array
    {
        $rawSegments = $this->splitDateRange($startDate, $endDate);
        if ($isHalfDay && !empty($rawSegments)) {
            $rawSegments = [array_merge($rawSegments[0], ['days' => 0.5])];
        }
        $leaveType = $leaveTypeId ? LeaveType::find($leaveTypeId) : null;
        $leaveTypeName = $leaveType ? $leaveType->name : null;

        $totalDays = 0;
        $totalPaid = 0;
        $totalLop = 0;
        $totalCasualAllocated = 0;
        $totalMedicalAllocated = 0;

        $monthDetails = [];
        $totalAvailableClAcrossMonths = 0;
        $totalAvailableMlAcrossMonths = 0;

        // Clone tracking balances for multi-month simulation
        $runningCl = [];
        $runningMl = [];

        foreach ($rawSegments as $seg) {
            $year = $seg['year'];
            $month = $seg['month'];
            $days = $seg['days'];
            $totalDays += $days;

            $bal = $this->getOrCalculateMonthlyBalance($employee, $year, $month);

            if (!isset($runningCl["{$year}_{$month}"])) {
                $runningCl["{$year}_{$month}"] = max(0.0, $bal->opening_cl + $bal->credited_cl - $bal->used_cl);
            }
            if (!isset($runningMl["{$year}_{$month}"])) {
                $runningMl["{$year}_{$month}"] = max(0.0, $bal->opening_ml + $bal->credited_ml - $bal->used_ml);
            }

            $availCl = $runningCl["{$year}_{$month}"];
            $availMl = $runningMl["{$year}_{$month}"];

            $totalAvailableClAcrossMonths += $availCl;
            $totalAvailableMlAcrossMonths += $availMl;

            $casualAllocated = 0;
            $medicalAllocated = 0;
            $lopAllocated = 0;

            if ($leaveTypeName === 'Casual' || str_contains(strtolower($leaveTypeName ?? ''), 'casual')) {
                if ($availCl >= $days) {
                    $casualAllocated = $days;
                } else {
                    $casualAllocated = $availCl;
                    $lopAllocated = $days - $availCl;
                }
            } elseif ($leaveTypeName === 'Medical' || str_contains(strtolower($leaveTypeName ?? ''), 'medical')) {
                if ($availMl >= $days) {
                    $medicalAllocated = $days;
                } else {
                    $medicalAllocated = $availMl;
                    $lopAllocated = $days - $availMl;
                }
            } else {
                // Loss of Pay or unspecified
                $lopAllocated = $days;
            }

            // Deduct from running balances for next segments in same month
            $runningCl["{$year}_{$month}"] -= $casualAllocated;
            $runningMl["{$year}_{$month}"] -= $medicalAllocated;

            $totalCasualAllocated += $casualAllocated;
            $totalMedicalAllocated += $medicalAllocated;
            $totalLop += $lopAllocated;
            $totalPaid += ($casualAllocated + $medicalAllocated);

            $monthDetails[] = [
                'year' => $year,
                'month' => $month,
                'month_name' => $seg['month_name'],
                'start_date' => $seg['start_date'],
                'end_date' => $seg['end_date'],
                'days' => $days,
                'available_cl' => $availCl,
                'available_ml' => $availMl,
                'casual_allocated' => $casualAllocated,
                'medical_allocated' => $medicalAllocated,
                'lop_allocated' => $lopAllocated,
            ];
        }

        // Target month info (first month in selection)
        $primaryMonthName = $monthDetails[0]['month_name'] ?? $startDate->format('F Y');
        $primaryAvailCl = $monthDetails[0]['available_cl'] ?? 0;
        $primaryAvailMl = $monthDetails[0]['available_ml'] ?? 0;

        // Determine leave type availability rules
        $allLeaveTypes = LeaveType::where('is_active', true)->get();
        $availableTypes = [];

        foreach ($allLeaveTypes as $type) {
            $isCasual = str_contains(strtolower($type->name), 'casual');
            $isMedical = str_contains(strtolower($type->name), 'medical');
            $isLop = str_contains(strtolower($type->name), 'loss of pay') || str_contains(strtolower($type->name), 'lop');

            if ($isLop) {
                $availableTypes[] = [
                    'id' => $type->id,
                    'name' => $type->name,
                    'available_days' => 'Unlimited',
                    'is_disabled' => false,
                ];
            } elseif ($isCasual) {
                if ($totalAvailableClAcrossMonths > 0) {
                    $availableTypes[] = [
                        'id' => $type->id,
                        'name' => $type->name,
                        'available_days' => $totalAvailableClAcrossMonths,
                        'is_disabled' => false,
                    ];
                }
            } elseif ($isMedical) {
                if ($totalAvailableMlAcrossMonths > 0) {
                    $availableTypes[] = [
                        'id' => $type->id,
                        'name' => $type->name,
                        'available_days' => $totalAvailableMlAcrossMonths,
                        'is_disabled' => false,
                    ];
                }
            } else {
                // Other leave types
                $availableTypes[] = [
                    'id' => $type->id,
                    'name' => $type->name,
                    'available_days' => 0,
                    'is_disabled' => false,
                ];
            }
        }

        return [
            'total_days' => $totalDays,
            'paid_days' => $totalPaid,
            'lop_days' => $totalLop,
            'casual_days' => $totalCasualAllocated,
            'medical_days' => $totalMedicalAllocated,
            'month_segments' => $monthDetails,
            'available_leave_types' => $availableTypes,
            'primary_month_name' => $primaryMonthName,
            'primary_available_cl' => $primaryAvailCl,
            'primary_available_ml' => $primaryAvailMl,
            'summary_balances' => [
                'available_cl' => $totalAvailableClAcrossMonths,
                'available_ml' => $totalAvailableMlAcrossMonths,
            ]
        ];
    }

    /**
     * Create leave request with month-wise segments inside a DB transaction.
     */
    public function processLeaveSubmission(Employee $employee, array $data): LeaveRequest
    {
        return DB::transaction(function () use ($employee, $data) {
            $startDate = Carbon::parse($data['start_date']);
            $endDate = Carbon::parse($data['end_date']);
            $leaveTypeId = $data['leave_type_id'];
            $isHalfDay = !empty($data['is_half_day']);
            $halfDayType = $data['half_day_type'] ?? null;

            if ($isHalfDay) {
                $endDate = $startDate->copy();
                $data['end_date'] = $data['start_date'];
            }

            $allocation = $this->calculateAllocation($employee, $startDate, $endDate, $leaveTypeId, $isHalfDay);

            $leaveRequest = LeaveRequest::create([
                'employee_id' => $employee->id,
                'leave_type_id' => $leaveTypeId,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'is_half_day' => $isHalfDay,
                'half_day_type' => $halfDayType,
                'total_days' => $allocation['total_days'],
                'paid_days' => $allocation['paid_days'],
                'lop_days' => $allocation['lop_days'],
                'reason' => $data['reason'] ?? null,
                'status' => 'Pending',
            ]);

            foreach ($allocation['month_segments'] as $seg) {
                LeaveSegment::create([
                    'leave_request_id' => $leaveRequest->id,
                    'year' => $seg['year'],
                    'month' => $seg['month'],
                    'start_date' => $seg['start_date'],
                    'end_date' => $seg['end_date'],
                    'total_days' => $seg['days'],
                    'casual_days' => $seg['casual_allocated'],
                    'medical_days' => $seg['medical_allocated'],
                    'lop_days' => $seg['lop_allocated'],
                ]);
            }

            // Sync monthly balance cache immediately
            $this->syncLeaveRequestBalances($leaveRequest);

            return $leaveRequest;
        });
    }

    /**
     * Sync leave balances for all month segments of a leave request.
     */
    public function syncLeaveRequestBalances(LeaveRequest $leaveRequest): void
    {
        $employee = $leaveRequest->employee;
        if (!$employee) return;

        foreach ($leaveRequest->segments as $seg) {
            $this->getOrCalculateMonthlyBalance($employee, $seg->year, $seg->month);
            $this->syncBalancesFromMonth($employee, $seg->year, $seg->month);
        }
    }

    /**
     * Sync attendance records for an approved leave request.
     * Creates or updates attendance records with status 'Leave' or 'Half-Day' for all dates in the leave period.
     */
    public function syncLeaveRequestAttendance(LeaveRequest $leaveRequest): void
    {
        if ($leaveRequest->status !== 'Approved' || !$leaveRequest->employee_id) {
            return;
        }

        $startDate = Carbon::parse($leaveRequest->start_date);
        $endDate = Carbon::parse($leaveRequest->end_date);
        $holidays = \App\Models\Holiday::pluck('date')->toArray();

        $attStatus = $leaveRequest->is_half_day ? 'Half-Day' : 'Leave';

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if ($date->isSunday()) continue;
            $dateStr = $date->format('Y-m-d');
            if (in_array($dateStr, $holidays)) continue;

            $att = \App\Models\Attendance::where('employee_id', $leaveRequest->employee_id)
                ->where('date', $dateStr)
                ->first();

            if (!$att) {
                \App\Models\Attendance::create([
                    'employee_id' => $leaveRequest->employee_id,
                    'date' => $dateStr,
                    'status' => $attStatus,
                    'check_in' => null,
                    'check_out' => null,
                ]);
            } else {
                if (!$att->check_in) {
                    $att->update(['status' => $attStatus]);
                }
            }
        }
    }

    /**
     * Remove attendance records created for a leave request if it is rejected or deleted.
     */
    public function removeLeaveRequestAttendance(LeaveRequest $leaveRequest): void
    {
        if (!$leaveRequest->employee_id) return;

        $startDate = Carbon::parse($leaveRequest->start_date);
        $endDate = Carbon::parse($leaveRequest->end_date);

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $att = \App\Models\Attendance::where('employee_id', $leaveRequest->employee_id)
                ->where('date', $dateStr)
                ->where('status', 'Leave')
                ->whereNull('check_in')
                ->first();

            if ($att) {
                $att->delete();
            }
        }
    }

    /**
     * Sync all approved leave requests in the system to attendance records.
     */
    public function syncAllApprovedLeavesToAttendance(): void
    {
        $approvedLeaves = LeaveRequest::where('status', 'Approved')->get();
        foreach ($approvedLeaves as $leave) {
            $this->syncLeaveRequestAttendance($leave);
        }
    }

    /**
     * Approve leave request and update monthly balances.
     */
    public function approveLeaveRequest(LeaveRequest $leaveRequest): void
    {
        DB::transaction(function () use ($leaveRequest) {
            $leaveRequest->update([
                'status' => 'Approved',
                'management_approved_at' => now(),
            ]);

            $this->syncLeaveRequestBalances($leaveRequest);
            $this->syncLeaveRequestAttendance($leaveRequest);
        });
    }

    /**
     * Summary stats for employee dashboard.
     */
    public function getEmployeeDashboardBalances(Employee $employee, ?Carbon $targetDate = null): array
    {
        $date = $targetDate ?? now();
        $year = (int)$date->format('Y');
        $month = (int)$date->format('m');

        $bal = $this->getOrCalculateMonthlyBalance($employee, $year, $month);

        $totalAvailableCl = max(0.0, $bal->opening_cl + $bal->credited_cl - $bal->used_cl);
        $totalAvailableMl = max(0.0, $bal->opening_ml + $bal->credited_ml - $bal->used_ml);

        return [
            'month_name' => $date->format('F Y'),
            'casual' => [
                'carry_forward' => $bal->opening_cl,
                'credit' => $bal->credited_cl,
                'used' => $bal->used_cl,
                'available' => $totalAvailableCl,
            ],
            'medical' => [
                'carry_forward' => $bal->opening_ml,
                'credit' => $bal->credited_ml,
                'used' => $bal->used_ml,
                'available' => $totalAvailableMl,
            ],
            'lop' => [
                'used' => $bal->lop_days,
                'available' => 'Unlimited',
            ]
        ];
    }
}
