<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;

#[Signature('attendance:autopunchout')]
#[Description('Automatically punch out employees based on their shift end times.')]
class AutoPunchOut extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();
        $currentTime = $now->format('H:i');

        $attendances = Attendance::whereNotNull('check_in')
            ->whereNull('check_out')
            ->get();

        $count = 0;
        foreach ($attendances as $attendance) {
            $employee = $attendance->employee ?? Employee::find($attendance->employee_id);
            $shiftDetails = $employee ? $employee->getShiftDetails() : [
                'start_time' => '09:00',
                'end_time' => '18:00',
                'start_carbon' => Carbon::parse('09:00'),
                'end_carbon' => Carbon::parse('18:00'),
                'label' => '9:00 AM - 6:00 PM',
            ];

            $shiftEndTimeStr = strlen($shiftDetails['end_time']) === 5 ? $shiftDetails['end_time'] . ':00' : $shiftDetails['end_time'];
            $isPastDate = $attendance->date < $today;
            $isTodayAfter9PM = ($attendance->date == $today && $currentTime >= '21:00' && $currentTime >= $shiftDetails['end_time']);

            if ($isPastDate || $isTodayAfter9PM) {
                $attendance->update([
                    'check_out' => $shiftEndTimeStr
                ]);
                $empName = $employee ? ($employee->name ?? $employee->first_name . ' ' . $employee->last_name) : $attendance->employee_id;
                $this->info("Auto punched out employee {$empName} (ID: {$attendance->employee_id}) at {$shiftEndTimeStr} for date {$attendance->date}");
                $count++;
            }
        }

        $this->info("AutoPunchOut command executed successfully. Updated {$count} attendance records.");
    }
}
