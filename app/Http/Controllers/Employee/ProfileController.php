<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $employee = auth()->guard('employee')->user()->employee;
        $employee->load('departmentRel', 'designationRel', 'education', 'experience', 'certificates');
        return view('employee.portal.profile', compact('employee'));
    }

    public function edit()
    {
        $employee = auth()->guard('employee')->user()->employee;
        $employee->load('departmentRel', 'designationRel', 'education', 'experience', 'certificates');
        return view('employee.portal.edit_profile', compact('employee'));
    }

    public function update(Request $request)
    {
        $employee = auth()->guard('employee')->user()->employee;

        $data = $request->validate([
            'personal_email' => 'required|email|max:255',
            'personal_phone' => 'required|string|max:20',
            'office_phone' => 'nullable|string|max:20',
            'gender' => 'required|string|max:20',
            'blood_group' => 'required|string|max:10',
            'marital_status' => 'required|string|max:20',
            'present_address' => 'required|string',
            'permanent_address' => 'required|string',
            'emergency_name' => 'required|string|max:255',
            'emergency_relation' => 'required|string|max:255',
            'emergency_phone' => 'required|string|max:20',
            'emergency_alt_phone' => 'nullable|string|max:20',
            'aadhaar' => 'required|string|max:50',
            'pan' => 'nullable|string|max:50',
            'passport' => 'nullable|string|max:50',
            'driving_license' => 'nullable|string|max:50',
            'bank_name' => 'required|string|max:255',
            'account_holder' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'ifsc' => 'required|string|max:20',
            'branch' => 'required|string|max:255',
            'photo' => $employee->photo ? 'nullable|image|max:5120' : 'required|image|max:5120',
            'aadhaar_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'pan_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'passport_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'license_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'bank_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'education' => 'required|array|min:1',
            'experience' => 'nullable|array',
            'certificates' => 'nullable|array',
        ]);

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $manager = new ImageManager(new Driver());
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
            'aadhaar_file' => 'employees/aadhaar',
            'pan_file' => 'employees/pan',
            'passport_file' => 'employees/passport',
            'license_file' => 'employees/license',
            'bank_document' => 'employees/bank',
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

        // Update Education
        $existingEduIds = [];
        if (isset($data['education'])) {
            foreach ($data['education'] as $edu) {
                if (empty($edu['qualification'])) continue;

                if (isset($edu['certificate']) && $edu['certificate'] instanceof \Illuminate\Http\UploadedFile) {
                    $edu['certificate'] = $edu['certificate']->store('employees/education', 'public');
                } else {
                    unset($edu['certificate']);
                }

                if (isset($edu['id']) && $record = $employee->education()->find($edu['id'])) {
                    $record->update($edu);
                    $existingEduIds[] = $record->id;
                } else {
                    $record = $employee->education()->create($edu);
                    $existingEduIds[] = $record->id;
                }
            }
        }
        $employee->education()->whereNotIn('id', $existingEduIds)->delete();

        // Update Experience
        $existingExpIds = [];
        if (isset($data['experience'])) {
            foreach ($data['experience'] as $exp) {
                if (empty($exp['company'])) continue;

                if (isset($exp['certificate']) && $exp['certificate'] instanceof \Illuminate\Http\UploadedFile) {
                    $exp['certificate'] = $exp['certificate']->store('employees/experience', 'public');
                } else {
                    unset($exp['certificate']);
                }

                if (isset($exp['id']) && $record = $employee->experience()->find($exp['id'])) {
                    $record->update($exp);
                    $existingExpIds[] = $record->id;
                } else {
                    $record = $employee->experience()->create($exp);
                    $existingExpIds[] = $record->id;
                }
            }
        }
        $employee->experience()->whereNotIn('id', $existingExpIds)->delete();

        // Update Certificates
        $existingCertIds = [];
        if (isset($data['certificates'])) {
            foreach ($data['certificates'] as $cert) {
                if (empty($cert['certificate_name'])) continue;

                if (isset($cert['file']) && $cert['file'] instanceof \Illuminate\Http\UploadedFile) {
                    $cert['file'] = $cert['file']->store('employees/certificates', 'public');
                } else {
                    unset($cert['file']);
                }

                if (isset($cert['id']) && $record = $employee->certificates()->find($cert['id'])) {
                    $record->update($cert);
                    $existingCertIds[] = $record->id;
                } else {
                    $record = $employee->certificates()->create($cert);
                    $existingCertIds[] = $record->id;
                }
            }
        }
        $employee->certificates()->whereNotIn('id', $existingCertIds)->delete();

        return redirect()->route('employee.profile.index')->with('success', 'Profile updated successfully.');
    }
}
