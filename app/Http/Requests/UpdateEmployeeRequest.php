<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Admin is already authorized via middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $employeeParam = $this->route('employee');
        $employeeId = $employeeParam instanceof \App\Models\Employee ? $employeeParam->id : $employeeParam;

        $imageSizeRule = function ($attribute, $value, $fail) {
            if ($value instanceof \Illuminate\Http\UploadedFile && in_array(strtolower($value->extension()), ['jpeg', 'jpg', 'png'])) {
                $size = $value->getSize() / 1024;
                if ($size > 200) {
                    $fail("The $attribute image must not be greater than 200 kilobytes.");
                }
            }
        };

        return [
            'name' => 'nullable|string|max:255',
            'employee_id' => [
                'nullable',
                'string',
                Rule::unique('employees', 'employee_id')->ignore($employeeId),
            ],
            'department_id' => 'nullable|exists:departments,id',
            'shift' => 'nullable|string',
            'designation' => 'nullable|string|max:255',
            'branch_id' => 'nullable|exists:company_branches,id',
            'company_email' => [
                'nullable',
                'email',
                Rule::unique('employees', 'company_email')->ignore($employeeId),
            ],
            'personal_email' => 'nullable|email',
            'personal_phone' => [
                'nullable',
                'string',
                Rule::unique('employees', 'personal_phone')->ignore($employeeId),
            ],
            'dob' => 'nullable|date',
            'gender' => 'nullable|string',
            'blood_group' => 'nullable|string',
            'emergency_name' => 'nullable|string|max:255',
            'emergency_relation' => 'nullable|string',
            'emergency_phone' => 'nullable|string',
            'emergency_alt_phone' => 'nullable|string',
            'aadhaar' => [
                'nullable',
                'string',
                Rule::unique('employees', 'aadhaar')->ignore($employeeId),
            ],
            'pan' => [
                'nullable',
                'string',
                Rule::unique('employees', 'pan')->ignore($employeeId),
            ],
            'passport' => 'nullable|string',
            'driving_license' => 'nullable|string',
            'photo' => ['nullable', 'file', 'mimes:jpeg,jpg,png', $imageSizeRule],
            'aadhaar_file' => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120', $imageSizeRule],
            'bank_name' => 'nullable|string|max:255',
            'account_holder' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'ifsc' => 'nullable|string|max:255',
            'branch' => 'nullable|string|max:255',
            
            'joining_date' => 'nullable|date',
            'employment_type' => 'nullable|string|max:255',
            'reporting_manager' => 'nullable|string|max:255',
            'work_location' => 'nullable|string|max:255',
            
            'present_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'marital_status' => 'nullable|string',
            'office_phone' => 'nullable|string',

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
            'experience.*.from_date' => 'nullable|date',
            'experience.*.to_date' => 'nullable|date',
            'experience.*.years' => 'nullable|string|max:255',
            'experience.*.certificate' => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120', $imageSizeRule],
            
            'certificates' => 'nullable|array',
            'certificates.*.id' => 'nullable|exists:employee_certificates,id',
            'certificates.*.certificate_name' => 'nullable|string|max:255',
            'certificates.*.issued_by' => 'nullable|string|max:255',
            'certificates.*.issued_date' => 'nullable|date',
            'certificates.*.expiry_date' => 'nullable|date',
            'certificates.*.file' => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120', $imageSizeRule],
            'provided_assets' => 'nullable|array',
            'provided_assets.*' => 'nullable|string',
            'status' => 'nullable|string',
            'is_teamleader' => 'nullable|boolean',
        ];
    }
}
