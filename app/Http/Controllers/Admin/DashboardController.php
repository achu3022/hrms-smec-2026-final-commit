<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', 'Active')->count();
        $inactiveEmployees = Employee::where('status', 'Inactive')->count();
        $terminatedEmployees = Employee::where('status', 'Terminated')->count();
        $resignedEmployees = Employee::where('status', 'Resigned')->count();
        $departments = \App\Models\Department::count();
        $recentEntries = Employee::with('departmentRel', 'designationRel')->latest()->take(5)->get();

        // Set up rolling window range for upcoming celebrations (next 30 days)
        $startDate = now()->startOfDay();
        $endDate = now()->addDays(30)->endOfDay();

        // Filter active employee birthdays falling in the upcoming window (max 5)
        $upcomingBirthdays = Employee::with('departmentRel', 'designationRel')
            ->where('status', 'Active')
            ->get()
            ->filter(function ($emp) use ($startDate, $endDate) {
                if (!$emp->dob) return false;
                $bdayThisYear = \Carbon\Carbon::parse($emp->dob)->year(now()->year)->startOfDay();
                $bdayNextYear = \Carbon\Carbon::parse($emp->dob)->year(now()->year + 1)->startOfDay();
                return $bdayThisYear->between($startDate, $endDate) || $bdayNextYear->between($startDate, $endDate);
            })->sortBy(function ($emp) {
                $bday = \Carbon\Carbon::parse($emp->dob)->year(now()->year)->startOfDay();
                if ($bday->isPast() && !$bday->isToday()) {
                    $bday->addYear();
                }
                return $bday->timestamp;
            })->take(5)->values();

        // Filter employee work anniversaries falling in upcoming window (max 5)
        $workAnniversaries = Employee::with('departmentRel', 'designationRel')
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
            })->sortBy(function ($emp) {
                $anniv = \Carbon\Carbon::parse($emp->joining_date)->year(now()->year)->startOfDay();
                if ($anniv->isPast() && !$anniv->isToday()) {
                    $anniv->addYear();
                }
                return $anniv->timestamp;
            })->take(5)->values();

        // Fetch all active employees for the interactive calendar
        $calendarEmployees = Employee::with('departmentRel')
            ->where('status', 'Active')
            ->get()
            ->map(function ($emp) {
                return [
                    'name' => $emp->name,
                    'dob' => $emp->dob,
                    'joining_date' => $emp->joining_date,
                    'department' => $emp->departmentRel->name ?? $emp->department ?? 'General',
                ];
            });

        // Define company holidays
        $companyHolidays = \App\Models\Holiday::select('date', 'name')->get()->toArray();

        $bloodGroups = ['O+', 'A+', 'B+', 'AB+', 'O-', 'A-', 'B-', 'AB-'];

        return view('admin.dashboard', compact('totalEmployees', 'activeEmployees', 'inactiveEmployees', 'terminatedEmployees', 'resignedEmployees', 'departments', 'recentEntries', 'upcomingBirthdays', 'workAnniversaries', 'bloodGroups', 'calendarEmployees', 'companyHolidays'));
    }

    public function searchBloodGroup(Request $request)
    {
        $bloodGroup = $request->get('blood_group');
        $employees = Employee::with('departmentRel')
            ->where('blood_group', $bloodGroup)
            ->get()
            ->map(function ($emp) {
                return [
                    'name' => $emp->name,
                    'status' => $emp->status,
                    'department' => $emp->departmentRel->name ?? $emp->department ?? 'N/A',
                    'blood_group' => $emp->blood_group,
                    'contact' => $emp->personal_phone ?? $emp->office_phone ?? 'N/A',
                    'initials' => collect(explode(' ', $emp->name))->map(fn($n) => mb_substr($n, 0, 1))->take(2)->join(''),
                ];
            });

        return response()->json($employees);
    }
}
