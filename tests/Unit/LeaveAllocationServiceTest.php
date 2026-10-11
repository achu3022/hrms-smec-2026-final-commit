<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\LeaveRequest;
use App\Models\LeaveSegment;
use App\Models\EmployeeLeaveBalance;
use App\Services\LeaveAllocationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LeaveAllocationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LeaveAllocationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LeaveAllocationService();

        config(['hrms.system_start_date' => '2026-01-01']);

        LeaveType::firstOrCreate(['name' => 'Casual'], ['is_active' => true]);
        LeaveType::firstOrCreate(['name' => 'Medical'], ['is_active' => true]);
        LeaveType::firstOrCreate(['name' => 'Loss of Pay (LOP)'], ['is_active' => true]);
    }

    protected function createTestEmployee(array $attributes = []): Employee
    {
        static $counter = 1;
        return Employee::create(array_merge([
            'employee_id' => 'EMP' . str_pad($counter++, 3, '0', STR_PAD_LEFT),
            'name' => 'Test Employee',
            'company_email' => 'test' . uniqid() . '@example.com',
            'joining_date' => '2026-01-01',
            'status' => 'Active',
        ], $attributes));
    }

    public function test_monthly_accrual_and_carry_forward()
    {
        config(['hrms.system_start_date' => '2026-07-01']);

        $employee = $this->createTestEmployee([
            'joining_date' => '2026-01-01',
        ]);

        // July 2026 balance (First active month)
        $julBal = $this->service->getOrCalculateMonthlyBalance($employee, 2026, 7);
        $this->assertEquals(0.0, $julBal->opening_cl);
        $this->assertEquals(1.0, $julBal->credited_cl);
        $this->assertEquals(1.0, $julBal->closing_cl);

        // August 2026 balance (should carry forward 1.0 CL + 1.0 Credit = 2.0)
        $augBal = $this->service->getOrCalculateMonthlyBalance($employee, 2026, 8);
        $this->assertEquals(1.0, $augBal->opening_cl);
        $this->assertEquals(1.0, $augBal->credited_cl);
        $this->assertEquals(2.0, $augBal->closing_cl);
    }

    public function test_maximum_carry_forward_limit_cap_at_12()
    {
        config(['hrms.system_start_date' => '2025-01-01']);

        $employee = $this->createTestEmployee([
            'joining_date' => '2025-01-01',
        ]);

        // Calculate for December 2025 (12 months = 12 CL)
        $decBal = $this->service->getOrCalculateMonthlyBalance($employee, 2025, 12);
        $this->assertEquals(12.0, $decBal->closing_cl);

        // January 2026 (13th month, should stay capped at 12.0)
        $janBal = $this->service->getOrCalculateMonthlyBalance($employee, 2026, 1);
        $this->assertEquals(12.0, $janBal->opening_cl);
        $this->assertEquals(0.0, $janBal->credited_cl); // Cannot exceed max 12
        $this->assertEquals(12.0, $janBal->closing_cl);
    }

    public function test_multi_month_leave_splitting_and_lop_allocation()
    {
        config(['hrms.system_start_date' => '2026-07-01']);

        $employee = $this->createTestEmployee([
            'joining_date' => '2026-01-01',
        ]);

        $casualType = LeaveType::where('name', 'Casual')->first();

        // 30-Jul-2026 (Thu) to 03-Aug-2026 (Mon) = 3 weekdays (Thu 30 Jul, Fri 31 Jul, Mon 03 Aug)
        // Jul: 1 day CL available -> 1 CL allocated.
        // Aug: Opening CL = 0, Credit = 1 => 1 CL available -> 1 CL allocated, 1 LOP allocated.
        $start = Carbon::parse('2026-07-30');
        $end = Carbon::parse('2026-08-03');

        $alloc = $this->service->calculateAllocation($employee, $start, $end, $casualType->id);

        $this->assertCount(2, $alloc['month_segments']);
        $this->assertEquals(3, $alloc['total_days']);
        $this->assertEquals(2, $alloc['paid_days']);
        $this->assertEquals(1, $alloc['lop_days']);
    }

    public function test_casual_leave_exhaustion_splits_into_lop()
    {
        config(['hrms.system_start_date' => '2026-07-01']);

        $employee = $this->createTestEmployee([
            'joining_date' => '2026-01-01',
        ]);

        $casualType = LeaveType::where('name', 'Casual')->first();

        // Request in Aug 2026: 03-Aug-2026 to 07-Aug-2026 (5 weekdays).
        // Balance in Aug = 2 days CL.
        $start = Carbon::parse('2026-08-03');
        $end = Carbon::parse('2026-08-07');

        $alloc = $this->service->calculateAllocation($employee, $start, $end, $casualType->id);

        $this->assertEquals(5, $alloc['total_days']);
        $this->assertEquals(2, $alloc['paid_days']);
        $this->assertEquals(3, $alloc['lop_days']);
    }

    public function test_leave_type_availability_hides_zero_balance_types()
    {
        config(['hrms.system_start_date' => '2026-07-01']);

        $employee = $this->createTestEmployee([
            'joining_date' => '2026-01-01',
        ]);

        // Manually exhaust CL for Jul 2026
        EmployeeLeaveBalance::create([
            'employee_id' => $employee->id,
            'year' => 2026,
            'month' => 7,
            'opening_cl' => 0,
            'credited_cl' => 1,
            'used_cl' => 1,
            'closing_cl' => 0,
            'opening_ml' => 0,
            'credited_ml' => 1,
            'used_ml' => 0,
            'closing_ml' => 1,
            'lop_days' => 0,
        ]);

        $start = Carbon::parse('2026-07-15');
        $end = Carbon::parse('2026-07-16');

        $alloc = $this->service->calculateAllocation($employee, $start, $end);

        $typeNames = array_column($alloc['available_leave_types'], 'name');

        // Casual should be excluded because available CL is 0. Medical and LOP should be present.
        $this->assertNotContains('Casual', $typeNames);
        $this->assertContains('Medical', $typeNames);
        $this->assertContains('Loss of Pay (LOP)', $typeNames);
    }

    public function test_casual_leave_is_disabled_after_applying_once_in_month()
    {
        config(['hrms.system_start_date' => '2026-07-01']);

        $employee = $this->createTestEmployee([
            'joining_date' => '2026-01-01',
        ]);

        $casualType = LeaveType::where('name', 'Casual')->first();

        // 1. Submit a 1-day Casual leave request in July 2026 (Status: Pending)
        $leaveRequest = $this->service->processLeaveSubmission($employee, [
            'start_date' => '2026-07-27',
            'end_date' => '2026-07-27',
            'leave_type_id' => $casualType->id,
            'reason' => 'Family event',
        ]);

        $this->assertEquals('Pending', $leaveRequest->status);

        // 2. Check balance for July 2026: Used CL should be 1.0, Closing CL should be 0.0
        $dashBal = $this->service->getEmployeeDashboardBalances($employee, Carbon::parse('2026-07-01'));
        $this->assertEquals(1.0, $dashBal['casual']['used']);
        $this->assertEquals(0.0, $dashBal['casual']['available']);

        // 3. Attempt to select dates again in July 2026
        $alloc = $this->service->calculateAllocation($employee, Carbon::parse('2026-07-28'), Carbon::parse('2026-07-28'));
        $typeNames = array_column($alloc['available_leave_types'], 'name');

        // Casual leave option must NOT be available because CL balance for July is now 0.
        $this->assertNotContains('Casual', $typeNames);
        $this->assertContains('Medical', $typeNames);
        $this->assertContains('Loss of Pay (LOP)', $typeNames);
    }
}
