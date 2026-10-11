<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class EmployeeProfileController extends Controller
{
    public function edit()
    {
        $employee = auth()->guard('employee')->user()->employee;
        $employee->load('education', 'experience', 'certificates');
        return view('employee.portal.edit-profile', compact('employee'));
    }

    public function update(Request $request)
    {
        $employee = auth()->guard('employee')->user()->employee;

        // Image size rule from StoreEmployeeRequest
        $imageSizeRule = function ($attribute, $value, $fail) {
            if ($value instanceof \Illuminate\Http\UploadedFile && in_array(strtolower($value->extension()), ['jpeg', 'jpg', 'png'])) {
                $size = $value->getSize() / 1024;
                if ($size > 200) {
                    $fail("The $attribute image must not be greater than 200 kilobytes.");
                }
            }
        };

        $validatedData = $request->validate([
            // Personal Details
            'name' => 'required|string|max:255',
            'personal_email' => 'nullable|email',
            'personal_phone' => 'nullable|string',
            'dob' => 'nullable|date_format:d-m-Y',
            'gender' => 'nullable|string',
            'blood_group' => 'nullable|string',
            'marital_status' => 'nullable|string',
            'present_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'emergency_name' => 'nullable|string|max:255',
            'emergency_relation' => 'nullable|string',
            'emergency_phone' => 'nullable|string',
            'emergency_alt_phone' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

            // Bank Details
            'bank_name' => 'nullable|string|max:255',
            'account_holder' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'ifsc' => 'nullable|string|max:255',
            'branch' => 'nullable|string|max:255',

            // Identity Details
            'aadhaar' => 'nullable|string|unique:employees,aadhaar,'.$employee->id,
            'pan' => 'nullable|string|unique:employees,pan,'.$employee->id,
            'passport' => 'nullable|string',
            'driving_license' => 'nullable|string',
            'aadhaar_file' => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120', $imageSizeRule],
            'pan_file' => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120', $imageSizeRule],
            'passport_file' => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120', $imageSizeRule],
            'license_file' => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120', $imageSizeRule],

            // Dynamic Rows
            'education' => 'nullable|array',
            'education.*.id' => 'nullable|exists:education_details,id',
            'education.*.qualification' => 'nullable|string|max:255',
            'education.*.course' => 'nullable|string|max:255',
            'education.*.institution' => 'nullable|string|max:255',
            'education.*.university' => 'nullable|string|max:255',
            'education.*.year' => 'nullable|string|max:255',
            'education.*.percentage' => 'nullable|string|max:255',
            'education.*.certificate' => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120', $imageSizeRule],

            'experience' => 'nullable|array',
            'experience.*.id' => 'nullable|exists:experience_details,id',
            'experience.*.company' => 'nullable|string|max:255',
            'experience.*.designation' => 'nullable|string|max:255',
            'experience.*.from_date' => 'nullable|date_format:d-m-Y',
            'experience.*.to_date' => 'nullable|date_format:d-m-Y',
            'experience.*.years' => 'nullable|string|max:255',
            'experience.*.certificate' => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120', $imageSizeRule],

            'certificates' => 'nullable|array',
            'certificates.*.id' => 'nullable|exists:employee_certificates,id',
            'certificates.*.certificate_name' => 'nullable|string|max:255',
            'certificates.*.issued_by' => 'nullable|string|max:255',
            'certificates.*.issued_date' => 'nullable|date_format:d-m-Y',
            'certificates.*.expiry_date' => 'nullable|date_format:d-m-Y',
            'certificates.*.file' => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120', $imageSizeRule],

            // Company Assets
            'assets' => 'nullable|array',
            'assets.*.assignment_id' => 'nullable|exists:asset_assignments,id',
            'assets.*.asset_id' => 'nullable|exists:assets,id',
            'assets.*.name' => 'nullable|string|max:255',
            'assets.*.type' => 'nullable|string|max:255',
            'assets.*.serial_number' => 'nullable|string|max:255',
        ]);

        $employeeData = \Illuminate\Support\Arr::except($validatedData, ['education', 'experience', 'certificates', 'assets', 'photo', 'aadhaar_file', 'pan_file', 'passport_file', 'license_file']);

        // Convert dob to Y-m-d
        if (!empty($employeeData['dob'])) {
            $employeeData['dob'] = \Carbon\Carbon::createFromFormat('d-m-Y', $employeeData['dob'])->format('Y-m-d');
        }

        // Handle Photo
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $manager = new ImageManager(new Driver());
            $image = $manager->decode($photo->getRealPath());
            $image->cover(413, 531);
            
            $filename = time() . '_' . $photo->getClientOriginalName();
            $path = 'employees/photos/' . $filename;
            
            Storage::disk('public')->put($path, (string) $image->encodeUsingFileExtension('jpeg'));
            $employeeData['photo'] = $path;
        }

        // Handle Identity Files
        $fileFields = [
            'aadhaar_file' => 'employees/aadhaar',
            'pan_file' => 'employees/pan',
            'passport_file' => 'employees/passport',
            'license_file' => 'employees/license'
        ];

        foreach ($fileFields as $field => $directory) {
            if ($request->hasFile($field)) {
                $employeeData[$field] = $request->file($field)->store($directory, 'public');
            }
        }

        $employee->update($employeeData);

        // Sync Education
        $submittedEduIds = [];
        if (isset($validatedData['education'])) {
            foreach ($validatedData['education'] as $edu) {
                $certificatePath = null;
                if (isset($edu['certificate']) && $edu['certificate'] instanceof \Illuminate\Http\UploadedFile) {
                    $certificatePath = $edu['certificate']->store('employees/education', 'public');
                    unset($edu['certificate']);
                }

                if (isset($edu['id']) && $edu['id']) {
                    $record = $employee->education()->find($edu['id']);
                    if ($record) {
                        $updateData = $edu;
                        if ($certificatePath) {
                            $updateData['certificate'] = $certificatePath;
                        }
                        $record->update($updateData);
                        $submittedEduIds[] = $record->id;
                    }
                } else {
                    $record = $employee->education()->create($edu);
                    if ($certificatePath) {
                        $record->update(['certificate' => $certificatePath]);
                    }
                    $submittedEduIds[] = $record->id;
                }
            }
        }
        $employee->education()->whereNotIn('id', $submittedEduIds)->delete();

        // Sync Experience
        $submittedExpIds = [];
        if (isset($validatedData['experience'])) {
            foreach ($validatedData['experience'] as $exp) {
                if (!empty($exp['from_date'])) {
                    $exp['from_date'] = \Carbon\Carbon::createFromFormat('d-m-Y', $exp['from_date'])->format('Y-m-d');
                }
                if (!empty($exp['to_date'])) {
                    $exp['to_date'] = \Carbon\Carbon::createFromFormat('d-m-Y', $exp['to_date'])->format('Y-m-d');
                }

                $certificatePath = null;
                if (isset($exp['certificate']) && $exp['certificate'] instanceof \Illuminate\Http\UploadedFile) {
                    $certificatePath = $exp['certificate']->store('employees/experience', 'public');
                    unset($exp['certificate']);
                }

                if (isset($exp['id']) && $exp['id']) {
                    $record = $employee->experience()->find($exp['id']);
                    if ($record) {
                        $updateData = $exp;
                        if ($certificatePath) {
                            $updateData['certificate'] = $certificatePath;
                        }
                        $record->update($updateData);
                        $submittedExpIds[] = $record->id;
                    }
                } else {
                    $record = $employee->experience()->create($exp);
                    if ($certificatePath) {
                        $record->update(['certificate' => $certificatePath]);
                    }
                    $submittedExpIds[] = $record->id;
                }
            }
        }
        $employee->experience()->whereNotIn('id', $submittedExpIds)->delete();

        // Sync Certificates
        $submittedCertIds = [];
        if (isset($validatedData['certificates'])) {
            foreach ($validatedData['certificates'] as $cert) {
                if (!empty($cert['issued_date'])) {
                    $cert['issued_date'] = \Carbon\Carbon::createFromFormat('d-m-Y', $cert['issued_date'])->format('Y-m-d');
                }
                if (!empty($cert['expiry_date'])) {
                    $cert['expiry_date'] = \Carbon\Carbon::createFromFormat('d-m-Y', $cert['expiry_date'])->format('Y-m-d');
                }

                $filePath = null;
                if (isset($cert['file']) && $cert['file'] instanceof \Illuminate\Http\UploadedFile) {
                    $filePath = $cert['file']->store('employees/certificates', 'public');
                    unset($cert['file']);
                }

                if (isset($cert['id']) && $cert['id']) {
                    $record = $employee->certificates()->find($cert['id']);
                    if ($record) {
                        $updateData = $cert;
                        if ($filePath) {
                            $updateData['file'] = $filePath;
                        }
                        $record->update($updateData);
                        $submittedCertIds[] = $record->id;
                    }
                } else {
                    $record = $employee->certificates()->create($cert);
                    if ($filePath) {
                        $record->update(['file' => $filePath]);
                    }
                    $submittedCertIds[] = $record->id;
                }
            }
        }
        $employee->certificates()->whereNotIn('id', $submittedCertIds)->delete();

        // Sync Assets
        $submittedAssetAssignmentIds = [];
        if (isset($validatedData['assets'])) {
            foreach ($validatedData['assets'] as $assetData) {
                if (empty($assetData['name']) && empty($assetData['type']) && empty($assetData['serial_number'])) {
                    continue; // Skip empty rows
                }
                
                if (!empty($assetData['assignment_id']) && !empty($assetData['asset_id'])) {
                    $assignment = $employee->assetAssignments()->find($assetData['assignment_id']);
                    if ($assignment && $assignment->asset) {
                        $assignment->asset->update([
                            'name' => $assetData['name'],
                            'type' => $assetData['type'],
                            'serial_number' => $assetData['serial_number'] ?? uniqid('SN-'),
                        ]);
                        $submittedAssetAssignmentIds[] = $assignment->id;
                    }
                } else {
                    $asset = \App\Models\Asset::create([
                        'name' => $assetData['name'],
                        'type' => $assetData['type'],
                        'serial_number' => $assetData['serial_number'] ?? uniqid('SN-'),
                        'status' => 'Assigned',
                    ]);
                    
                    $assignment = $employee->assetAssignments()->create([
                        'asset_id' => $asset->id,
                        'assigned_date' => \Carbon\Carbon::now()->format('Y-m-d'),
                    ]);
                    
                    $submittedAssetAssignmentIds[] = $assignment->id;
                }
            }
        }
        
        $removedAssignments = $employee->assetAssignments()->whereNotIn('id', $submittedAssetAssignmentIds)->get();
        foreach($removedAssignments as $ra) {
            if($ra->asset) {
                $ra->asset->update(['status' => 'Available']);
            }
            $ra->delete();
        }

        return redirect()->route('employee.profile.index')->with('success', 'Profile updated successfully.');
    }
}
