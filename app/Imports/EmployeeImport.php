<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use App\Models\CompanyBranch;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeeImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        \Illuminate\Support\Facades\Log::info("Excel Row: " . json_encode($row));
        
        // Flexible key matching
        $empId = $row['employee_id'] ?? $row['emp_id'] ?? $row['emp_code'] ?? $row['employee_code'] ?? $row['id'] ?? null;
        $name = $row['full_name'] ?? $row['fullname'] ?? $row['name'] ?? $row['employee_name'] ?? null;
        
        if (!$empId || !$name) {
            \Illuminate\Support\Facades\Log::warning("Skipped row due to missing ID or Name.");
            return null; // Skip invalid rows
        }

        $department = Department::firstOrCreate(['name' => $row['department'] ?? 'General']);
        $designation = Designation::firstOrCreate(['name' => $row['designation'] ?? 'Employee']);
        
        $branchId = null;
        if (!empty($row['branch'])) {
            $branch = CompanyBranch::firstOrCreate(['name' => $row['branch']]);
            $branchId = $branch->id;
        }

        $dob = $row['dob'] ?? null;
        if (is_numeric($dob)) {
            $dob = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dob)->format('Y-m-d');
        } elseif ($dob) {
            try {
                $dob = \Carbon\Carbon::parse($dob)->format('Y-m-d');
            } catch (\Exception $e) {
                // fallback to original if parsing fails
            }
        }

        return new Employee([
            'employee_id'     => $empId,
            'name'            => $name,
            'department_id'   => $department->id,
            'designation_id'  => $designation->id,
            'branch_id'       => $branchId,
            'company_email'   => $row['company_email'] ?? null,
            'personal_email'  => $row['personal_email'] ?? null,
            'personal_phone'  => $row['personal_phone'] ?? '0000000000',
            'office_phone'    => $row['office_phone'] ?? null,
            'dob'             => $dob,
            'gender'          => $row['gender'] ?? 'Other',
            'joining_date'    => $row['joining_date'] ?? null,
            'status'          => $row['status'] ?? 'Active',
            'is_profile_completed' => false,
        ]);
    }
}
