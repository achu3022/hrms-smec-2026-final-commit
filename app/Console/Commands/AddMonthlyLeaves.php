<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\LeaveBalance;

class AddMonthlyLeaves extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaves:add-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds 1 Casual and 1 Medical leave to each active employee monthly, capped at 12 per year.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $employees = Employee::where('status', 'Active')->get();
        $casualType = LeaveType::where('name', 'Casual')->first();
        $medicalType = LeaveType::where('name', 'Medical')->first();

        if (!$casualType || !$medicalType) {
            $this->error('Leave types not found.');
            return;
        }

        $count = 0;
        foreach ($employees as $employee) {
            foreach ([$casualType, $medicalType] as $type) {
                $balance = LeaveBalance::firstOrCreate(
                    ['employee_id' => $employee->id, 'leave_type_id' => $type->id],
                    ['remaining' => 0, 'used' => 0]
                );

                if ($balance->remaining < $type->days_allowed) {
                    $balance->remaining += 1;
                    $balance->save();
                }
            }
            $count++;
        }

        $this->info("Successfully added monthly leaves to {$count} employees.");
    }
}
