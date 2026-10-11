<?php

use App\Http\Controllers\EmployeeFormController;
use App\Http\Controllers\ProfileController;

Route::redirect('/', '/employee/login');
Route::view('/terms-and-conditions', 'employee.terms')->name('terms');

// Front Office QR Kiosk Display Routes
Route::get('/qr-kiosk', [\App\Http\Controllers\QrAttendanceController::class, 'showKiosk'])->name('qr.kiosk');
Route::get('/api/qr-kiosk/active-token', [\App\Http\Controllers\QrAttendanceController::class, 'getKioskToken'])->name('api.qr.kiosk-token');

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;

Route::middleware(['auth', 'role:Super Admin|HR Manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/blood-group', [DashboardController::class, 'searchBloodGroup'])->name('dashboard.blood-group');
    Route::post('employees/import', [EmployeeController::class, 'import'])->name('employees.import');
    Route::resource('employees', EmployeeController::class);
    Route::post('employees/{employee}/status', [EmployeeController::class, 'updateStatus'])->name('employees.status');
    Route::get('employees/{employee}/id-card', [EmployeeController::class, 'idCard'])->name('employees.id-card');
    Route::get('employees/{employee}/experience-certificate', [EmployeeController::class, 'experienceCertificate'])->name('employees.experience-certificate');
    
    Route::resource('team-leaders', \App\Http\Controllers\Admin\TeamLeaderController::class)->only(['index', 'store', 'destroy']);

    Route::resource('holidays', \App\Http\Controllers\Admin\HolidayController::class)->except(['create', 'show', 'edit', 'update']);

    Route::resource('leave-types', \App\Http\Controllers\Admin\LeaveTypeController::class)->except(['create', 'show', 'edit']);
    
    // Leave Requests
    Route::get('leave/export', [\App\Http\Controllers\Admin\LeaveRequestController::class, 'export'])->name('leave.export');
    Route::get('leave', [\App\Http\Controllers\Admin\LeaveRequestController::class, 'index'])->name('leave.index');
    Route::delete('leave/{leave}', [\App\Http\Controllers\Admin\LeaveRequestController::class, 'destroy'])->name('leave.destroy');
    Route::post('leave/{leave}/approve', [\App\Http\Controllers\Admin\LeaveRequestController::class, 'approve'])->name('leave.approve');
    Route::post('leave/{leave}/reject', [\App\Http\Controllers\Admin\LeaveRequestController::class, 'reject'])->name('leave.reject');
    
    // Reports
    Route::get('reports/leaves', [\App\Http\Controllers\Admin\ReportController::class, 'leaves'])->name('reports.leaves');
    Route::get('reports/attendance', [\App\Http\Controllers\Admin\ReportController::class, 'attendance'])->name('reports.attendance');
    Route::get('reports/tasks', [\App\Http\Controllers\Admin\ReportController::class, 'tasks'])->name('reports.tasks');

    // Early Going
    Route::get('early-going', [\App\Http\Controllers\Admin\EarlyGoingController::class, 'index'])->name('early-going.index');
    Route::post('early-going/{id}/update', [\App\Http\Controllers\Admin\EarlyGoingController::class, 'update'])->name('early-going.update');
    
    // Attendance
    Route::get('attendance/export', [\App\Http\Controllers\Admin\AttendanceController::class, 'export'])->name('attendance.export');
    Route::resource('attendance', \App\Http\Controllers\Admin\AttendanceController::class)->except(['create', 'show', 'edit', 'update']);
    Route::resource('assets', \App\Http\Controllers\Admin\AssetController::class)->except(['create', 'show', 'edit']);
    Route::post('assets/{asset}/assign', [\App\Http\Controllers\Admin\AssetController::class, 'assign'])->name('assets.assign');
    Route::post('assets/return/{assignment}', [\App\Http\Controllers\Admin\AssetController::class, 'returnAsset'])->name('assets.return');

    // Daily Reports (Admin)
    Route::get('/daily-reports', [\App\Http\Controllers\Admin\DailyReportController::class, 'index'])->name('daily-reports.index');
    Route::post('/daily-reports/toggle/{id}', [\App\Http\Controllers\Admin\DailyReportController::class, 'toggleRequirement'])->name('daily-reports.toggle');
    Route::post('/daily-reports/{id}', [\App\Http\Controllers\Admin\DailyReportController::class, 'update'])->name('daily-reports.update');

    Route::resource('api-tokens', \App\Http\Controllers\Admin\ApiTokenController::class)->only(['index', 'store', 'destroy']);

    Route::get('/organization/{type}', [\App\Http\Controllers\Admin\OrganizationController::class, 'index'])->name('organization.index');
    Route::post('/organization/{type}', [\App\Http\Controllers\Admin\OrganizationController::class, 'store'])->name('organization.store');
    Route::put('/organization/{type}/{id}', [\App\Http\Controllers\Admin\OrganizationController::class, 'update'])->name('organization.update');
    Route::delete('/organization/{type}/{id}', [\App\Http\Controllers\Admin\OrganizationController::class, 'destroy'])->name('organization.destroy');
    
    // Broadcast Announcements (Admin)
    Route::resource('announcements', \App\Http\Controllers\Admin\AnnouncementController::class)->only(['index', 'store', 'destroy']);
    Route::post('/announcements/{id}/toggle', [\App\Http\Controllers\Admin\AnnouncementController::class, 'toggleStatus'])->name('announcements.toggle');

    // Office Locations
    Route::get('office-locations', [\App\Http\Controllers\Admin\OfficeLocationController::class, 'index'])->name('office-locations.index');
    Route::put('office-locations/{id}', [\App\Http\Controllers\Admin\OfficeLocationController::class, 'update'])->name('office-locations.update');
    
    // System Settings
    Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    
    // Notifications
    Route::post('/notifications/{id}/mark-as-read', function($id) {
        auth()->user()->notifications()->where('id', $id)->first()?->markAsRead();
        return back();
    })->name('notifications.markAsRead');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Employee Portal Routes
Route::prefix('employee')->name('employee.')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Employee\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Employee\AuthController::class, 'login']);
    Route::post('/logout', [\App\Http\Controllers\Employee\AuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth:employee', 'employee_password_reset'])->group(function () {
        // Profile completion route
        Route::get('/complete-profile', [App\Http\Controllers\EmployeeFormController::class, 'create'])->name('complete-profile');
        Route::post('/complete-profile', [App\Http\Controllers\EmployeeFormController::class, 'store'])->name('complete-profile.store');

        Route::get('/reset-password', [\App\Http\Controllers\Employee\AuthController::class, 'showResetPasswordForm'])->name('password.reset')->withoutMiddleware('employee_password_reset');
        Route::post('/reset-password', [\App\Http\Controllers\Employee\AuthController::class, 'updatePassword'])->name('password.update')->withoutMiddleware('employee_password_reset');

        Route::middleware(['employee_profile_completed'])->group(function () {
            Route::get('/dashboard', function () {
            $employee = auth()->guard('employee')->user()->employee;
            $todayAttendance = \App\Models\Attendance::where('employee_id', $employee->id)->whereDate('date', \Carbon\Carbon::today())->first();
            
            // Active targeted announcements for employee
            $activeAnnouncements = \App\Models\Announcement::with(['department', 'employees'])
                ->where('is_active', true)
                ->where(function($q) use ($employee) {
                    $q->where('target_type', 'all')
                      ->orWhere(function($sub) use ($employee) {
                          if ($employee && $employee->department_id) {
                              $sub->where('target_type', 'department')
                                  ->where('department_id', $employee->department_id);
                          }
                      })
                      ->orWhere(function($sub) use ($employee) {
                          if ($employee) {
                              $sub->where('target_type', 'individual')
                                  ->whereHas('employees', function($empQuery) use ($employee) {
                                      $empQuery->where('employees.id', $employee->id);
                                  });
                          }
                      });
                })
                ->where(function($q) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>=', now());
                })
                ->latest()
                ->get();

            // Calculate Weekly Chart Data
            $startOfWeek = \Carbon\Carbon::now()->startOfWeek();
            $endOfWeek   = \Carbon\Carbon::now()->endOfWeek();
            
            $weeklyAttendances = \App\Models\Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
                ->get();
            
            $chartLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            $chartData   = [0, 0, 0, 0, 0, 0];
            
            foreach ($weeklyAttendances as $att) {
                $dayIndex = \Carbon\Carbon::parse($att->date)->dayOfWeekIso - 1;
                if ($dayIndex < 6 && $att->check_in && $att->check_out) {
                    $checkIn  = \Carbon\Carbon::parse($att->check_in);
                    $checkOut = \Carbon\Carbon::parse($att->check_out);
                    $hours = $checkIn->diffInMinutes($checkOut) / 60;
                    $chartData[$dayIndex] = round($hours, 1);
                }
            }

            // ── Celebrations (same logic as admin dashboard) ───────────────────────
            $startDate = now()->startOfDay();
            $endDate   = now()->addDays(30)->endOfDay();

            $upcomingBirthdays = \App\Models\Employee::with('departmentRel', 'designationRel')
                ->where('status', 'Active')
                ->get()
                ->filter(function ($emp) use ($startDate, $endDate) {
                    if (!$emp->dob) return false;
                    $bdayThisYear = \Carbon\Carbon::parse($emp->dob)->year(now()->year)->startOfDay();
                    $bdayNextYear = \Carbon\Carbon::parse($emp->dob)->year(now()->year + 1)->startOfDay();
                    return $bdayThisYear->between($startDate, $endDate) || $bdayNextYear->between($startDate, $endDate);
                })
                ->sortBy(function ($emp) {
                    $bday = \Carbon\Carbon::parse($emp->dob)->year(now()->year)->startOfDay();
                    if ($bday->isPast() && !$bday->isToday()) {
                        $bday->addYear();
                    }
                    return $bday->timestamp;
                })
                ->take(5)
                ->values();

            $workAnniversaries = \App\Models\Employee::with('departmentRel', 'designationRel')
                ->where('status', 'Active')
                ->get()
                ->filter(function ($emp) use ($startDate, $endDate) {
                    if (!$emp->joining_date) return false;
                    $annivThisYear = \Carbon\Carbon::parse($emp->joining_date)->year(now()->year)->startOfDay();
                    $annivNextYear = \Carbon\Carbon::parse($emp->joining_date)->year(now()->year + 1)->startOfDay();
                    if ($annivThisYear->between($startDate, $endDate)) {
                        $emp->anniversary_years = now()->year - \Carbon\Carbon::parse($emp->joining_date)->year;
                        return true;
                    }
                    if ($annivNextYear->between($startDate, $endDate)) {
                        $emp->anniversary_years = (now()->year + 1) - \Carbon\Carbon::parse($emp->joining_date)->year;
                        return true;
                    }
                    return false;
                })
                ->sortBy(function ($emp) {
                    $anniv = \Carbon\Carbon::parse($emp->joining_date)->year(now()->year)->startOfDay();
                    if ($anniv->isPast() && !$anniv->isToday()) {
                        $anniv->addYear();
                    }
                    return $anniv->timestamp;
                })
                ->take(5)
                ->values();

            $calendarEmployees = \App\Models\Employee::with('departmentRel', 'designationRel')
                ->where('status', 'Active')
                ->get();

            $companyHolidays = \App\Models\Holiday::orderBy('date')->get();

            $currentMonth = now()->month;
            $currentYear  = now()->year;

            $leavesMonth = \App\Models\LeaveRequest::where('employee_id', $employee->id)
                ->where('status', 'Approved')
                ->whereMonth('start_date', $currentMonth)
                ->whereYear('start_date', $currentYear)
                ->sum('total_days');

            $leavesYear = \App\Models\LeaveRequest::where('employee_id', $employee->id)
                ->where('status', 'Approved')
                ->whereYear('start_date', $currentYear)
                ->sum('total_days');

            $attendancesThisMonth = \App\Models\Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->get();

            $totalLateMinutes = 0;
            $totalEarlyMinutes = 0;

            $shiftDetails = $employee ? $employee->getShiftDetails() : [
                'start_carbon' => \Carbon\Carbon::parse('09:00'),
                'end_carbon' => \Carbon\Carbon::parse('18:00'),
            ];
            $lateGrace = (int)\App\Models\Setting::get('late_grace_period_minutes', 5);
            $earlyGrace = (int)\App\Models\Setting::get('early_checkout_grace_minutes', 15);

            foreach ($attendancesThisMonth as $att) {
                if ($att->check_in) {
                    $checkInTime = \Carbon\Carbon::parse($att->check_in);
                    $start = $checkInTime->copy()->setTimeFrom($shiftDetails['start_carbon']);
                    $lateThreshold = $start->copy()->addMinutes($lateGrace);
                    if ($checkInTime->gt($lateThreshold)) {
                        $totalLateMinutes += $lateThreshold->diffInMinutes($checkInTime);
                    }
                }
                
                if ($att->check_out) {
                    $checkOutTime = \Carbon\Carbon::parse($att->check_out);
                    $end = $checkOutTime->copy()->setTimeFrom($shiftDetails['end_carbon']);
                    $earlyThreshold = $end->copy()->subMinutes($earlyGrace);
                    if ($checkOutTime->lt($earlyThreshold)) {
                        $totalEarlyMinutes += $checkOutTime->diffInMinutes($earlyThreshold);
                    }
                }
            }

            $statsLateTime = floor($totalLateMinutes / 60) . 'h ' . ($totalLateMinutes % 60) . 'm';
            $statsEarlyTime = floor($totalEarlyMinutes / 60) . 'h ' . ($totalEarlyMinutes % 60) . 'm';

            return view('employee.portal.dashboard', compact(
                'employee', 'todayAttendance', 'chartLabels', 'chartData',
                'upcomingBirthdays', 'workAnniversaries',
                'calendarEmployees', 'companyHolidays',
                'leavesMonth', 'leavesYear', 'statsLateTime', 'statsEarlyTime',
                'activeAnnouncements'
            ));
        })->name('dashboard');

        Route::post('/attendance/punch-in', [\App\Http\Controllers\Employee\AttendanceController::class, 'punchIn'])->name('attendance.punch-in');
        Route::post('/attendance/punch-out', [\App\Http\Controllers\Employee\AttendanceController::class, 'punchOut'])->name('attendance.punch-out');
        Route::get('/scan', [\App\Http\Controllers\QrAttendanceController::class, 'showScanPage'])->name('qr.scan');
        Route::post('/attendance/punch-via-qr', [\App\Http\Controllers\QrAttendanceController::class, 'punchViaQr'])->name('attendance.punch-via-qr');

        Route::get('/attendance', function () {
            $employee = auth()->guard('employee')->user()->employee;

            // Sync approved leave requests into Attendance table
            $leaveService = app(\App\Services\LeaveAllocationService::class);
            $approvedLeaves = \App\Models\LeaveRequest::where('employee_id', $employee->id)->where('status', 'Approved')->get();
            foreach ($approvedLeaves as $leave) {
                $leaveService->syncLeaveRequestAttendance($leave);
            }

            // All attendance records (all time, for calendar)
            $allRecords = \App\Models\Attendance::where('employee_id', $employee->id)->get();
            $holidays = \App\Models\Holiday::all()->pluck('name', 'date')->toArray();

            // Collect all approved leave dates
            $approvedLeaveDates = $approvedLeaves->flatMap(function($leave) {
                $dates = [];
                $curr = \Carbon\Carbon::parse($leave->start_date);
                $end = \Carbon\Carbon::parse($leave->end_date);
                while ($curr->lte($end)) {
                    $dates[] = $curr->format('Y-m-d');
                    $curr->addDay();
                }
                return $dates;
            })->toArray();

            $startDate = $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date) : $employee->created_at;
            $endDate = now();

            $existingDates = $allRecords->pluck('date')->toArray();

            $dynamicAbsences = collect();

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                if ($date->isSunday()) continue;

                $dateString = $date->format('Y-m-d');
                if (isset($holidays[$dateString])) continue;

                if (!in_array($dateString, $existingDates)) {
                    $status = in_array($dateString, $approvedLeaveDates) ? 'Leave' : 'Absent';
                    $dynamicAbsences->push(new \App\Models\Attendance([
                        'employee_id' => $employee->id,
                        'date' => $dateString,
                        'status' => $status,
                        'check_in' => null,
                        'check_out' => null,
                    ]));
                }
            }

            $allRecords = $allRecords->concat($dynamicAbsences)->sortByDesc('date')->values();

            // This month's records for the table
            $records = $allRecords->filter(fn($a) =>
                \Carbon\Carbon::parse($a->date)->month === now()->month &&
                \Carbon\Carbon::parse($a->date)->year  === now()->year
            );

            // Monthly stats (current month)
            $stats = [
                'present' => $records->where('status', 'Present')->count(),
                'absent'  => $records->where('status', 'Absent')->count(),
                'halfday' => $records->where('status', 'Half-Day')->count(),
                'late'    => $records->where('status', 'Late')->count(),
                'leave'   => $records->where('status', 'Leave')->count(),
            ];

            // Build date-keyed map for JS calendar (all records)
            $calendarData = $allRecords->keyBy(fn($a) => $a->date)
                ->map(fn($a) => [
                    'status'    => $a->status,
                    'check_in'  => $a->check_in  ? \Carbon\Carbon::parse($a->check_in)->format('h:i A')  : null,
                    'check_out' => $a->check_out ? \Carbon\Carbon::parse($a->check_out)->format('h:i A') : null,
                ]);

            $todayAttendance = $allRecords->first(fn($a) =>
                \Carbon\Carbon::parse($a->date)->isToday()
            );

            return view('employee.portal.attendance', compact(
                'employee', 'records', 'allRecords', 'stats', 'calendarData', 'todayAttendance', 'holidays'
            ));
        })->name('attendance.index');


        // ── Leaves ────────────────────────────────────────────────────────────
        Route::get('/leaves', [\App\Http\Controllers\Employee\LeaveController::class, 'index'])->name('leaves.index');
        Route::post('/leaves/calculate', [\App\Http\Controllers\Employee\LeaveController::class, 'calculateAllocation'])->name('leaves.calculate');
        Route::post('/leaves', [\App\Http\Controllers\Employee\LeaveController::class, 'store'])->name('leaves.store');
        Route::post('/leaves/{leave}/certificate', [\App\Http\Controllers\Employee\LeaveController::class, 'uploadCertificate'])->name('leaves.certificate.upload');

        // Early Going
        Route::get('/early-going', [App\Http\Controllers\Employee\EarlyGoingController::class, 'index'])->name('early-going');
        Route::post('/early-going', [App\Http\Controllers\Employee\EarlyGoingController::class, 'store'])->name('early-going.store');

        // Daily Reports
        Route::get('/daily-reports', [App\Http\Controllers\Employee\DailyReportController::class, 'index'])->name('daily-reports.index');
        Route::post('/daily-reports', [App\Http\Controllers\Employee\DailyReportController::class, 'store'])->name('daily-reports.store');

        // ── Profile ───────────────────────────────────────────────────────────
        Route::get('/profile-view', [\App\Http\Controllers\Employee\ProfileController::class, 'show'])->name('profile.index');
        Route::get('/profile-edit', [\App\Http\Controllers\Employee\ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile-edit', [\App\Http\Controllers\Employee\ProfileController::class, 'update'])->name('profile.update');
        // ── Team Management (Teamleaders only) ────────────────────────────────
        Route::middleware(['is_teamleader'])->prefix('team')->name('team.')->group(function () {
            Route::get('/leaves', [\App\Http\Controllers\Employee\TeamController::class, 'leaves'])->name('leaves');
            Route::post('/leaves/{id}', [\App\Http\Controllers\Employee\TeamController::class, 'updateLeaveStatus'])->name('leaves.update');
            Route::get('/early-going', [\App\Http\Controllers\Employee\TeamController::class, 'earlyGoing'])->name('early-going');
            Route::post('/early-going/{id}', [\App\Http\Controllers\Employee\TeamController::class, 'updateEarlyGoingStatus'])->name('early-going.update');
            Route::get('/attendance', [\App\Http\Controllers\Employee\TeamController::class, 'attendance'])->name('attendance');
            Route::post('/attendance', [\App\Http\Controllers\Employee\TeamController::class, 'updateAttendance'])->name('attendance.update');
            
            // Team Daily Reports
            Route::get('/daily-reports', [\App\Http\Controllers\Employee\TeamDailyReportController::class, 'index'])->name('daily-reports.index');
        });
        
        // Notifications
        Route::post('/notifications/{id}/mark-as-read', function($id) {
            auth()->guard('employee')->user()->notifications()->where('id', $id)->first()?->markAsRead();
            return back();
        })->name('notifications.markAsRead');
        
        }); // End of employee_profile_completed middleware
    });
});
