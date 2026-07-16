<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            'Automation',
            'Instrumentation',
            'Embedded Systems',
            'BMS',
            'MEP',
            'Civil',
            'Oil and Gas',
            'Accounting',
            'Networking',
            'Fintech',
            'Graphics',
            'Video Editing',
            'Digital Marketing',
            'Logistics',
            'Health Care Management',
            'Hospitality Management',
            'Human Resource Departments',
            'Admin',
            'Placements',
            'Sales',
            'Marketing',
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate([
                'name' => $dept
            ], [
                'is_active' => true
            ]);
        }
    }
}
