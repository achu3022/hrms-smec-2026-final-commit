<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function create()
    {
        return redirect()->route('admin.employees.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'employee_id' => 'required|string|max:255|unique:employees',
            'department_id' => 'required|exists:departments,id',
            'shift' => 'required|string',
            'custom_shift_start' => 'nullable|string',
            'custom_shift_end' => 'nullable|string',
            'personal_phone' => 'required|string|max:20|unique:employees,personal_phone',
            'dob' => 'required|date',
        ]);

        if ($data['shift'] === 'custom' && $request->filled('custom_shift_start') && $request->filled('custom_shift_end')) {
            $start = \Carbon\Carbon::parse($request->custom_shift_start)->format('g:i A');
            $end = \Carbon\Carbon::parse($request->custom_shift_end)->format('g:i A');
            $data['shift'] = "{$start} - {$end}";
        }

        unset($data['custom_shift_start'], $data['custom_shift_end']);

        $data['is_profile_completed'] = false;
        
        // EmployeeObserver will auto-generate EmployeeLogin
        Employee::create($data);

        return back()->with('success', 'Temporary employee login created successfully.');
    }

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
        if (request()->ajax() || request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('admin.employees.show_modal', compact('employee'));
        }
        return view('admin.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(\App\Http\Requests\UpdateEmployeeRequest $request, Employee $employee)
    {
        $data = $request->validated();
        
        if (($request->input('shift') === 'custom' || (isset($data['shift']) && $data['shift'] === 'custom')) && $request->filled('custom_shift_start') && $request->filled('custom_shift_end')) {
            $start = \Carbon\Carbon::parse($request->custom_shift_start)->format('g:i A');
            $end = \Carbon\Carbon::parse($request->custom_shift_end)->format('g:i A');
            $data['shift'] = "{$start} - {$end}";
        }

        if (isset($data['employee_id'])) {
            $data['employee_id'] = strtoupper($data['employee_id']);
        }

        if (isset($data['branch_id']) && $data['branch_id']) {
            $branch = \App\Models\CompanyBranch::find($data['branch_id']);
            if ($branch) {
                $data['work_location'] = $branch->name;
            }
        }

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->decode($photo->getRealPath());
            $image->cover(413, 531);
            
            $filename = time() . '_' . $photo->getClientOriginalName();
            $path = 'employees/photos/' . $filename;
            
            Storage::disk('public')->put($path, (string) $image->encodeUsingFileExtension('jpeg'));
            
            if ($employee->photo) {
                Storage::disk('public')->delete($employee->photo);
            }
            $data['photo'] = $path;
        }

        $fileFields = [
            'aadhaar_file' => 'employees/aadhaar'
        ];

        foreach ($fileFields as $field => $directory) {
            if ($request->hasFile($field)) {
                if ($employee->$field) {
                    Storage::disk('public')->delete($employee->$field);
                }
                $data[$field] = $request->file($field)->store($directory, 'public');
            }
        }

        $employeeData = \Illuminate\Support\Arr::except($data, ['education', 'experience', 'certificates']);
        
        $employee->update($employeeData);

        // Education
        $existingEducationIds = [];
        if (isset($data['education'])) {
            foreach ($data['education'] as $edu) {
                if (isset($edu['certificate']) && $edu['certificate'] instanceof \Illuminate\Http\UploadedFile) {
                    $edu['certificate'] = $edu['certificate']->store('employees/education', 'public');
                } else {
                    unset($edu['certificate']);
                }
                
                if (isset($edu['id'])) {
                    $record = $employee->education()->find($edu['id']);
                    if ($record) {
                        $record->update($edu);
                        $existingEducationIds[] = $record->id;
                    }
                } else {
                    $record = $employee->education()->create($edu);
                    $existingEducationIds[] = $record->id;
                }
            }
        }
        $employee->education()->whereNotIn('id', $existingEducationIds)->delete();

        // Experience
        $existingExperienceIds = [];
        if (isset($data['experience'])) {
            foreach ($data['experience'] as $exp) {
                if (isset($exp['certificate']) && $exp['certificate'] instanceof \Illuminate\Http\UploadedFile) {
                    $exp['certificate'] = $exp['certificate']->store('employees/experience', 'public');
                } else {
                    unset($exp['certificate']);
                }
                
                if (isset($exp['id'])) {
                    $record = $employee->experience()->find($exp['id']);
                    if ($record) {
                        $record->update($exp);
                        $existingExperienceIds[] = $record->id;
                    }
                } else {
                    $record = $employee->experience()->create($exp);
                    $existingExperienceIds[] = $record->id;
                }
            }
        }
        $employee->experience()->whereNotIn('id', $existingExperienceIds)->delete();

        // Certificates
        $existingCertificateIds = [];
        if (isset($data['certificates'])) {
            foreach ($data['certificates'] as $cert) {
                if (isset($cert['file']) && $cert['file'] instanceof \Illuminate\Http\UploadedFile) {
                    $cert['file'] = $cert['file']->store('employees/certificates', 'public');
                } else {
                    unset($cert['file']);
                }
                
                if (isset($cert['id'])) {
                    $record = $employee->certificates()->find($cert['id']);
                    if ($record) {
                        $record->update($cert);
                        $existingCertificateIds[] = $record->id;
                    }
                } else {
                    $record = $employee->certificates()->create($cert);
                    $existingCertificateIds[] = $record->id;
                }
            }
        }
        $employee->certificates()->whereNotIn('id', $existingCertificateIds)->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Employee updated successfully.']);
        }
        
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
