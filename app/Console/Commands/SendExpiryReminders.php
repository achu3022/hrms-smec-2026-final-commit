<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendExpiryReminders extends Command
{
    protected $signature = 'eims:expiry-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily reminders for documents expiring within 30 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetDate = \Carbon\Carbon::today()->addDays(30)->toDateString();
        $expiringCerts = \App\Models\EmployeeCertificate::with('employee')
            ->where('expiry_date', $targetDate)
            ->get();

        if ($expiringCerts->isEmpty()) {
            $this->info('No documents expiring in 30 days.');
            return;
        }

        try {
            \Illuminate\Support\Facades\Mail::to('hr@company.com')->send(new \App\Mail\ExpiryReminder($expiringCerts));
            $this->info('Sent document expiry reminders for ' . $expiringCerts->count() . ' documents to HR.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send expiry reminder email: ' . $e->getMessage());
            $this->error('Failed to send expiry reminder email.');
        }
    }
}
