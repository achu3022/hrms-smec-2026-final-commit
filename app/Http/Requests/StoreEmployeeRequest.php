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
        return [
            'name' => 'required|string|max:255',
            'employee_id' => 'required|string|unique:employees,employee_id',
            'department_id' => 'required|exists:departments,id',
            'designation' => 'required|string|max:255',
            'branch_id' => 'nullable|exists:company_branches,id',
            'company_email' => 'required|email|unique:employees,company_email',
            'personal_email' => 'nullable|email',
            'personal_phone' => 'required|string|unique:employees,personal_phone',
            'dob' => 'required|date',
            'gender' => 'required|string',
            'blood_group' => 'required|string',
            'emergency_name' => 'required|string|max:255',
            'emergency_relation' => 'required|string',
            'emergency_phone' => 'required|string',
            'emergency_alt_phone' => 'nullable|string',
            'aadhaar' => 'required|string|unique:employees,aadhaar',
            'pan' => 'nullable|string|unique:employees,pan',
            'passport' => 'nullable|string',
            'driving_license' => 'nullable|string',
            'photo' => 'required|file|mimes:jpeg,jpg,png|max:150',
            'aadhaar_file' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'bank_name' => 'required|string|max:255',
            'account_holder' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'ifsc' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            
            'joining_date' => 'nullable|date',
            'employment_type' => 'nullable|string|max:255',
            'reporting_manager' => 'nullable|string|max:255',
            'work_location' => 'nullable|string|max:255',
            
            'present_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'marital_status' => 'nullable|string',
            'office_phone' => 'nullable|string',

            'education' => 'required|array|min:1',
            'education.*.qualification' => 'required|string|max:255',
            'education.*.course' => 'nullable|string|max:255',
            'education.*.institution' => 'nullable|string|max:255',
            'education.*.university' => 'nullable|string|max:255',
            'education.*.year' => 'nullable|string|max:255',
            'education.*.percentage' => 'nullable|string|max:255',
            'education.*.certificate' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
            
            'experience' => 'nullable|array',
            'experience.*.company' => 'nullable|string|max:255',
            'experience.*.designation' => 'nullable|string|max:255',
            'experience.*.from_date' => 'nullable|date',
            'experience.*.to_date' => 'nullable|date',
            'experience.*.years' => 'nullable|string|max:255',
            'experience.*.certificate' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
            
            'certificates' => 'nullable|array',
            'certificates.*.certificate_name' => 'nullable|string|max:255',
            'certificates.*.issued_by' => 'nullable|string|max:255',
            'certificates.*.issued_date' => 'nullable|date',
            'certificates.*.expiry_date' => 'nullable|date',
            'certificates.*.file' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'provided_assets' => 'nullable|array',
            'provided_assets.*' => 'nullable|string',
        ];
    }
}
