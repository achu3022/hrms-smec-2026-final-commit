<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Http\Requests\StoreEmployeeRequest;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;

class EmployeeFormController extends Controller
{
    public function create()
    {
        $departments = \App\Models\Department::where('is_active', true)->get();
        $designations = \App\Models\Designation::where('is_active', true)->get();
        $branches = \App\Models\CompanyBranch::where('is_active', true)->get();
        
        $employee = null;
        if (auth()->guard('employee')->check()) {
            $employee = auth()->guard('employee')->user()->employee;
            if ($employee && $employee->is_profile_completed) {
                return redirect()->route('employee.dashboard');
            }
        }
        
        return view('employee.form', compact('departments', 'designations', 'branches', 'employee'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->validated();
        
        $designation = \App\Models\Designation::firstOrCreate(
            ['name' => $data['designation']],
            ['is_active' => true]
        );
        $data['designation_id'] = $designation->id;
        
        // Set work_location from branch if branch_id is provided
        if (isset($data['branch_id']) && $data['branch_id']) {
            $branch = \App\Models\CompanyBranch::find($data['branch_id']);
            if ($branch) {
                $data['work_location'] = $branch->name;
            }
        }

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $manager = new ImageManager(new Driver());
            $image = $manager->decode($photo->getRealPath());
            $image->cover(413, 531);
            
            $filename = time() . '_' . $photo->getClientOriginalName();
            $path = 'employees/photos/' . $filename;
            
            Storage::disk('public')->put($path, (string) $image->encodeUsingFileExtension('jpeg'));
            $data['photo'] = $path;
        }

        $fileFields = [
            'aadhaar_file' => 'employees/aadhaar'
        ];

        foreach ($fileFields as $field => $directory) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store($directory, 'public');
            }
        }

        $employeeData = \Illuminate\Support\Arr::except($data, ['education', 'experience', 'certificates', 'employee_id']);
        
        $employee = auth()->guard('employee')->user()->employee;
        $employee->update($employeeData);

        if (isset($data['education'])) {
            foreach ($data['education'] as $edu) {
                $certificatePath = null;
                if (isset($edu['certificate']) && $edu['certificate'] instanceof \Illuminate\Http\UploadedFile) {
                    $certificatePath = $edu['certificate']->store('employees/education', 'public');
                    unset($edu['certificate']);
                }
                $educationRecord = $employee->education()->create($edu);
                if ($certificatePath) {
                    $educationRecord->update(['certificate' => $certificatePath]);
                }
            }
        }

        if (isset($data['experience'])) {
            foreach ($data['experience'] as $exp) {
                $certificatePath = null;
                if (isset($exp['certificate']) && $exp['certificate'] instanceof \Illuminate\Http\UploadedFile) {
                    $certificatePath = $exp['certificate']->store('employees/experience', 'public');
                    unset($exp['certificate']);
                }
                $experienceRecord = $employee->experience()->create($exp);
                if ($certificatePath) {
                    $experienceRecord->update(['certificate' => $certificatePath]);
                }
            }
        }

        if (isset($data['certificates'])) {
            foreach ($data['certificates'] as $cert) {
                $filePath = null;
                if (isset($cert['file']) && $cert['file'] instanceof \Illuminate\Http\UploadedFile) {
                    $filePath = $cert['file']->store('employees/certificates', 'public');
                    unset($cert['file']);
                }
                $certificateRecord = $employee->certificates()->create($cert);
                if ($filePath) {
                    $certificateRecord->update(['file' => $filePath]);
                }
            }
        }

        $employee->update(['is_profile_completed' => true]);

        // Calculate and initialize leave balances based on probation status
        $isProbationOver = false;
        if ($employee->joining_date) {
            $isProbationOver = \Carbon\Carbon::parse($employee->joining_date)->copy()->addMonths(6)->isPast();
        }

        $leaveTypes = \App\Models\LeaveType::where('is_active', true)->get();
        foreach ($leaveTypes as $type) {
            $initialBalance = ($isProbationOver && in_array($type->name, ['Casual', 'Medical'])) ? 1 : 0;
            \App\Models\LeaveBalance::firstOrCreate(
                ['employee_id' => $employee->id, 'leave_type_id' => $type->id],
                ['used' => 0, 'remaining' => 0]
            )->update(['remaining' => $initialBalance]);
        }

        try {
            \Illuminate\Support\Facades\Mail::to('hr@company.com')->send(new \App\Mail\NewEmployeeOnboarding($employee));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send onboarding email: ' . $e->getMessage());
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Your profile has been completed successfully.', 'redirect' => route('employee.dashboard')]);
        }
        
        return redirect()->route('employee.dashboard')->with('success', 'Your profile has been completed successfully.');
    }
}
