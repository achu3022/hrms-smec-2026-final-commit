@extends('layouts.admin')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { background-color: #f8f9fc; }
    .lm-font { font-family: 'Inter', sans-serif; }
    
    /* Summary Cards */
    .lm-summary-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        height: 100%;
    }
    .lm-summary-title { font-size: 0.75rem; color: #64748b; font-weight: 600; margin-bottom: 4px; }
    .lm-summary-value { font-size: 1.35rem; font-weight: 700; color: #1e293b; }
    .lm-summary-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
    .icon-total { background: #f1f5f9; color: #64748b; }
    .icon-pending { background: #fef3c7; color: #d97706; }
    .icon-tl-approved { background: #e0e7ff; color: #4338ca; }
    .icon-approved { background: #dcfce7; color: #16a34a; }
    .icon-rejected { background: #fee2e2; color: #dc2626; }
    .icon-today { background: #e0e7ff; color: #4f46e5; }
    .text-pending { color: #d97706 !important; }
    .text-tl-approved { color: #4338ca !important; }
    .text-approved { color: #16a34a !important; }
    .text-rejected { color: #dc2626 !important; }
    .text-today { color: #4f46e5 !important; }

    /* Filters Section */
    .lm-filter-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 20px;
        margin-top: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .lm-filter-label { font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 6px; display: block; }
    .lm-filter-select, .lm-filter-input {
        border-radius: 8px; border: 1px solid #cbd5e1; padding: 9px 12px; font-size: 0.85rem; width: 100%;
        color: #334155; font-weight: 500; outline: none; transition: border-color 0.2s;
    }
    .lm-filter-select:focus, .lm-filter-input:focus { border-color: #4f46e5; }

    /* Table Section */
    .lm-table-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        margin-top: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        overflow: hidden;
    }
    .lm-table-header { padding: 18px 20px; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #1e293b; font-size: 1.05rem; }
    .lm-table { width: 100%; border-collapse: collapse; }
    .lm-table th { background: #f8fafc; padding: 12px 18px; text-align: left; font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; }
    .lm-table td { padding: 14px 18px; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; color: #334155; vertical-align: middle; }
    .lm-table tr:last-child td { border-bottom: none; }
    
    .status-pill { padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; display: inline-block; white-space: nowrap; }
    .pill-pending { background: #fef3c7; color: #b45309; }
    .pill-tl-approved { background: #e0e7ff; color: #4338ca; }
    .pill-hr-approved { background: #e0f2fe; color: #0284c7; }
    .pill-cert-uploaded { background: #dbeafe; color: #1d4ed8; }
    .pill-approved { background: #dcfce7; color: #15803d; }
    .pill-rejected { background: #fee2e2; color: #b91c1c; }

    .btn-action { padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: 0.2s; color: white; text-decoration: none; min-height: 32px; }
    .btn-approve { background: #16a34a; }
    .btn-approve:hover { background: #15803d; }
    .btn-reject { background: #dc2626; }
    .btn-reject:hover { background: #b91c1c; }

    .btn-primary-custom { background: #4f46e5; color: white; border-radius: 8px; padding: 10px 18px; font-weight: 600; border: none; font-size: 0.85rem; transition: background 0.2s; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
    .btn-primary-custom:hover { background: #4338ca; color: white; }
    .btn-outline-custom { background: white; color: #4f46e5; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 18px; font-weight: 600; font-size: 0.85rem; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
    .btn-outline-custom:hover { background: #f8fafc; border-color: #4f46e5; color: #4f46e5; }

    /* Mobile Responsive Optimizations */
    @media (max-width: 575.98px) {
        .lm-summary-card { padding: 12px 10px; border-radius: 10px; }
        .lm-summary-title { font-size: 0.68rem; margin-bottom: 2px; }
        .lm-summary-value { font-size: 1.15rem; }
        .lm-summary-icon { width: 34px; height: 34px; border-radius: 8px; font-size: 0.95rem; }
        .lm-filter-card { padding: 14px; margin-top: 14px; }
    }
</style>

<div class="lm-font pb-5">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1" style="font-size: 1.35rem;">Leave Management</h2>
            <p class="text-muted small mb-0">Manage employee leave requests and approvals</p>
        </div>
        <div class="w-100 w-sm-auto text-end">
            <form action="{{ route('admin.leave.index') }}" method="GET" class="d-block d-sm-inline">
                <input type="hidden" name="status" value="Pending">
                <button type="submit" class="btn-primary-custom w-100 w-sm-auto">
                    <i class="fa-regular fa-clock me-1"></i> Pending Approvals
                </button>
            </form>
        </div>
    </div>

    <!-- Summary Cards Grid -->
    <div class="row g-2 g-md-3 row-cols-2 row-cols-sm-3 row-cols-xl-6">
        <div class="col">
            <div class="lm-summary-card">
                <div class="me-2">
                    <div class="lm-summary-title">Total Requests</div>
                    <div class="lm-summary-value">{{ $stats['total'] }}</div>
                </div>
                <div class="lm-summary-icon icon-total"><i class="fa-regular fa-file-lines"></i></div>
            </div>
        </div>
        <div class="col">
            <div class="lm-summary-card">
                <div class="me-2">
                    <div class="lm-summary-title">Pending</div>
                    <div class="lm-summary-value text-pending">{{ $stats['pending'] }}</div>
                </div>
                <div class="lm-summary-icon icon-pending"><i class="fa-regular fa-clock"></i></div>
            </div>
        </div>
        <div class="col">
            <div class="lm-summary-card">
                <div class="me-2">
                    <div class="lm-summary-title">TL Approved</div>
                    <div class="lm-summary-value text-tl-approved">{{ $stats['tl_approved'] }}</div>
                </div>
                <div class="lm-summary-icon icon-tl-approved"><i class="fa-solid fa-user-check"></i></div>
            </div>
        </div>
        <div class="col">
            <div class="lm-summary-card">
                <div class="me-2">
                    <div class="lm-summary-title">Approved</div>
                    <div class="lm-summary-value text-approved">{{ $stats['approved'] }}</div>
                </div>
                <div class="lm-summary-icon icon-approved"><i class="fa-regular fa-circle-check"></i></div>
            </div>
        </div>
        <div class="col">
            <div class="lm-summary-card">
                <div class="me-2">
                    <div class="lm-summary-title">Rejected</div>
                    <div class="lm-summary-value text-rejected">{{ $stats['rejected'] }}</div>
                </div>
                <div class="lm-summary-icon icon-rejected"><i class="fa-regular fa-circle-xmark"></i></div>
            </div>
        </div>
        <div class="col">
            <div class="lm-summary-card">
                <div class="me-2">
                    <div class="lm-summary-title">On Leave Today</div>
                    <div class="lm-summary-value text-today">{{ $stats['on_leave_today'] }}</div>
                </div>
                <div class="lm-summary-icon icon-today"><i class="fa-solid fa-user-group"></i></div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="lm-filter-card">
        <form method="GET" action="{{ route('admin.leave.index') }}" id="filterForm">
            <div class="row g-2 g-md-3 align-items-end">
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <label class="lm-filter-label">Date Filter</label>
                    <select name="date_filter_type" id="dateFilterType" class="lm-filter-select">
                        <option value="year" {{ request('date_filter_type') == 'year' ? 'selected' : '' }}>Yearly</option>
                        <option value="month" {{ request('date_filter_type') == 'month' ? 'selected' : '' }}>Monthly</option>
                        <option value="custom" {{ request('date_filter_type') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-4 col-lg-2" id="yearDiv">
                    <label class="lm-filter-label">Year</label>
                    <select name="year" class="lm-filter-select">
                        @for($y = date('Y'); $y >= date('Y')-3; $y--)
                            <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-4 col-lg-2" id="monthDiv" style="display: none;">
                    <label class="lm-filter-label">Month</label>
                    <select name="month" class="lm-filter-select">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ sprintf('%02d', $m) }}" {{ request('month', date('m')) == sprintf('%02d', $m) ? 'selected' : '' }}>
                                {{ date('F', mktime(0,0,0,$m,1)) }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-4 col-lg-2" id="startDiv" style="display: none;">
                    <label class="lm-filter-label">Start Date</label>
                    <input type="date" name="start_date" class="lm-filter-input" value="{{ request('start_date') }}">
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2" id="endDiv" style="display: none;">
                    <label class="lm-filter-label">End Date</label>
                    <input type="date" name="end_date" class="lm-filter-input" value="{{ request('end_date') }}">
                </div>

                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <label class="lm-filter-label">Department</label>
                    <select name="department_id" class="lm-filter-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <label class="lm-filter-label">Employee</label>
                    <select name="employee_id" class="lm-filter-select">
                        <option value="">All Employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <label class="lm-filter-label">Status</label>
                    <select name="status" class="lm-filter-select">
                        <option value="">All Status</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="TL Approved" {{ request('status') == 'TL Approved' ? 'selected' : '' }}>TL Approved</option>
                        <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="col-12 d-flex flex-column flex-sm-row justify-content-sm-end gap-2 mt-3 pt-3 border-top">
                    @if(request()->anyFilled(['date_filter_type', 'department_id', 'employee_id', 'status', 'clear_filters']))
                        <a href="{{ route('admin.leave.index', ['clear_filters' => 1]) }}" class="btn-outline-custom text-center text-decoration-none w-100 w-sm-auto">Clear</a>
                    @endif
                    <button type="button" class="btn-outline-custom w-100 w-sm-auto d-flex align-items-center justify-content-center" onclick="exportExcel()"><i class="fa-solid fa-file-excel me-2"></i>Export to Excel</button>
                    <button type="submit" class="btn-primary-custom w-100 w-sm-auto">Apply Filters</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Card Section -->
    <div class="lm-table-card">
        <div class="lm-table-header d-flex justify-content-between align-items-center">
            <span>Leave Requests</span>
            @if(!$leaves->isEmpty())
                <span class="badge bg-light text-secondary border fw-normal" style="font-size: 0.75rem;">{{ $leaves->count() }} records</span>
            @endif
        </div>
        @if($leaves->isEmpty())
            <div class="text-center py-5 px-3">
                <i class="fa-regular fa-folder-open text-muted mb-3" style="font-size: 2.5rem;"></i>
                <h6 class="fw-bold text-dark">No records found</h6>
                <p class="text-muted small mb-0">Try adjusting your filters.</p>
            </div>
        @else
            <!-- Desktop Table View -->
            <div class="table-responsive d-none d-md-block">
                <table class="lm-table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Leave Type</th>
                            <th>Duration</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <th>Applied On</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaves as $leave)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $leave->employee->name ?? 'Unknown' }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $leave->employee->employee_id ?? '' }} • {{ $leave->employee->departmentRel->name ?? 'General' }}</div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $leave->leaveType->name ?? '—' }}</div>
                                    @if($leave->medical_certificate_path)
                                        <div class="mt-1">
                                            <a href="{{ Storage::url($leave->medical_certificate_path) }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.75rem; border-radius: 6px;">
                                                <i class="fa-solid fa-paperclip"></i> View Cert
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ \Carbon\Carbon::parse($leave->start_date)->format('d-m-Y') }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">to {{ \Carbon\Carbon::parse($leave->end_date)->format('d-m-Y') }}</div>
                                </td>
                                <td>
                                    @if($leave->is_half_day)
                                        <span class="badge bg-warning text-dark px-2 py-1">0.5 Day ({{ $leave->half_day_type ?? 'Half-Day' }})</span>
                                    @else
                                        <span class="fw-bold text-dark">{{ $leave->total_days }} Days</span>
                                    @endif
                                    <div class="text-muted small">({{ $leave->paid_days }}P / {{ $leave->lop_days }}L)</div>
                                </td>
                                <td>
                                    @php
                                        $pillClass = match($leave->status) {
                                            'TL Approved' => 'pill-tl-approved',
                                            'HR_Approved' => 'pill-hr-approved',
                                            'Certificate_Uploaded' => 'pill-cert-uploaded',
                                            'Approved' => 'pill-approved',
                                            'Rejected' => 'pill-rejected',
                                            default => 'pill-pending'
                                        };
                                    @endphp
                                    <span class="status-pill {{ $pillClass }}">{{ str_replace('_', ' ', $leave->status) }}</span>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $leave->reason }}">
                                        {{ $leave->reason ?: '—' }}
                                    </div>
                                </td>
                                <td class="text-muted">{{ $leave->created_at->format('d-m-Y') }}</td>
                                <td class="text-end whitespace-nowrap">
                                    @if(in_array($leave->status, ['Pending', 'TL Approved', 'Certificate_Uploaded']))
                                        <form action="{{ route('admin.leave.approve', $leave->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @if($leave->status === 'TL Approved' && optional($leave->leaveType)->name === 'Medical' && !$leave->medical_certificate_path)
                                                <button type="button" class="btn-action bg-secondary" disabled title="Waiting for Medical Certificate">
                                                    <i class="fa-solid fa-clock"></i> Cert Pending
                                                </button>
                                            @else
                                                <button class="btn-action btn-approve">
                                                    <i class="fa-solid fa-check"></i> Approve
                                                </button>
                                            @endif
                                        </form>
                                        <form action="{{ route('admin.leave.reject', $leave->id) }}" method="POST" class="d-inline ms-1">
                                            @csrf
                                            <button class="btn-action btn-reject"><i class="fa-solid fa-xmark"></i> Reject</button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.leave.destroy', $leave->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this record?');">
                                            @csrf @method('DELETE')
                                            <button class="btn-action bg-light text-secondary border"><i class="fa-solid fa-trash-can"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="d-block d-md-none p-2 p-sm-3 bg-light">
                @foreach($leaves as $leave)
                    @php
                        $pillClass = match($leave->status) {
                            'TL Approved' => 'pill-tl-approved',
                            'HR_Approved' => 'pill-hr-approved',
                            'Certificate_Uploaded' => 'pill-cert-uploaded',
                            'Approved' => 'pill-approved',
                            'Rejected' => 'pill-rejected',
                            default => 'pill-pending'
                        };
                    @endphp
                    <div class="bg-white rounded-3 border p-3 mb-3 shadow-sm">
                        <!-- Card Header -->
                        <div class="d-flex justify-content-between align-items-start pb-2 mb-2 border-bottom">
                            <div>
                                <div class="fw-bold text-dark fs-6">{{ $leave->employee->name ?? 'Unknown' }}</div>
                                <div class="text-muted" style="font-size: 0.78rem;">
                                    <span class="badge bg-light text-dark border me-1">{{ $leave->employee->employee_id ?? 'N/A' }}</span>
                                    <span>{{ $leave->employee->departmentRel->name ?? 'General' }}</span>
                                </div>
                            </div>
                            <span class="status-pill {{ $pillClass }}">{{ str_replace('_', ' ', $leave->status) }}</span>
                        </div>

                        <!-- Card Body Grid -->
                        <div class="row g-2 mb-2" style="font-size: 0.825rem;">
                            <div class="col-6">
                                <span class="text-muted d-block" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 600;">Leave Type</span>
                                <span class="fw-semibold text-dark">{{ $leave->leaveType->name ?? '—' }}</span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 600;">Duration</span>
                                @if($leave->is_half_day)
                                    <span class="badge bg-warning text-dark px-1">0.5 Day ({{ $leave->half_day_type ?? 'Half-Day' }})</span>
                                @else
                                    <span class="fw-bold text-dark">{{ $leave->total_days }} Days</span>
                                @endif
                                <span class="text-muted ms-1" style="font-size: 0.75rem;">({{ $leave->paid_days }}P / {{ $leave->lop_days }}L)</span>
                            </div>
                            <div class="col-12 mt-2">
                                <span class="text-muted d-block" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 600;">Dates</span>
                                <span class="fw-medium text-dark bg-light px-2 py-1 rounded d-inline-block border">
                                    <i class="fa-regular fa-calendar me-1 text-primary"></i>
                                    {{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}
                                    <i class="fa-solid fa-arrow-right mx-1 text-muted" style="font-size: 0.7rem;"></i>
                                    {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                                </span>
                            </div>
                            @if($leave->reason)
                            <div class="col-12 mt-2">
                                <span class="text-muted d-block" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 600;">Reason</span>
                                <div class="bg-light p-2 rounded text-secondary border-start border-3 border-primary" style="font-size: 0.8rem; word-break: break-word;">
                                    {{ $leave->reason }}
                                </div>
                            </div>
                            @endif
                            @if($leave->medical_certificate_path)
                            <div class="col-12 mt-2">
                                <a href="{{ Storage::url($leave->medical_certificate_path) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100 py-1" style="font-size: 0.78rem; border-radius: 6px;">
                                    <i class="fa-solid fa-paperclip me-1"></i> View Medical Certificate
                                </a>
                            </div>
                            @endif
                        </div>

                        <!-- Card Footer Actions -->
                        <div class="d-flex justify-content-between align-items-center pt-2 mt-2 border-top">
                            <span class="text-muted" style="font-size: 0.75rem;">
                                <i class="fa-regular fa-clock me-1"></i>Applied: {{ $leave->created_at->format('d M Y') }}
                            </span>
                            <div class="d-flex gap-2">
                                @if(in_array($leave->status, ['Pending', 'TL Approved', 'Certificate_Uploaded']))
                                    <form action="{{ route('admin.leave.approve', $leave->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @if($leave->status === 'TL Approved' && optional($leave->leaveType)->name === 'Medical' && !$leave->medical_certificate_path)
                                            <button type="button" class="btn-action bg-secondary" disabled title="Waiting for Medical Certificate">
                                                <i class="fa-solid fa-clock"></i> Cert Pending
                                            </button>
                                        @else
                                            <button class="btn-action btn-approve px-3 py-2">
                                                <i class="fa-solid fa-check me-1"></i> Approve
                                            </button>
                                        @endif
                                    </form>
                                    <form action="{{ route('admin.leave.reject', $leave->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn-action btn-reject px-3 py-2"><i class="fa-solid fa-xmark me-1"></i> Reject</button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.leave.destroy', $leave->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this record?');">
                                        @csrf @method('DELETE')
                                        <button class="btn-action bg-light text-danger border px-3 py-1"><i class="fa-solid fa-trash-can me-1"></i> Delete</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-4 d-flex justify-content-center pb-3">
                @if($leaves->hasPages())
                    {{ $leaves->links('pagination::bootstrap-5') }}
                @else
                    <p class="text-muted">Total pages: {{ $leaves->lastPage() }} | Total records: {{ $leaves->total() }}</p>
                @endif
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function toggleDateInputs() {
        const type = document.getElementById('dateFilterType').value;
        document.getElementById('yearDiv').style.display = (type === 'year' || type === 'month') ? 'block' : 'none';
        document.getElementById('monthDiv').style.display = (type === 'month') ? 'block' : 'none';
        document.getElementById('startDiv').style.display = (type === 'custom') ? 'block' : 'none';
        document.getElementById('endDiv').style.display = (type === 'custom') ? 'block' : 'none';
    }

    document.getElementById('dateFilterType').addEventListener('change', toggleDateInputs);
    // Init on load
    toggleDateInputs();

    function exportExcel() {
        // Change form action to export route, submit, then change it back
        const form = document.getElementById('filterForm');
        const originalAction = form.action;
        form.action = "{{ route('admin.leave.export') }}";
        form.submit();
        form.action = originalAction;
    }
</script>
@endpush
@endsection
