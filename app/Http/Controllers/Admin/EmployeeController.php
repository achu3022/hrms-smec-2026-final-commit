<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('departmentRel');

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('id', 'desc')->paginate(20)->appends($request->all());
        $departments = \App\Models\Department::all();

        return view('admin.employees.index', compact('employees', 'departments'));
    }

    public function show(Employee $employee)
    {
        $employee->load('education', 'experience', 'certificates', 'departmentRel', 'designationRel', 'assetAssignments.asset');
        if (request()->ajax()) {
            return view('admin.employees.show_modal', compact('employee'));
        }
        return view('admin.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $employee->update($request->all());
        return redirect()->route('admin.employees.index')->with('success', 'Employee updated successfully.');
    }

    public function updateStatus(Request $request, Employee $employee)
    {
        $request->validate(['status' => 'required|string']);
        $employee->update(['status' => $request->status]);
        return response()->json(['success' => true]);
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('admin.employees.index')->with('success', 'Employee deleted successfully.');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:5120']);
        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\EmployeeImport, $request->file('file'));
        return back()->with('success', 'Employees imported successfully.');
    }

    public function idCard(Employee $employee)
    {
        $employee->load('departmentRel', 'designationRel', 'branchRel');
        $companyName = 'SMEC Labs';
        return view('admin.employees.id-card', compact('employee', 'companyName'));
    }

    public function experienceCertificate(Employee $employee)
    {
        $employee->load('departmentRel', 'designationRel', 'branchRel');
        $companyName = 'SMEC Labs';
        $branch = $employee->branchRel;
        return view('admin.employees.experience-certificate', compact('employee', 'companyName', 'branch'));
    }
}
