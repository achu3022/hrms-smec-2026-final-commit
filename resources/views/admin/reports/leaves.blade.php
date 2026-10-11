@extends('layouts.admin')

@section('content')
<style>
    .report-card { border: none; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); background: white; }
    .table-custom th { background-color: #f8fafc; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; padding: 16px; }
    .table-custom td { padding: 16px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
    .table-custom tbody tr:hover { background-color: #f8fafc; }
    .avatar-circle { width: 40px; height: 40px; border-radius: 50%; background-color: #e0e7ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; }
    .badge-soft-indigo { background-color: #e0e7ff; color: #4f46e5; border: 1px solid #c7d2fe; padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; }
    .badge-soft-success { background-color: #dcfce7; color: #16a34a; padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 0.75rem; }
    .badge-soft-danger { background-color: #fee2e2; color: #dc2626; padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 0.75rem; }
    .date-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px 12px; font-size: 0.85rem; font-weight: 500; color: #334155; display: inline-block; }
    .form-label-custom { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
    .form-select-custom { border-radius: 10px; border: 1px solid #cbd5e1; padding: 10px 15px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .btn-filter { background-color: #4f46e5; color: white; border-radius: 10px; padding: 10px 20px; font-weight: 600; transition: all 0.2s; border: none; height: 46px; }
    .btn-filter:hover { background-color: #4338ca; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2); }
</style>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-4">
    <div>
        <h2 class="h4 mb-1 fw-bold" style="color: #1e293b;">Leave Reports</h2>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Approved leaves for the selected month</p>
    </div>
</div>

<div class="card report-card mb-4">
    <div class="card-body p-3 p-md-4">
        <form action="{{ route('admin.reports.leaves') }}" method="GET" class="row g-2 g-md-3 align-items-end">
            <div class="col-12 col-md-5">
                <label class="form-label-custom d-block">Employee</label>
                <select name="employee_id" class="form-select form-select-custom w-100">
                    <option value="all" {{ $employeeId === 'all' ? 'selected' : '' }}>All Employees</option>
                    @foreach($allEmployees as $emp)
                        <option value="{{ $emp->id }}" {{ $employeeId == $emp->id ? 'selected' : '' }}>
                            {{ $emp->name }} ({{ $emp->employee_id }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label-custom d-block">Month</label>
                <select name="month" class="form-select form-select-custom w-100">
                    @for($i=1; $i<=12; $i++)
                        <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                            {{ date('M', mktime(0, 0, 0, $i, 1)) }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label-custom d-block">Year</label>
                <select name="year" class="form-select form-select-custom w-100">
                    @for($i=now()->year - 2; $i<=now()->year; $i++)
                        <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-12 col-md-2 mt-3 mt-md-0">
                <button type="submit" class="btn btn-filter w-100 d-flex justify-content-center align-items-center gap-2">
                    <i class="fa-solid fa-filter"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card report-card overflow-hidden">
    <!-- Desktop Table View -->
    <div class="table-responsive d-none d-md-block">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>Employee Info</th>
                    <th class="text-center">Leave Type</th>
                    <th class="text-center">Duration</th>
                    <th class="text-end">Days</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalPaid = 0;
                    $totalLop = 0;
                @endphp
                @forelse($leaves as $leave)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-circle">
                                {{ substr($leave->employee->name ?? 'E', 0, 1) }}
                            </div>
                            <div>
                                <div class="fw-bold" style="color: #1e293b;">{{ $leave->employee->name ?? 'Unknown' }}</div>
                                <div class="text-muted" style="font-size: 0.8rem;">{{ $leave->employee->employee_id ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="d-flex flex-column align-items-center justify-content-center gap-2">
                            <span class="badge-soft-indigo d-inline-flex align-items-center gap-2">
                                <i class="fa-solid fa-tag text-xs"></i> {{ $leave->leaveType ? $leave->leaveType->name : 'N/A' }}
                            </span>
                            @if($leave->medical_certificate_path)
                            <a href="{{ Storage::url($leave->medical_certificate_path) }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.75rem; border-radius: 6px;">
                                <i class="fa-solid fa-paperclip"></i> View Cert
                            </a>
                            @endif
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="date-box">
                            {{ \Carbon\Carbon::parse($leave->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}
                        </div>
                    </td>
                    <td class="text-end">
                        <div class="d-flex flex-column align-items-end gap-1">
                            <div><span class="badge-soft-success">{{ $leave->paid_days }}</span> <span class="fw-medium text-success" style="font-size: 0.85rem;">Paid</span></div>
                            @if($leave->lop_days > 0)
                            <div><span class="badge-soft-danger">{{ $leave->lop_days }}</span> <span class="fw-medium text-danger" style="font-size: 0.85rem;">LOP</span></div>
                            @endif
                        </div>
                        @php
                            $totalPaid += $leave->paid_days;
                            $totalLop += $leave->lop_days;
                        @endphp
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-5">
                        <div style="width: 70px; height: 70px; border-radius: 50%; background: #e0e7ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                            <i class="fa-solid fa-file-invoice fa-2x"></i>
                        </div>
                        <h5 class="fw-bold" style="color: #1e293b;">No Approved Leaves</h5>
                        <p class="text-muted">There are no approved leave requests for the selected filters.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($leaves->count() > 0)
            <tfoot style="background-color: #f8fafc; border-top: 2px solid #e2e8f0;">
                <tr>
                    <td colspan="3" class="text-end fw-bold text-uppercase" style="color: #64748b; font-size: 0.8rem; letter-spacing: 0.5px; padding: 20px;">Totals for Period:</td>
                    <td class="text-end" style="padding: 20px;">
                        <div class="d-flex flex-column align-items-end gap-2">
                            <div class="fw-bold text-success" style="font-size: 0.95rem;">
                                <span style="background: #dcfce7; padding: 2px 8px; border-radius: 6px; margin-right: 5px;">{{ $totalPaid }}</span> Paid Days
                            </div>
                            <div class="fw-bold text-danger" style="font-size: 0.95rem;">
                                <span style="background: #fee2e2; padding: 2px 8px; border-radius: 6px; margin-right: 5px;">{{ $totalLop }}</span> LOP Days
                            </div>
                        </div>
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="d-block d-md-none p-3 bg-light">
        @php
            $mobilePaid = 0;
            $mobileLop = 0;
        @endphp
        @forelse($leaves as $leave)
            @php
                $mobilePaid += $leave->paid_days;
                $mobileLop += $leave->lop_days;
            @endphp
            <div class="bg-white rounded-3 border p-3 mb-3 shadow-sm">
                <div class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar-circle" style="width: 34px; height: 34px; font-size: 0.85rem;">
                            {{ substr($leave->employee->name ?? 'E', 0, 1) }}
                        </div>
                        <div>
                            <div class="fw-bold text-dark fs-6">{{ $leave->employee->name ?? 'Unknown' }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">{{ $leave->employee->employee_id ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <span class="badge-soft-indigo" style="font-size: 0.75rem; padding: 4px 8px;">
                        <i class="fa-solid fa-tag text-xs me-1"></i> {{ $leave->leaveType ? $leave->leaveType->name : 'N/A' }}
                    </span>
                </div>

                <div class="row g-2 text-sm" style="font-size: 0.825rem;">
                    <div class="col-12">
                        <span class="text-muted d-block" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 600;">Duration</span>
                        <div class="date-box py-1 px-2 mt-1 w-100 text-center fw-medium">
                            <i class="fa-regular fa-calendar me-1 text-primary"></i>
                            {{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}
                        </div>
                    </div>

                    <div class="col-12 mt-2 d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="text-muted" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Days Recorded:</span>
                        <div class="d-flex gap-2">
                            <span class="badge-soft-success"><i class="fa-solid fa-check me-1"></i>{{ $leave->paid_days }} Paid</span>
                            @if($leave->lop_days > 0)
                            <span class="badge-soft-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i>{{ $leave->lop_days }} LOP</span>
                            @endif
                        </div>
                    </div>

                    @if($leave->medical_certificate_path)
                    <div class="col-12 mt-2">
                        <a href="{{ Storage::url($leave->medical_certificate_path) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100 py-1" style="font-size: 0.75rem; border-radius: 6px;">
                            <i class="fa-solid fa-paperclip me-1"></i> View Medical Certificate
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-4 bg-white rounded-3 border">
                <i class="fa-solid fa-file-invoice text-muted mb-2" style="font-size: 2rem;"></i>
                <h6 class="fw-bold text-dark">No Approved Leaves</h6>
                <p class="text-muted small mb-0">There are no approved leave requests for the selected filters.</p>
            </div>
        @endforelse

        @if($leaves->count() > 0)
        <div class="bg-white rounded-3 border p-3 mt-3 shadow-sm">
            <div class="fw-bold text-uppercase text-secondary mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Totals for Period</div>
            <div class="d-flex justify-content-around text-center pt-1">
                <div>
                    <div class="fw-bold text-success fs-5">{{ $mobilePaid }}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">Total Paid Days</div>
                </div>
                <div class="border-end"></div>
                <div>
                    <div class="fw-bold text-danger fs-5">{{ $mobileLop }}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">Total LOP Days</div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
