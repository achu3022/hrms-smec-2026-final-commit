<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;

class AutoPunchOutTest extends TestCase
{
    use RefreshDatabase;

    public function test_past_date_attendance_without_checkout_is_auto_punched_out()
    {
        $employee = Employee::create([
            'employee_id' => 'EMP001',
            'name' => 'John Doe',
            'shift' => '09:00 AM - 06:00 PM',
        ]);

        $pastDate = Carbon::yesterday()->toDateString();

        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'date' => $pastDate,
            'check_in' => '09:05:00',
            'check_out' => null,
            'status' => 'Present',
        ]);

        // Freeze time at 14:00 today (before 9 PM today)
        Carbon::setTestNow(Carbon::today()->setTime(14, 0, 0));

        $this->artisan('attendance:autopunchout')
            ->assertExitCode(0);

        $attendance->refresh();
        $this->assertNotNull($attendance->check_out);
        $this->assertEquals('18:00:00', $attendance->check_out);
    }

    public function test_today_attendance_before_9pm_is_not_auto_punched_out()
    {
        $employee = Employee::create([
            'employee_id' => 'EMP002',
            'name' => 'Jane Smith',
            'shift' => '09:00 AM - 06:00 PM',
        ]);

        $today = Carbon::today()->toDateString();

        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'date' => $today,
            'check_in' => '09:00:00',
            'check_out' => null,
            'status' => 'Present',
        ]);

        // Freeze time at 19:30 today (7:30 PM, after shift end but before 9 PM)
        Carbon::setTestNow(Carbon::today()->setTime(19, 30, 0));

        $this->artisan('attendance:autopunchout')
            ->assertExitCode(0);

        $attendance->refresh();
        $this->assertNull($attendance->check_out);
    }

    public function test_today_attendance_after_9pm_is_auto_punched_out()
    {
        $employee = Employee::create([
            'employee_id' => 'EMP003',
            'name' => 'Alice Johnson',
            'shift' => '09:00 AM - 06:00 PM',
        ]);

        $today = Carbon::today()->toDateString();

        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'date' => $today,
            'check_in' => '09:00:00',
            'check_out' => null,
            'status' => 'Present',
        ]);

        // Freeze time at 21:15 today (9:15 PM)
        Carbon::setTestNow(Carbon::today()->setTime(21, 15, 0));

        $this->artisan('attendance:autopunchout')
            ->assertExitCode(0);

        $attendance->refresh();
        $this->assertNotNull($attendance->check_out);
        $this->assertEquals('18:00:00', $attendance->check_out);
    }
}
