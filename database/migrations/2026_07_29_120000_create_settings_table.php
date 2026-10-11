<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general')->index();
            $table->string('type')->default('string'); // string, integer, boolean, time, json
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert Default System Settings
        $defaultSettings = [
            // Attendance & Timings Settings
            [
                'key' => 'office_start_time',
                'value' => '09:00',
                'group' => 'attendance',
                'type' => 'time',
                'label' => 'Office Start Time',
                'description' => 'Default morning start time for employees.',
            ],
            [
                'key' => 'office_end_time',
                'value' => '18:00',
                'group' => 'attendance',
                'type' => 'time',
                'label' => 'Office End Time',
                'description' => 'Default evening end time for employees.',
            ],
            [
                'key' => 'late_grace_period_minutes',
                'value' => '5',
                'group' => 'attendance',
                'type' => 'integer',
                'label' => 'Late Grace Period (Minutes)',
                'description' => 'Grace minutes allowed after shift start time before check-in is marked as Late (e.g. 5 mins = up to 09:05 AM for a 9:00 AM shift).',
            ],
            [
                'key' => 'half_day_late_threshold_minutes',
                'value' => '15',
                'group' => 'attendance',
                'type' => 'integer',
                'label' => 'Half-Day Late Threshold (Minutes)',
                'description' => 'Arrival after shift start time + these minutes will be marked as Half-Day (e.g. 15 mins = after 09:15 AM for a 9:00 AM shift).',
            ],
            [
                'key' => 'max_late_allowed_per_month',
                'value' => '3',
                'group' => 'attendance',
                'type' => 'integer',
                'label' => 'Max Allowed Late Marks Per Month',
                'description' => 'Maximum allowed late check-ins per month before flag/warning.',
            ],
            [
                'key' => 'early_checkout_grace_minutes',
                'value' => '15',
                'group' => 'attendance',
                'type' => 'integer',
                'label' => 'Early Checkout Grace Period (Minutes)',
                'description' => 'Allowed minutes before office end time without early going penalty.',
            ],

            // Leave Policies
            [
                'key' => 'monthly_credit_cl',
                'value' => '1.0',
                'group' => 'leave',
                'type' => 'string',
                'label' => 'Monthly Casual Leave (CL) Credit',
                'description' => 'Casual leave credited per month to each active employee.',
            ],
            [
                'key' => 'monthly_credit_ml',
                'value' => '1.0',
                'group' => 'leave',
                'type' => 'string',
                'label' => 'Monthly Medical Leave (ML) Credit',
                'description' => 'Medical leave credited per month to each active employee.',
            ],
            [
                'key' => 'max_balance_cl',
                'value' => '12.0',
                'group' => 'leave',
                'type' => 'string',
                'label' => 'Maximum CL Accumulation Cap',
                'description' => 'Maximum CL balance an employee can hold.',
            ],
            [
                'key' => 'max_balance_ml',
                'value' => '12.0',
                'group' => 'leave',
                'type' => 'string',
                'label' => 'Maximum ML Accumulation Cap',
                'description' => 'Maximum ML balance an employee can hold.',
            ],
            [
                'key' => 'medical_certificate_required_days',
                'value' => '2',
                'group' => 'leave',
                'type' => 'integer',
                'label' => 'Medical Certificate Required (Days)',
                'description' => 'Leaves exceeding this number of consecutive days require a medical certificate upload.',
            ],
            [
                'key' => 'sandwich_leave_rule',
                'value' => '0',
                'group' => 'leave',
                'type' => 'boolean',
                'label' => 'Sandwich Leave Rule Enabled',
                'description' => 'If enabled, weekends/holidays falling between leave days are counted as leave.',
            ],

            // Date, Time & Locale
            [
                'key' => 'company_timezone',
                'value' => 'Asia/Kolkata',
                'group' => 'datetime',
                'type' => 'string',
                'label' => 'Company Timezone',
                'description' => 'Timezone used across attendance logs, reports, and notifications.',
            ],
            [
                'key' => 'date_format',
                'value' => 'Y-m-d',
                'group' => 'datetime',
                'type' => 'string',
                'label' => 'System Date Format',
                'description' => 'Format used to display dates (e.g. Y-m-d, d/m/Y, d-m-Y).',
            ],
            [
                'key' => 'time_format',
                'value' => '12',
                'group' => 'datetime',
                'type' => 'string',
                'label' => 'Time Format',
                'description' => 'Time format (12 for 12-hour AM/PM, 24 for 24-hour).',
            ],

            // General & Feature Controls
            [
                'key' => 'company_name',
                'value' => 'SMEC HRMS',
                'group' => 'general',
                'type' => 'string',
                'label' => 'Company / Organization Name',
                'description' => 'Name displayed on reports, certs, and app headers.',
            ],
            [
                'key' => 'geofence_strict_mode',
                'value' => '1',
                'group' => 'general',
                'type' => 'boolean',
                'label' => 'Enforce Geofence for Attendance',
                'description' => 'If enabled, employees must be within designated office premises to punch in/out.',
            ],
            [
                'key' => 'require_selfie_punch',
                'value' => '1',
                'group' => 'general',
                'type' => 'boolean',
                'label' => 'Require Photo / Selfie Verification',
                'description' => 'If enabled, live photo capture is compulsory during attendance punch.',
            ],
            [
                'key' => 'enable_blink_liveness',
                'value' => '1',
                'group' => 'general',
                'type' => 'boolean',
                'label' => 'Require Eye-Blink Liveness Detection',
                'description' => 'If enabled, employees must blink their eyes to verify liveness before selfie is captured.',
            ],
            [
                'key' => 'default_daily_report_required',
                'value' => '1',
                'group' => 'general',
                'type' => 'boolean',
                'label' => 'Default Daily Report Requirement',
                'description' => 'Enable daily task report submission by default for new employees.',
            ],
        ];

        foreach ($defaultSettings as $setting) {
            $setting['created_at'] = now();
            $setting['updated_at'] = now();
            DB::table('settings')->insertOrIgnore($setting);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
