@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--brand-dark);"><i class="fa-solid fa-sliders me-2 text-primary"></i>System Settings</h3>
            <p class="text-muted mb-0">Manage attendance timings, late policies, leave rules, and system configurations.</p>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs card-header-tabs m-0 border-0" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active py-3 px-4 fw-semibold border-0" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance-pane" type="button" role="tab">
                        <i class="fa-solid fa-clock me-2 text-primary"></i>Attendance & Timings
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-4 fw-semibold border-0" id="leave-tab" data-bs-toggle="tab" data-bs-target="#leave-pane" type="button" role="tab">
                        <i class="fa-solid fa-calendar-days me-2 text-success"></i>Leave Policies
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-4 fw-semibold border-0" id="datetime-tab" data-bs-toggle="tab" data-bs-target="#datetime-pane" type="button" role="tab">
                        <i class="fa-solid fa-globe me-2 text-info"></i>Date, Time & Locale
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-4 fw-semibold border-0" id="general-tab" data-bs-toggle="tab" data-bs-target="#general-pane" type="button" role="tab">
                        <i class="fa-solid fa-gears me-2 text-warning"></i>General Controls
                    </button>
                </li>
            </ul>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            <div class="card-body p-4">
                <div class="tab-content" id="settingsTabContent">

                    <!-- Attendance & Timings Tab -->
                    <div class="tab-pane fade show active" id="attendance-pane" role="tabpanel">
                        <h5 class="fw-bold mb-3 text-dark">Work Schedule & Late Rules</h5>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Office Start Time</label>
                                <input type="time" name="office_start_time" class="form-control form-control-lg" value="{{ \App\Models\Setting::get('office_start_time', '09:00') }}" required>
                                <div class="form-text">Standard morning check-in time.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Office End Time</label>
                                <input type="time" name="office_end_time" class="form-control form-control-lg" value="{{ \App\Models\Setting::get('office_end_time', '18:00') }}" required>
                                <div class="form-text">Standard evening check-out time.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Late Grace Period (Minutes)</label>
                                <input type="number" name="late_grace_period_minutes" class="form-control form-control-lg" value="{{ \App\Models\Setting::get('late_grace_period_minutes', 5) }}" min="0" required>
                                <div class="form-text">Minutes allowed after shift start time before check-in is marked as Late (e.g. 5 mins = up to 09:05 AM for a 9:00 AM shift).</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Half-Day Late Threshold (Minutes)</label>
                                <input type="number" name="half_day_late_threshold_minutes" class="form-control form-control-lg" value="{{ \App\Models\Setting::get('half_day_late_threshold_minutes', 15) }}" min="0" required>
                                <div class="form-text">Arrival after shift start time + these minutes is marked as Half-Day (e.g. 15 mins = after 09:15 AM for a 9:00 AM shift).</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Early Checkout Grace Period (Minutes)</label>
                                <input type="number" name="early_checkout_grace_minutes" class="form-control form-control-lg" value="{{ \App\Models\Setting::get('early_checkout_grace_minutes', 15) }}" min="0" required>
                                <div class="form-text">Allowed checkout minutes prior to office end time without early going penalty.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Max Allowed Late Marks Per Month</label>
                                <input type="number" name="max_late_allowed_per_month" class="form-control form-control-lg" value="{{ \App\Models\Setting::get('max_late_allowed_per_month', 3) }}" min="0" required>
                                <div class="form-text">Number of late marks allowed before flagged in attendance reports.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Leave Policies Tab -->
                    <div class="tab-pane fade" id="leave-pane" role="tabpanel">
                        <h5 class="fw-bold mb-3 text-dark">Leave Allocations & Rules</h5>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Monthly Casual Leave (CL) Credit</label>
                                <input type="number" step="0.5" name="monthly_credit_cl" class="form-control form-control-lg" value="{{ \App\Models\Setting::get('monthly_credit_cl', '1.0') }}" required>
                                <div class="form-text">CL days credited to active employees at the start of each month.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Monthly Medical Leave (ML) Credit</label>
                                <input type="number" step="0.5" name="monthly_credit_ml" class="form-control form-control-lg" value="{{ \App\Models\Setting::get('monthly_credit_ml', '1.0') }}" required>
                                <div class="form-text">ML days credited to active employees at the start of each month.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Maximum CL Accumulation Cap</label>
                                <input type="number" step="0.5" name="max_balance_cl" class="form-control form-control-lg" value="{{ \App\Models\Setting::get('max_balance_cl', '12.0') }}" required>
                                <div class="form-text">Maximum total casual leave balance allowed per employee.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Maximum ML Accumulation Cap</label>
                                <input type="number" step="0.5" name="max_balance_ml" class="form-control form-control-lg" value="{{ \App\Models\Setting::get('max_balance_ml', '12.0') }}" required>
                                <div class="form-text">Maximum total medical leave balance allowed per employee.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Medical Certificate Required Threshold (Days)</label>
                                <input type="number" name="medical_certificate_required_days" class="form-control form-control-lg" value="{{ \App\Models\Setting::get('medical_certificate_required_days', 2) }}" min="1" required>
                                <div class="form-text">Leaves exceeding this duration require a medical document upload.</div>
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                                <div class="form-check form-switch fs-5 mt-3">
                                    <input class="form-check-input" type="checkbox" role="switch" id="sandwich_leave_rule" name="sandwich_leave_rule" value="1" {{ \App\Models\Setting::get('sandwich_leave_rule', false) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-dark fs-6" for="sandwich_leave_rule">Enable Sandwich Leave Rule</label>
                                    <div class="form-text fs-6">If active, holidays/weekends inside a continuous leave period are counted as leave.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Date, Time & Locale Tab -->
                    <div class="tab-pane fade" id="datetime-pane" role="tabpanel">
                        <h5 class="fw-bold mb-3 text-dark">Timezone & Regional Formatting</h5>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Company Timezone</label>
                                <select name="company_timezone" class="form-select form-select-lg">
                                    @php $tz = \App\Models\Setting::get('company_timezone', 'Asia/Kolkata'); @endphp
                                    <option value="Asia/Kolkata" {{ $tz === 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata (IST +5:30)</option>
                                    <option value="UTC" {{ $tz === 'UTC' ? 'selected' : '' }}>UTC (GMT +0:00)</option>
                                    <option value="Asia/Dubai" {{ $tz === 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai (GST +4:00)</option>
                                    <option value="America/New_York" {{ $tz === 'America/New_York' ? 'selected' : '' }}>America/New_York (EST -5:00)</option>
                                    <option value="Europe/London" {{ $tz === 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT/BST)</option>
                                </select>
                                <div class="form-text">Primary timezone for punch logs, timestamps, and scheduling.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">System Date Format</label>
                                <select name="date_format" class="form-select form-select-lg">
                                    @php $df = \App\Models\Setting::get('date_format', 'Y-m-d'); @endphp
                                    <option value="Y-m-d" {{ $df === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (2026-07-29)</option>
                                    <option value="d/m/Y" {{ $df === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (29/07/2026)</option>
                                    <option value="d-m-Y" {{ $df === 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY (29-07-2026)</option>
                                    <option value="M d, Y" {{ $df === 'M d, Y' ? 'selected' : '' }}>Mon DD, YYYY (Jul 29, 2026)</option>
                                </select>
                                <div class="form-text">Display format for dates in reports and lists.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Time Format</label>
                                <select name="time_format" class="form-select form-select-lg">
                                    @php $tf = \App\Models\Setting::get('time_format', '12'); @endphp
                                    <option value="12" {{ $tf === '12' ? 'selected' : '' }}>12-Hour AM/PM (09:00 AM)</option>
                                    <option value="24" {{ $tf === '24' ? 'selected' : '' }}>24-Hour Military (09:00)</option>
                                </select>
                                <div class="form-text">Preferred time format throughout the application.</div>
                            </div>
                        </div>
                    </div>

                    <!-- General Controls Tab -->
                    <div class="tab-pane fade" id="general-pane" role="tabpanel">
                        <h5 class="fw-bold mb-3 text-dark">Organization & Feature Verification Controls</h5>
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Company / Organization Name</label>
                                <input type="text" name="company_name" class="form-control form-control-lg" value="{{ \App\Models\Setting::get('company_name', 'SMEC HRMS') }}" required>
                                <div class="form-text">Brand name displayed on reports, headers, and certificates.</div>
                            </div>

                            <div class="col-md-6">
                                <div class="card bg-light border-0 p-3 rounded-3">
                                    <div class="form-check form-switch fs-5">
                                        <input class="form-check-input" type="checkbox" role="switch" id="geofence_strict_mode" name="geofence_strict_mode" value="1" {{ \App\Models\Setting::get('geofence_strict_mode', true) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold text-dark fs-6" for="geofence_strict_mode">Enforce Geofence Verification</label>
                                        <div class="form-text fs-6">Require employees to be within approved office radius when punching in/out.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card bg-light border-0 p-3 rounded-3">
                                    <div class="form-check form-switch fs-5">
                                        <input class="form-check-input" type="checkbox" role="switch" id="require_selfie_punch" name="require_selfie_punch" value="1" {{ \App\Models\Setting::get('require_selfie_punch', true) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold text-dark fs-6" for="require_selfie_punch">Require Photo/Selfie Capture</label>
                                        <div class="form-text fs-6">Compel live webcam/camera photo upload during attendance check-in/out.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card bg-light border-0 p-3 rounded-3">
                                    <div class="form-check form-switch fs-5">
                                        <input class="form-check-input" type="checkbox" role="switch" id="enable_blink_liveness" name="enable_blink_liveness" value="1" {{ \App\Models\Setting::get('enable_blink_liveness', true) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold text-dark fs-6" for="enable_blink_liveness">Eye-Blink Liveness Detection</label>
                                        <div class="form-text fs-6">Require employees to blink their eyes to verify liveness before selfie is captured.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card bg-light border-0 p-3 rounded-3">
                                    <div class="form-check form-switch fs-5">
                                        <input class="form-check-input" type="checkbox" role="switch" id="default_daily_report_required" name="default_daily_report_required" value="1" {{ \App\Models\Setting::get('default_daily_report_required', true) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold text-dark fs-6" for="default_daily_report_required">Default Daily Task Report Requirement</label>
                                        <div class="form-text fs-6">Enable mandatory daily task reporting by default for newly added employees.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card-footer bg-light p-3 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary btn-lg px-5 rounded-3 fw-bold">
                    <i class="fa-solid fa-floppy-disk me-2"></i>Save All Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .nav-tabs .nav-link {
        color: #64748b;
        background: transparent;
        border-bottom: 3px solid transparent !important;
        transition: all 0.2s ease-in-out;
    }
    .nav-tabs .nav-link:hover {
        color: var(--brand-primary);
        background: #f8fafc;
    }
    .nav-tabs .nav-link.active {
        color: var(--brand-primary);
        background: #ffffff;
        border-bottom: 3px solid var(--brand-primary) !important;
    }
</style>
@endpush
