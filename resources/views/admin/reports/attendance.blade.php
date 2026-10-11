@extends('layouts.admin')

@section('content')
<style>
    .report-card { border: none; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); background: white; }
    .table-custom th { background-color: #f8fafc; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; padding: 16px; }
    .table-custom td { padding: 16px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
    .table-custom tbody tr:hover { background-color: #f8fafc; }
    .avatar-circle { width: 40px; height: 40px; border-radius: 50%; background-color: #e0e7ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-weight: 700; }
    .badge-soft-warning { background-color: #fef3c7; color: #d97706; border: 1px solid #fde68a; padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; }
    .badge-soft-orange { background-color: #ffedd5; color: #ea580c; border: 1px solid #fed7aa; padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; }
    .badge-soft-success { background-color: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; }
    .form-label-custom { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
    .form-select-custom { border-radius: 10px; border: 1px solid #cbd5e1; padding: 10px 15px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .btn-filter { background-color: #4f46e5; color: white; border-radius: 10px; padding: 10px 20px; font-weight: 600; transition: all 0.2s; border: none; height: 46px; }
    .btn-filter:hover { background-color: #4338ca; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2); }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 mb-1 fw-bold" style="color: #1e293b;">Attendance Hours Report</h2>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Total late hours and early going hours</p>
    </div>
</div>

<div class="card report-card mb-4">
    <div class="card-body p-4">
        <form action="{{ route('admin.reports.attendance') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-5 col-12">
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
            <div class="col-md-3 col-6">
                <label class="form-label-custom d-block">Month</label>
                <select name="month" class="form-select form-select-custom w-100">
                    @for($i=1; $i<=12; $i++)
                        <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                            {{ date('M', mktime(0, 0, 0, $i, 1)) }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label-custom d-block">Year</label>
                <select name="year" class="form-select form-select-custom w-100">
                    @for($i=now()->year - 2; $i<=now()->year; $i++)
                        <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2 col-12">
                <button type="submit" class="btn btn-filter w-100 d-flex justify-content-center align-items-center gap-2">
                    <i class="fa-solid fa-filter"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card report-card">
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>Employee Info</th>
                    <th class="text-center">Late Hours</th>
                    <th class="text-center">Early Going Hours</th>
                    <th class="text-center">Approved Requests</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData as $row)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-circle">
                                {{ substr($row['employee']->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="fw-bold" style="color: #1e293b;">{{ $row['employee']->name }}</div>
                                <div class="text-muted" style="font-size: 0.8rem;">{{ $row['employee']->employee_id }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge-soft-warning d-inline-flex align-items-center gap-2">
                            <i class="fa-regular fa-clock"></i> {{ $row['total_late_hours'] }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge-soft-orange d-inline-flex align-items-center gap-2">
                            <i class="fa-solid fa-person-running"></i> {{ $row['total_early_hours'] }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge-soft-success d-inline-flex align-items-center gap-2">
                            <i class="fa-solid fa-check-double"></i> {{ $row['approved_early_goings'] }} Requests
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-5">
                        <div style="width: 70px; height: 70px; border-radius: 50%; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                            <i class="fa-solid fa-award fa-2x"></i>
                        </div>
                        <h5 class="fw-bold" style="color: #1e293b;">Perfect Attendance!</h5>
                        <p class="text-muted">No late arrivals or early goings found for the selected period.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
