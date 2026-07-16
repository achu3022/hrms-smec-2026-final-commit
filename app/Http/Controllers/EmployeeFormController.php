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
        
        return view('employee.form', compact('departments', 'designations', 'branches'));
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

        $employeeData = \Illuminate\Support\Arr::except($data, ['education', 'experience', 'certificates']);
        $employee = Employee::create($employeeData);

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

        try {
            \Illuminate\Support\Facades\Mail::to('hr@company.com')->send(new \App\Mail\NewEmployeeOnboarding($employee));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send onboarding email: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'Your employee information has been submitted successfully.']);
    }
}
