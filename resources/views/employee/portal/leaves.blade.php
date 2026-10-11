@extends('employee.portal.layout')

@push('styles')
<style>
    /* Form Layout */
    .lv-form-card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:20px; margin-bottom:24px; }
    .lv-form-grid { display:grid; grid-template-columns: 1fr 1fr; gap:14px; width:100%; box-sizing:border-box; }
    @media(max-width:640px){ .lv-form-grid{ grid-template-columns:1fr; } }
    .lv-form-group { display:flex; flex-direction:column; gap:5px; width:100%; box-sizing:border-box; }
    .lv-form-label { font-size:.75rem; font-weight:700; color:#475569; }
    .lv-form-input, .lv-form-textarea {
        border:1px solid #e2e8f0; border-radius:8px; padding:8px 12px;
        font-size:.85rem; color:#1e293b; background:#f8fafc; outline:none;
        transition:border-color .2s; font-family:inherit; width:100%; box-sizing:border-box;
    }
    .lv-form-input:focus, .lv-form-textarea:focus { border-color:#6366f1; background:#fff; }
    .lv-form-input:disabled, .lv-form-textarea:disabled, select:disabled {
        background:#f1f5f9; color:#94a3b8; cursor:not-allowed; border-color:#e2e8f0;
    }
    .lv-form-textarea { resize:vertical; min-height:90px; }
    .lv-submit-btn {
        background:#2b3a67; color:#fff; border:none; border-radius:8px;
        padding:10px 22px; font-size:.85rem; font-weight:700; cursor:pointer; transition:all .2s;
        display:inline-flex; items-center; justify-content:center; gap:6px; width:100%;
    }
    .lv-submit-btn:hover { background:#1e293b; }
    .lv-submit-btn:disabled { background:#94a3b8; cursor:not-allowed; opacity:.7; }

    /* Dashboard Balance Cards */
    .lv-dash-grid { display:grid; grid-template-columns:repeat(3, 1fr); gap:16px; margin-bottom:24px; }
    @media(max-width:768px){ .lv-dash-grid{ grid-template-columns:1fr; } }
    
    .lv-dash-card {
        background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:18px;
        box-shadow:0 1px 3px rgba(0,0,0,0.02); display:flex; flex-direction:column; justify-content:space-between;
    }
    .lv-dash-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; }
    .lv-dash-title { font-size:.85rem; font-weight:800; color:#334155; text-transform:uppercase; letter-spacing:.5px; }
    .lv-dash-badge { font-size:.65rem; font-weight:700; padding:3px 8px; border-radius:6px; }
    .badge-casual { background:#e0e7ff; color:#3730a3; }
    .badge-medical { background:#fce7f3; color:#9d174d; }
    .badge-lop { background:#feefc3; color:#a16207; }
    
    .lv-dash-avail { font-size:2rem; font-weight:800; color:#0f172a; line-height:1; margin-bottom:14px; }
    .lv-dash-subgrid { display:grid; grid-template-columns:repeat(3, 1fr); gap:8px; padding-top:12px; border-top:1px dashed #e2e8f0; }
    .lv-sub-item { display:flex; flex-direction:column; text-align:center; }
    .lv-sub-val { font-size:.9rem; font-weight:700; color:#334155; }
    .lv-sub-lbl { font-size:.62rem; font-weight:600; color:#94a3b8; text-transform:uppercase; }

    /* Allocation Preview */
    .allocation-box { background:#f8fafc; border:1px solid #cbd5e1; border-radius:10px; padding:14px; margin-top:10px; }
    .allocation-item { display:flex; justify-content:space-between; align-items:center; font-size:.8rem; padding:6px 0; border-bottom:1px solid #e2e8f0; }
    .allocation-item:last-child { border-bottom:none; }

    /* History Table */
    .lv-table { width:100%; border-collapse:collapse; }
    .lv-table th { background:#f8fafc; color:#64748b; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; padding:10px 14px; text-align:left; border-bottom:1px solid #e2e8f0; }
    .lv-table td { padding:12px 14px; font-size:.82rem; border-bottom:1px solid #f1f5f9; color:#374151; }
    .lv-table tr:last-child td { border-bottom:none; }
    .lv-table tr:hover td { background:#f8fafc; }
    .lv-pill { display:inline-flex; align-items:center; gap:4px; font-size:.68rem; font-weight:700; padding:3px 10px; border-radius:6px; }
    .pill-pending  { background:#fef9c3; color:#ca8a04; }
    .pill-approved { background:#dcfce7; color:#16a34a; }
    .pill-rejected { background:#fee2e2; color:#dc2626; }
</style>
@endpush

@section('content')
<div class="mb-5 flex flex-col md:flex-row md:items-center justify-between gap-2">
    <div>
        <h2 class="text-xl font-bold text-slate-800">My Leaves</h2>
        <p class="text-sm text-slate-400 mt-0.5">Apply for leave, check monthly balances, and view your request history</p>
    </div>
    <div class="text-xs font-semibold bg-indigo-50 border border-indigo-200 text-indigo-700 px-3 py-1.5 rounded-lg flex items-center gap-1.5 self-start">
        <i class="fa-solid fa-calendar-day text-indigo-500"></i> Active Month: <span id="dash_month_name" class="font-bold">{{ $balanceStats['month_name'] }}</span>
    </div>
</div>

{{-- Leave Balances Dashboard (Requirement #10) --}}
<div class="lv-dash-grid">
    {{-- Casual Leave --}}
    <div class="lv-dash-card">
        <div>
            <div class="lv-dash-header">
                <span class="lv-dash-title"><i class="fa-solid fa-umbrella-beach text-indigo-500 mr-1.5"></i> Casual Leave</span>
                <span class="lv-dash-badge badge-casual">Max 12 / Year</span>
            </div>
            <div class="lv-dash-avail"><span id="dash_casual_avail">{{ number_format($balanceStats['casual']['available'], 1) }}</span> <span class="text-xs font-semibold text-slate-400">Days Available</span></div>
        </div>
        <div class="lv-dash-subgrid">
            <div class="lv-sub-item">
                <span class="lv-sub-val" id="dash_casual_carry">{{ number_format($balanceStats['casual']['carry_forward'], 1) }}</span>
                <span class="lv-sub-lbl">Carry Fwd</span>
            </div>
            <div class="lv-sub-item">
                <span class="lv-sub-val" id="dash_casual_credit">+{{ number_format($balanceStats['casual']['credit'], 1) }}</span>
                <span class="lv-sub-lbl">Credit</span>
            </div>
            <div class="lv-sub-item">
                <span class="lv-sub-val" id="dash_casual_used" style="color:#dc2626;">{{ number_format($balanceStats['casual']['used'], 1) }}</span>
                <span class="lv-sub-lbl">Used</span>
            </div>
        </div>
    </div>

    {{-- Medical Leave --}}
    <div class="lv-dash-card">
        <div>
            <div class="lv-dash-header">
                <span class="lv-dash-title"><i class="fa-solid fa-notes-medical text-pink-500 mr-1.5"></i> Medical Leave</span>
                <span class="lv-dash-badge badge-medical">Max 12 / Year</span>
            </div>
            <div class="lv-dash-avail"><span id="dash_medical_avail">{{ number_format($balanceStats['medical']['available'], 1) }}</span> <span class="text-xs font-semibold text-slate-400">Days Available</span></div>
        </div>
        <div class="lv-dash-subgrid">
            <div class="lv-sub-item">
                <span class="lv-sub-val" id="dash_medical_carry">{{ number_format($balanceStats['medical']['carry_forward'], 1) }}</span>
                <span class="lv-sub-lbl">Carry Fwd</span>
            </div>
            <div class="lv-sub-item">
                <span class="lv-sub-val" id="dash_medical_credit">+{{ number_format($balanceStats['medical']['credit'], 1) }}</span>
                <span class="lv-sub-lbl">Credit</span>
            </div>
            <div class="lv-sub-item">
                <span class="lv-sub-val" id="dash_medical_used" style="color:#dc2626;">{{ number_format($balanceStats['medical']['used'], 1) }}</span>
                <span class="lv-sub-lbl">Used</span>
            </div>
        </div>
    </div>

    {{-- Loss of Pay (LOP) --}}
    <div class="lv-dash-card">
        <div>
            <div class="lv-dash-header">
                <span class="lv-dash-title"><i class="fa-solid fa-file-invoice-dollar text-amber-500 mr-1.5"></i> Loss of Pay (LOP)</span>
                <span class="lv-dash-badge badge-lop">Unlimited</span>
            </div>
            <div class="lv-dash-avail">∞ <span class="text-xs font-semibold text-slate-400">Available</span></div>
        </div>
        <div class="lv-dash-subgrid" style="grid-template-columns:1fr 1fr;">
            <div class="lv-sub-item">
                <span class="lv-sub-val" style="color:#dc2626;">{{ number_format($balanceStats['lop']['used'], 1) }}</span>
                <span class="lv-sub-lbl">LOP Days Taken</span>
            </div>
            <div class="lv-sub-item">
                <span class="lv-sub-val" style="color:#16a34a;">Active</span>
                <span class="lv-sub-lbl">Status</span>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Apply Form (Requirements #1 & #2 & #7 & #8 & #11) --}}
    <div class="lv-form-card">
        <h3 class="font-bold text-slate-800 text-sm mb-4 border-b border-slate-100 pb-3 flex items-center justify-between">
            <span><i class="fa-solid fa-paper-plane text-indigo-500 mr-1.5"></i> Apply for Leave</span>
            <span class="text-[11px] font-normal text-slate-400">Step 1: Choose Dates</span>
        </h3>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold rounded-lg px-4 py-3 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i>{{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-lg px-4 py-3 mb-4">
                @foreach($errors->all() as $e)<div class="flex items-center gap-1"><i class="fa-solid fa-circle-xmark"></i>{{ $e }}</div>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('employee.leaves.store') }}" id="leaveApplicationForm">
            @csrf
            <div class="flex flex-col gap-4">
                {{-- DURATION TOGGLE: Full Day / Half Day --}}
                <div class="lv-form-group">
                    <label class="lv-form-label">Leave Duration *</label>
                    <div class="flex items-center gap-4 bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                        <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-semibold text-slate-700">
                            <input type="radio" name="leave_duration_radio" value="full" checked class="accent-indigo-600"> Full Day
                        </label>
                        <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-semibold text-slate-700">
                            <input type="radio" name="leave_duration_radio" value="half" class="accent-indigo-600"> Half Day (0.5 Day)
                        </label>
                    </div>
                    <input type="hidden" name="is_half_day" id="is_half_day" value="0">
                </div>

                <div class="lv-form-group" id="half_day_type_group" style="display: none;">
                    <label class="lv-form-label">Half Day Session *</label>
                    <select name="half_day_type" id="half_day_type" class="lv-form-input">
                        <option value="First Half">First Half (Morning)</option>
                        <option value="Second Half">Second Half (Afternoon)</option>
                    </select>
                </div>

                {{-- STEP 1: DATE SELECTION FIRST --}}
                <div class="lv-form-grid">
                    <div class="lv-form-group">
                        <label class="lv-form-label" id="start_date_label">From Date *</label>
                        <input type="text" name="start_date" id="start_date" class="lv-form-input datepicker"
                               value="{{ old('start_date') }}" required autocomplete="off" placeholder="YYYY-MM-DD">
                    </div>
                    <div class="lv-form-group" id="to_date_group">
                        <label class="lv-form-label">To Date *</label>
                        <input type="text" name="end_date" id="end_date" class="lv-form-input datepicker"
                               value="{{ old('end_date') }}" required autocomplete="off" placeholder="YYYY-MM-DD">
                    </div>
                </div>

                {{-- Date Calculation Loader --}}
                <div id="calc_loading" class="hidden text-xs text-indigo-600 font-semibold items-center justify-center gap-2 py-2">
                    <i class="fa-solid fa-spinner fa-spin"></i> Calculating leave balance eligibility...
                </div>

                {{-- Month-wise allocation breakdown container --}}
                <div id="allocation_preview" class="hidden allocation-box">
                    <div class="bg-indigo-50 border border-indigo-200 text-indigo-800 text-xs font-bold rounded-lg px-3 py-2 mb-2 flex items-center justify-between">
                        <span><i class="fa-solid fa-calendar-check text-indigo-500 mr-1"></i> Balance for <strong id="target_month_banner">--</strong>:</span>
                        <span id="target_balance_summary" class="text-indigo-700"></span>
                    </div>
                    <div class="text-xs font-bold text-slate-700 mb-2 border-b border-slate-200 pb-1 flex justify-between">
                        <span><i class="fa-solid fa-calendar-week text-indigo-500 mr-1"></i> Allocation Breakdown</span>
                        <span id="preview_total_days" class="text-indigo-600 font-extrabold"></span>
                    </div>
                    <div id="segment_details_list"></div>
                </div>

                {{-- STEP 2: LEAVE TYPE & REASON (Disabled until dates are selected) --}}
                <div class="lv-form-group">
                    <label class="lv-form-label">Leave Type *</label>
                    <select name="leave_type_id" id="leave_type_id" class="lv-form-input" required disabled>
                        <option value="">-- Select Leave Dates First --</option>
                    </select>
                </div>

                <div class="lv-form-group">
                    <label class="lv-form-label">Reason</label>
                    <textarea name="reason" id="reason" class="lv-form-textarea" disabled
                              placeholder="Briefly describe the reason for your leave request...">{{ old('reason') }}</textarea>
                </div>

                <button type="submit" id="submit_btn" class="lv-submit-btn" disabled>
                    <i class="fa-solid fa-paper-plane"></i> Submit Request
                </button>
            </div>
        </form>
    </div>

    {{-- Leave History --}}
    <div class="xl:col-span-2 bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="flex justify-between items-center px-5 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-sm">
                <i class="fa-solid fa-clock-rotate-left text-indigo-500 mr-1"></i> Leave Request History
            </h3>
            <span class="text-xs text-slate-400">{{ $requests->count() }} total requests</span>
        </div>

        @if($requests->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="lv-table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Days</th>
                        <th>Reason</th>
                        <th>Applied On</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $req)
                    <tr>
                        <td class="font-semibold">{{ $req->leaveType->name ?? '—' }}</td>
                        <td class="font-semibold">{{ \Carbon\Carbon::parse($req->start_date)->format('d-m-Y') }}</td>
                        <td class="font-semibold">{{ \Carbon\Carbon::parse($req->end_date)->format('d-m-Y') }}</td>
                        <td class="font-bold text-indigo-600">
                            @if($req->is_half_day)
                                0.5 Days <span class="bg-amber-100 text-amber-800 text-[10px] px-1.5 py-0.5 rounded font-bold inline-block">{{ $req->half_day_type ?? 'Half Day' }}</span>
                            @else
                                {{ $req->total_days }} Days
                            @endif
                            <span class="text-[10px] text-slate-400 font-normal block">({{ $req->paid_days }} Paid / {{ $req->lop_days }} LOP)</span>
                        </td>
                        <td class="text-slate-500 max-w-[200px] truncate">{{ $req->reason ?? '—' }}</td>
                        <td class="text-slate-400">{{ $req->created_at->format('d-m-Y') }}</td>
                        <td>
                            <span class="lv-pill
                                @if($req->status === 'Approved') pill-approved
                                @elseif($req->status === 'Rejected') pill-rejected
                                @else pill-pending @endif">
                                @if($req->status === 'Approved')<i class="fa-solid fa-circle-check"></i>
                                @elseif($req->status === 'Rejected')<i class="fa-solid fa-circle-xmark"></i>
                                @else<i class="fa-solid fa-clock"></i>@endif
                                {{ str_replace('_', ' ', $req->status) }}
                            </span>
                        </td>
                        <td>
                            @if($req->status === 'TL Approved' && optional($req->leaveType)->name === 'Medical' && !$req->medical_certificate_path)
                                <form action="{{ route('employee.leaves.certificate.upload', $req->id) }}" method="POST" enctype="multipart/form-data" class="mt-1">
                                    @csrf
                                    <label class="cursor-pointer inline-flex items-center gap-1.5 bg-white border border-indigo-200 hover:bg-indigo-50 text-indigo-600 font-semibold text-[10px] px-2.5 py-1 rounded-lg shadow-sm whitespace-nowrap">
                                        <i class="fa-solid fa-cloud-arrow-up text-indigo-400"></i> Upload Cert
                                        <input type="file" name="certificate" required class="hidden" accept=".pdf,.jpg,.jpeg,.png" onchange="this.form.submit()">
                                    </label>
                                </form>
                            @elseif($req->medical_certificate_path)
                                <a href="{{ Storage::url($req->medical_certificate_path) }}" target="_blank" class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-600 border border-emerald-200 font-semibold text-[10px] px-2.5 py-1 rounded-lg shadow-sm whitespace-nowrap">
                                    <i class="fa-solid fa-file-medical"></i> View Cert
                                </a>
                            @else
                                <span class="text-slate-300 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <div class="text-center py-14 text-slate-400">
                <i class="fa-regular fa-calendar-xmark text-4xl mb-3 block"></i>
                <p class="text-sm font-semibold">No leave requests yet.</p>
                <p class="text-xs mt-1">Select dates above to apply for leave.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle Half Day / Full Day
        $('input[name="leave_duration_radio"]').on('change', function() {
            var val = $(this).val();
            if (val === 'half') {
                $('#is_half_day').val('1');
                $('#half_day_type_group').css('display', 'flex');
                $('#half_day_type').prop('required', true);
                $('#to_date_group').hide();
                $('#start_date_label').text('Date *');
                var sDate = $('#start_date').val();
                if (sDate) {
                    $('#end_date').val(sDate);
                }
            } else {
                $('#is_half_day').val('0');
                $('#half_day_type_group').hide();
                $('#half_day_type').prop('required', false);
                $('#to_date_group').css('display', 'flex');
                $('#start_date_label').text('From Date *');
            }
            onDatesChanged();
        });

        // Initialize Datepickers (System start date: 2026-08-01)
        var systemMinDate = "2026-08-01";
        $("#start_date").datepicker({
            dateFormat: "yy-mm-dd",
            minDate: systemMinDate,
            onSelect: function(selectedDate) {
                if ($('#is_half_day').val() === '1') {
                    $("#end_date").val(selectedDate);
                } else {
                    $("#end_date").datepicker("option", "minDate", selectedDate);
                    var endDateVal = $("#end_date").val();
                    if (endDateVal && endDateVal < selectedDate) {
                        $("#end_date").val("");
                    }
                }
                onDatesChanged();
            }
        });

        $("#end_date").datepicker({
            dateFormat: "yy-mm-dd",
            minDate: systemMinDate,
            onSelect: function() {
                onDatesChanged();
            }
        });

        // Trigger on manual input/change as well
        $("#start_date, #end_date").on("change input", function() {
            if ($('#is_half_day').val() === '1') {
                $("#end_date").val($("#start_date").val());
            }
            onDatesChanged();
        });

        // Date selection listener
        function onDatesChanged() {
            var isHalfDay = $('#is_half_day').val() === '1';
            var startDate = $("#start_date").val();
            if (isHalfDay && startDate) {
                $("#end_date").val(startDate);
            }
            var endDate = $("#end_date").val();

            if (!startDate || (!isHalfDay && !endDate)) {
                disableRemainingFields(isHalfDay ? "Select Date" : "Select both From and To dates");
                $("#allocation_preview").addClass("hidden");
                return;
            }

            if (!isHalfDay && endDate < startDate) {
                disableRemainingFields("To Date cannot be earlier than From Date");
                $("#allocation_preview").addClass("hidden");
                return;
            }

            calculateLeaveEligibility();
        }

        function disableRemainingFields(placeholderText) {
            $("#leave_type_id").html('<option value="">-- ' + placeholderText + ' --</option>').prop("disabled", true);
            $("#reason").prop("disabled", true);
            $("#submit_btn").prop("disabled", true);
        }

        function calculateLeaveEligibility() {
            var isHalfDay = $('#is_half_day').val() === '1';
            var startDate = $("#start_date").val();
            var endDate = isHalfDay ? startDate : $("#end_date").val();
            var selectedTypeId = $("#leave_type_id").val();

            $("#calc_loading").removeClass("hidden").addClass("flex");

            $.ajax({
                url: "{{ route('employee.leaves.calculate') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    start_date: startDate,
                    end_date: endDate,
                    is_half_day: isHalfDay ? 1 : 0,
                    leave_type_id: selectedTypeId
                },
                success: function(res) {
                    $("#calc_loading").addClass("hidden").removeClass("flex");

                    if (res.success && res.allocation) {
                        var alloc = res.allocation;
                        var targetMonthName = alloc.primary_month_name || "Target Month";

                        // Update target month banner
                        $("#dash_month_name").text(targetMonthName);
                        $("#target_month_banner").text(targetMonthName);
                        $("#target_balance_summary").text("Casual: " + alloc.primary_available_cl + " Days | Medical: " + alloc.primary_available_ml + " Days");

                        // Update Dashboard Cards dynamically to show target month balance
                        $("#dash_casual_avail").text(alloc.primary_available_cl);
                        $("#dash_medical_avail").text(alloc.primary_available_ml);

                        // Requirement #7: Populate available leave types (do not display zero balance types)
                        var typeOptions = '<option value="">-- Select Leave Type --</option>';

                        alloc.available_leave_types.forEach(function(type) {
                            var availStr = type.available_days === 'Unlimited' ? '∞' : type.available_days + ' Days Available in ' + targetMonthName;
                            var isSel = (selectedTypeId && selectedTypeId == type.id) ? 'selected' : '';

                            typeOptions += '<option value="' + type.id + '" ' + isSel + '>' +
                                            type.name + ' (' + availStr + ')' +
                                           '</option>';
                        });

                        $("#leave_type_id").html(typeOptions).prop("disabled", false);
                        $("#reason").prop("disabled", false);

                        // Requirement #8 & #11: Display month-wise allocation breakdown
                        $("#preview_total_days").text(alloc.total_days + " Total Days (" + alloc.paid_days + " Paid, " + alloc.lop_days + " LOP)");

                        var detailsHtml = '';
                        alloc.month_segments.forEach(function(seg) {
                            detailsHtml += '<div class="allocation-item">' +
                                '<div>' +
                                    '<span class="font-bold text-slate-800">' + seg.month_name + '</span> ' +
                                    '<span class="text-xs text-slate-400">(' + seg.days + ' Days)</span>' +
                                '</div>' +
                                '<div class="text-right text-xs font-semibold text-slate-600">' +
                                    (seg.casual_allocated > 0 ? '<span class="text-indigo-600 mr-2">' + seg.casual_allocated + ' Casual</span>' : '') +
                                    (seg.medical_allocated > 0 ? '<span class="text-pink-600 mr-2">' + seg.medical_allocated + ' Medical</span>' : '') +
                                    (seg.lop_allocated > 0 ? '<span class="text-amber-600">' + seg.lop_allocated + ' LOP</span>' : '') +
                                '</div>' +
                            '</div>';
                        });

                        $("#segment_details_list").html(detailsHtml);
                        $("#allocation_preview").removeClass("hidden");

                        // Enable submit button if a leave type is selected
                        if ($("#leave_type_id").val()) {
                            $("#submit_btn").prop("disabled", false);
                        } else {
                            $("#submit_btn").prop("disabled", true);
                        }
                    }
                },
                error: function(err) {
                    $("#calc_loading").addClass("hidden").removeClass("flex");
                    var errMsg = (err.responseJSON && err.responseJSON.message) ? err.responseJSON.message : "Error calculating leave eligibility. Please verify your selected dates.";
                    console.error("Leave eligibility calculation error:", err);
                    disableRemainingFields(errMsg);
                    $("#allocation_preview").addClass("hidden");
                }
            });
        }

        // When employee changes Leave Type, update allocation preview
        $("#leave_type_id").on("change", function() {
            if ($(this).val()) {
                $("#submit_btn").prop("disabled", false);
                calculateLeaveEligibility();
            } else {
                $("#submit_btn").prop("disabled", true);
            }
        });
    });
</script>
@endpush
@endsection
