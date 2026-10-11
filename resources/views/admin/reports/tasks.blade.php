@extends('layouts.admin')

@section('content')
<div class="container-fluid mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color: var(--brand-dark);">Employee Task Reports</h2>
            <p class="text-muted mb-0">Generate and view consolidated task reports for specific employees.</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.tasks') }}" id="reportForm">
                <div class="row g-3 align-items-end">
                    
                    <!-- Employee Select -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Select Employee <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">-- Select Employee --</option>
                            @foreach($allEmployees as $emp)
                                <option value="{{ $emp->id }}" {{ $employeeId == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->name }} ({{ $emp->employee_id }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Filter -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-muted small text-uppercase">Date Range</label>
                        <select name="date_filter" id="date_filter" class="form-select" onchange="toggleCustomDates()">
                            <option value="today" {{ $dateFilter == 'today' ? 'selected' : '' }}>Daily (Today)</option>
                            <option value="week" {{ $dateFilter == 'week' ? 'selected' : '' }}>Weekly (This Week)</option>
                            <option value="month" {{ $dateFilter == 'month' ? 'selected' : '' }}>Monthly (This Month)</option>
                            <option value="custom" {{ $dateFilter == 'custom' ? 'selected' : '' }}>Custom Dates</option>
                        </select>
                    </div>

                    <!-- Custom Start Date -->
                    <div class="col-md-2 custom-date-group" style="display: {{ $dateFilter == 'custom' ? 'block' : 'none' }};">
                        <label class="form-label fw-bold text-muted small text-uppercase">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                    </div>

                    <!-- Custom End Date -->
                    <div class="col-md-2 custom-date-group" style="display: {{ $dateFilter == 'custom' ? 'block' : 'none' }};">
                        <label class="form-label fw-bold text-muted small text-uppercase">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                    </div>

                    <!-- Status Filter -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-muted small text-uppercase">Task Status</label>
                        <select name="status" class="form-select">
                            <option value="All" {{ $statusFilter == 'All' ? 'selected' : '' }}>All Statuses</option>
                            <option value="Pending" {{ $statusFilter == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="In Progress" {{ $statusFilter == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Completed" {{ $statusFilter == 'Completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa-solid fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(!$employeeId)
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5 text-muted">
                <i class="fa-solid fa-users-viewfinder display-4 mb-3" style="opacity: 0.3;"></i>
                <h5>Select an Employee</h5>
                <p>Please select an employee and date range above to generate the task report.</p>
            </div>
        </div>
    @else
        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-list-check fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-uppercase fw-bold mb-1 opacity-75">Total Tasks</h6>
                            <h3 class="mb-0 fw-bold">{{ $totalTasks }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm" style="background: var(--brand-sidebar); color: white;">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-clock fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-uppercase fw-bold mb-1 opacity-75">Total Hours Logged</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($totalHours, 2) }} <span class="fs-6 opacity-75">hrs</span></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-light text-dark">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-calendar-day fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-uppercase fw-bold mb-1 text-muted">Reporting Period</h6>
                            <div class="fw-bold fs-6">
                                {{ $queryStartDate ? \Carbon\Carbon::parse($queryStartDate)->format('M d, Y') : 'N/A' }} 
                                <br>
                                <small class="text-muted fw-normal">to</small> 
                                {{ $queryEndDate ? \Carbon\Carbon::parse($queryEndDate)->format('M d, Y') : 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold" style="color: var(--brand-dark);">Task Breakdown</h5>
                <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                    <i class="fa-solid fa-print"></i> Print Report
                </button>
            </div>
            <div class="card-body p-0">
                @if(empty($reportData))
                    <div class="text-center py-5 text-muted">
                        <i class="fa-solid fa-folder-open display-4 mb-3" style="opacity: 0.3;"></i>
                        <h5>No Tasks Found</h5>
                        <p>No tasks match the selected criteria for this employee.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 120px;" class="ps-4">Date</th>
                                    <th>Task Description</th>
                                    <th style="width: 120px;" class="text-center">Hours</th>
                                    <th style="width: 140px;" class="text-center">Status</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reportData as $task)
                                    <tr>
                                        <td class="ps-4">
                                            <span class="fw-medium text-muted">{{ \Carbon\Carbon::parse($task['date'])->format('M d') }}</span>
                                            <br>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($task['date'])->format('Y') }}</small>
                                        </td>
                                        <td class="fw-bold" style="color: var(--brand-dark);">{{ $task['description'] }}</td>
                                        <td class="text-center fw-medium">{{ number_format($task['hours'], 2) }}</td>
                                        <td class="text-center">
                                            @if($task['status'] === 'Completed')
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success">{{ $task['status'] }}</span>
                                            @elseif($task['status'] === 'In Progress')
                                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">{{ $task['status'] }}</span>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">{{ $task['status'] }}</span>
                                            @endif
                                        </td>
                                        <td class="text-muted small">{{ $task['remarks'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

<script>
    function toggleCustomDates() {
        const filter = document.getElementById('date_filter').value;
        const groups = document.querySelectorAll('.custom-date-group');
        const start = document.getElementById('start_date');
        const end = document.getElementById('end_date');
        
        if (filter === 'custom') {
            groups.forEach(el => el.style.display = 'block');
            start.setAttribute('required', 'required');
            end.setAttribute('required', 'required');
        } else {
            groups.forEach(el => el.style.display = 'none');
            start.removeAttribute('required');
            end.removeAttribute('required');
        }
    }
</script>

<style>
    @media print {
        .sidebar, .topbar, #reportForm, .btn-outline-secondary {
            display: none !important;
        }
        .content {
            margin-left: 0 !important;
            width: 100% !important;
            padding: 0 !important;
        }
        .card {
            box-shadow: none !important;
            border: none !important;
        }
    }
</style>
@endsection
