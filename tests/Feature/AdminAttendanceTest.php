<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Holiday;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminAttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_filter_returns_full_list_for_date_range_skipping_sundays_and_holidays()
    {
        $role = Role::create(['name' => 'Super Admin']);
        $user = User::factory()->create();
        $user->assignRole($role);

        $dept = Department::create(['name' => 'Sales']);

        $emp1 = Employee::create([
            'employee_id' => 'EMP001',
            'name' => 'John Sales',
            'department_id' => $dept->id,
            'status' => 'Active',
        ]);

        $emp2 = Employee::create([
            'employee_id' => 'EMP002',
            'name' => 'Jane Sales',
            'department_id' => $dept->id,
            'status' => 'Active',
        ]);

        // Emp1 has attendance on 2026-08-01 (Saturday)
        Attendance::create([
            'employee_id' => $emp1->id,
            'date' => '2026-08-01',
            'status' => 'Present',
            'check_in' => '09:00:00',
        ]);

        // Mark 2026-08-03 (Monday) as a Holiday
        Holiday::create([
            'name' => 'Test Holiday',
            'date' => '2026-08-03',
        ]);

        // Date range: 2026-08-01 (Sat) to 2026-08-04 (Tue)
        // 2026-08-02 is Sunday (skipped)
        // 2026-08-03 is Holiday (skipped)
        // Working days in range: Aug 1 and Aug 4 = 2 working days
        // 2 employees * 2 working days = 4 records total
        $response = $this->actingAs($user)->get(route('admin.attendance.index', [
            'department_id' => $dept->id,
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-04',
        ]));

        $response->assertStatus(200);

        /** @var \Illuminate\Support\Collection $attendances */
        $attendances = $response->viewData('attendances');

        $this->assertCount(4, $attendances);

        // Assert no records for Aug 2 (Sunday) or Aug 3 (Holiday) exist for unpunched employees
        $aug2Count = $attendances->where('date', '2026-08-02')->count();
        $aug3Count = $attendances->where('date', '2026-08-03')->count();
        $this->assertEquals(0, $aug2Count);
        $this->assertEquals(0, $aug3Count);
    }
}
