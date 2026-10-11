<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;

class TeamLeaderController extends Controller
{
    /**
     * Display a listing of team leaders and the assignment form.
     */
    public function index()
    {
        // Fetch all active employees to populate the assignment dropdown
        $employees = Employee::where('status', 'Active')->orderBy('name')->get();
        
        // Fetch all departments
        $departments = Department::orderBy('name')->get();
        
        // Fetch current team leaders
        $teamLeaders = Employee::where('is_teamleader', true)
                               ->with('departmentRel')
                               ->orderBy('name')
                               ->get();

        return view('admin.team-leaders.index', compact('employees', 'departments', 'teamLeaders'));
    }

    /**
     * Assign a team leader role to an employee.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'department_id' => 'required|exists:departments,id',
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $department = Department::findOrFail($request->department_id);

        $employee->is_teamleader = true;
        $employee->department_id = $department->id;
        $employee->department = $department->name;
        $employee->save();

        return redirect()->route('admin.team-leaders.index')
                         ->with('success', 'Employee ' . $employee->name . ' has been assigned as a Team Leader for ' . $department->name . '.');
    }

    /**
     * Remove team leader role from an employee.
     */
    public function destroy(Employee $team_leader)
    {
        $name = $team_leader->name;
        $team_leader->is_teamleader = false;
        $team_leader->save();

        return redirect()->route('admin.team-leaders.index')
                         ->with('success', 'Team Leader status removed from ' . $name . '.');
    }
}
