<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Services\LeaveAllocationService;
use Carbon\Carbon;

class AccrueMonthlyLeaves extends Command
{
    protected $signature = 'leaves:accrue-monthly';
    protected $description = 'Accrues 1 Medical and 1 Casual leave for all active employees every month with max limits and carry forward.';

    public function handle(LeaveAllocationService $leaveService)
    {
        $this->info('Starting monthly leave accrual and balance sync...');

        $activeEmployees = Employee::where('status', 'Active')->get();
        $count = 0;
        $now = Carbon::now();
        $year = (int)$now->format('Y');
        $month = (int)$now->format('m');

        foreach ($activeEmployees as $employee) {
            $leaveService->getOrCalculateMonthlyBalance($employee, $year, $month);
            $count++;
        }

        $this->info("Successfully accrued and synced leaves for {$count} active employees for {$now->format('F Y')}.");
    }
}
