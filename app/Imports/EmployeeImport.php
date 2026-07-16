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
        if (!isset($row['employee_id']) || !isset($row['name']) || !isset($row['company_email'])) {
            return null; // Skip invalid rows
        }

        $department = Department::firstOrCreate(['name' => $row['department'] ?? 'General']);
        $designation = Designation::firstOrCreate(['name' => $row['designation'] ?? 'Employee']);
        
        $branchId = null;
        if (!empty($row['branch'])) {
            $branch = CompanyBranch::firstOrCreate(['name' => $row['branch']]);
            $branchId = $branch->id;
        }

        return new Employee([
            'employee_id'     => $row['employee_id'],
            'name'            => $row['name'],
            'department_id'   => $department->id,
            'designation_id'  => $designation->id,
            'branch_id'       => $branchId,
            'company_email'   => $row['company_email'],
            'personal_email'  => $row['personal_email'] ?? null,
            'personal_phone'  => $row['personal_phone'] ?? '0000000000',
            'office_phone'    => $row['office_phone'] ?? null,
            'dob'             => $row['dob'] ?? null,
            'gender'          => $row['gender'] ?? 'Other',
            'joining_date'    => $row['joining_date'] ?? null,
            'status'          => $row['status'] ?? 'Active',
        ]);
    }
}
