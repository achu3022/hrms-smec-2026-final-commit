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
        $departments = \App\Models\Department::count();
        $recentEntries = Employee::with('departmentRel', 'designationRel')->latest()->take(5)->get();

        return view('admin.dashboard', compact('totalEmployees', 'activeEmployees', 'inactiveEmployees', 'departments', 'recentEntries'));
    }
}
