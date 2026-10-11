<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $imageSizeRule = function ($attribute, $value, $fail) {
            if ($value instanceof \Illuminate\Http\UploadedFile && in_array(strtolower($value->extension()), ['jpeg', 'jpg', 'png'])) {
                $size = $value->getSize() / 1024;
                if ($size > 200) {
                    $fail("The $attribute image must not be greater than 200 kilobytes.");
                }
            }
        };

        $employeeId = null;
        if (auth()->guard('employee')->check()) {
            $employeeId = auth()->guard('employee')->user()->employee_id;
        }

        return [
            'name' => 'required|string|max:255',
            'employee_id' => ['required', 'string', $employeeId ? 'unique:employees,employee_id,'.$employeeId : 'unique:employees,employee_id'],
            'department_id' => 'required|exists:departments,id',
            'designation' => 'required|string|max:255',
            'branch_id' => 'required|exists:company_branches,id',
            'company_email' => 'nullable|email|' . ($employeeId ? 'unique:employees,company_email,'.$employeeId : 'unique:employees,company_email'),
            'personal_email' => 'required|email',
            'personal_phone' => 'required|string|' . ($employeeId ? 'unique:employees,personal_phone,'.$employeeId : 'unique:employees,personal_phone'),
            'dob' => 'required|date',
            'gender' => 'required|string',
            'blood_group' => 'required|string',
            'emergency_name' => 'required|string|max:255',
            'emergency_relation' => 'required|string',
            'emergency_phone' => 'required|string|different:personal_phone',
            'emergency_alt_phone' => 'nullable|string',
            'aadhaar' => 'required|string|' . ($employeeId ? 'unique:employees,aadhaar,'.$employeeId : 'unique:employees,aadhaar'),
            'pan' => 'nullable|string|' . ($employeeId ? 'unique:employees,pan,'.$employeeId : 'unique:employees,pan'),
            'passport' => 'nullable|string',
            'driving_license' => 'nullable|string',
            'photo' => [($employeeId && optional(auth()->guard('employee')->user()->employee)->photo) ? 'nullable' : 'required', 'file', 'mimes:jpeg,jpg,png', $imageSizeRule],
            'aadhaar_file' => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120', $imageSizeRule],
            'bank_name' => 'required|string|max:255',
            'account_holder' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'ifsc' => 'required|string|max:255',
            'branch' => 'required|string|max:255',
            
            'joining_date' => 'required|date',
            'employment_type' => 'required|string|max:255',
            'reporting_manager' => 'required|string|max:255',
            'work_location' => 'nullable|string|max:255',
            
            'present_address' => 'required|string',
            'permanent_address' => 'required|string',
            'marital_status' => 'required|string',
            'office_phone' => 'nullable|string',

            'education' => 'required|array|min:1',
            'education.*.qualification' => 'required|string|max:255',
            'education.*.course' => 'required|string|max:255',
            'education.*.institution' => 'required|string|max:255',
            'education.*.university' => 'required|string|max:255',
            'education.*.year' => 'required|string|max:255',
            'education.*.percentage' => 'required|string|max:255',
            'education.*.certificate' => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120', $imageSizeRule],
            
            'experience' => 'nullable|array',
            'experience.*.company' => 'required_with:experience|string|max:255',
            'experience.*.designation' => 'required_with:experience|string|max:255',
            'experience.*.from_date' => 'required_with:experience|date',
            'experience.*.to_date' => 'required_with:experience|date',
            'experience.*.years' => 'required_with:experience|string|max:255',
            'experience.*.certificate' => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120', $imageSizeRule],
            
            'certificates' => 'nullable|array',
            'certificates.*.certificate_name' => 'required_with:certificates|string|max:255',
            'certificates.*.issued_by' => 'required_with:certificates|string|max:255',
            'certificates.*.issued_date' => 'required_with:certificates|date',
            'certificates.*.expiry_date' => 'required_with:certificates|date',
            'certificates.*.file' => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120', $imageSizeRule],
            'provided_assets' => 'nullable|array',
            'provided_assets.*' => 'nullable|string',
            'agreed_to_terms' => 'required',
        ];
    }
}
