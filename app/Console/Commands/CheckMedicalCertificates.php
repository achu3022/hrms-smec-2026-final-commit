<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LeaveRequest;
use Carbon\Carbon;

class CheckMedicalCertificates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaves:check-certificates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for expired medical certificates (2 days after leave ends) and converts them to LOP.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $leaves = LeaveRequest::where('status', 'TL Approved')
            ->whereNull('medical_certificate_path')
            ->whereHas('leaveType', function ($q) {
                $q->where('name', 'Medical');
            })
            ->get();
            
        $count = 0;
        foreach ($leaves as $leave) {
            $endDate = Carbon::parse($leave->end_date);
            // Check if current time is past the end of the 2nd day after leave end date
            if (now()->startOfDay()->greaterThan($endDate->copy()->addDays(2)->endOfDay())) {
                $leave->update([
                    'status' => 'Approved',
                    'paid_days' => 0,
                    'lop_days' => $leave->total_days,
                    'management_approved_at' => now(),
                    'reason' => $leave->reason . "\n[System: Medical Certificate not uploaded within 2 days. Converted to LOP.]"
                ]);
                $count++;
            }
        }
        
        $this->info("Converted $count expired medical leaves to LOP.");
    }
}
