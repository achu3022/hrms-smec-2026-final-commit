<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendBirthdayReminders extends Command
{
    protected $signature = 'eims:birthday-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily birthday reminders for employees';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = \Carbon\Carbon::today();
        $employees = \App\Models\Employee::where('status', 'Active')
            ->whereMonth('dob', $today->month)
            ->whereDay('dob', $today->day)
            ->get();

        if ($employees->isEmpty()) {
            $this->info('No birthdays today.');
            return;
        }

        try {
            \Illuminate\Support\Facades\Mail::to('hr@company.com')->send(new \App\Mail\BirthdayReminder($employees));
            $this->info('Sent birthday reminders for ' . $employees->count() . ' employees to HR.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send birthday reminder email: ' . $e->getMessage());
            $this->error('Failed to send birthday reminder email.');
        }
    }
}
