@extends('layouts.admin')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { background-color: #f8f9fc; }
    .att-font { font-family: 'Inter', sans-serif; }
    
    /* Summary Cards */
    .att-summary-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .att-summary-title { font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 4px; }
    .att-summary-value { font-size: 1.5rem; font-weight: 700; color: #1e293b; }
    .att-summary-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .icon-total { background: #f1f5f9; color: #64748b; }
    .icon-present { background: #dcfce7; color: #16a34a; }
    .icon-absent { background: #fee2e2; color: #dc2626; }
    .icon-late { background: #ffedd5; color: #ea580c; }
    .icon-half { background: #fef3c7; color: #d97706; }
    .icon-leave { background: #e0e7ff; color: #4f46e5; }
    .icon-not-punched { background: #f1f5f9; color: #64748b; }
    
    .text-present { color: #16a34a !important; }
    .text-absent { color: #dc2626 !important; }
    .text-late { color: #ea580c !important; }
    .text-half { color: #d97706 !important; }
    .text-leave { color: #4f46e5 !important; }
    .text-not-punched { color: #64748b !important; }

    /* Filters Section */
    .att-filter-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 24px;
        margin-top: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .att-filter-label { font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 8px; display: block; }
    .att-filter-select, .att-filter-input {
        border-radius: 8px; border: 1px solid #cbd5e1; padding: 10px 14px; font-size: 0.85rem; width: 100%;
        color: #334155; font-weight: 500; outline: none; transition: border-color 0.2s;
    }
    .att-filter-select:focus, .att-filter-input:focus { border-color: #4f46e5; }

    /* Table Section */
    .att-table-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        margin-top: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        overflow: hidden;
    }
    .att-table-header { padding: 20px 24px; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #1e293b; font-size: 1.1rem; }
    .att-table { width: 100%; border-collapse: collapse; }
    .att-table th { background: #f8fafc; padding: 12px 24px; text-align: left; font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; }
    .att-table td { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; color: #334155; vertical-align: middle; }
    .att-table tr:last-child td { border-bottom: none; }
    
    .status-pill { padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; }
    .pill-present { background: #dcfce7; color: #15803d; }
    .pill-absent { background: #fee2e2; color: #b91c1c; }
    .pill-late { background: #ffedd5; color: #c2410c; }
    .pill-half { background: #fef3c7; color: #b45309; }
    .pill-leave { background: #e0e7ff; color: #4338ca; }
    .pill-not-punched { background: #f1f5f9; color: #475569; border: 1px dashed #cbd5e1; }

    .btn-primary-custom { background: #4f46e5; color: white; border-radius: 8px; padding: 10px 20px; font-weight: 600; border: none; font-size: 0.85rem; transition: background 0.2s; }
    .btn-primary-custom:hover { background: #4338ca; color: white; }
    .btn-outline-custom { background: white; color: #4f46e5; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 20px; font-weight: 600; font-size: 0.85rem; transition: all 0.2s; }
    .btn-outline-custom:hover { background: #f8fafc; border-color: #4f46e5; color: #4f46e5;}
</style>

<div class="att-font pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1" style="font-size: 1.5rem;">Attendance Logs</h2>
            <p class="text-muted small mb-0">Track and manage employee attendance records</p>
        </div>
        <div>
            <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addAttModal">
                <i class="fa-solid fa-plus me-2"></i> Add Log
            </button>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm font-medium">
            @foreach($errors->all() as $e) <div><i class="fa-solid fa-xmark-circle me-2"></i>{{ $e }}</div> @endforeach
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="row g-3">
        <div class="col-md-3 col-sm-6">
            <div class="att-summary-card">
                <div>
                    <div class="att-summary-title">Total Records</div>
                    <div class="att-summary-value">{{ $stats['total'] }}</div>
                </div>
                <div class="att-summary-icon icon-total"><i class="fa-regular fa-calendar-days"></i></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="att-summary-card">
                <div>
                    <div class="att-summary-title">Present</div>
                    <div class="att-summary-value text-present">{{ $stats['present'] }}</div>
                </div>
                <div class="att-summary-icon icon-present"><i class="fa-solid fa-check"></i></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="att-summary-card">
                <div>
                    <div class="att-summary-title">Not Punched In</div>
                    <div class="att-summary-value text-not-punched">{{ $stats['not_punched_in'] }}</div>
                </div>
                <div class="att-summary-icon icon-not-punched"><i class="fa-solid fa-user-clock"></i></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="att-summary-card">
                <div>
                    <div class="att-summary-title">Absent</div>
                    <div class="att-summary-value text-absent">{{ $stats['absent'] }}</div>
                </div>
                <div class="att-summary-icon icon-absent"><i class="fa-solid fa-xmark"></i></div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="att-summary-card">
                <div>
                    <div class="att-summary-title">Late</div>
                    <div class="att-summary-value text-late">{{ $stats['late'] }}</div>
                </div>
                <div class="att-summary-icon icon-late"><i class="fa-regular fa-clock"></i></div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="att-summary-card">
                <div>
                    <div class="att-summary-title">Half-Day</div>
                    <div class="att-summary-value text-half">{{ $stats['half_day'] }}</div>
                </div>
                <div class="att-summary-icon icon-half"><i class="fa-solid fa-circle-half-stroke"></i></div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="att-summary-card">
                <div>
                    <div class="att-summary-title">On Leave</div>
                    <div class="att-summary-value text-leave">{{ $stats['leave'] }}</div>
                </div>
                <div class="att-summary-icon icon-leave"><i class="fa-solid fa-bed"></i></div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    @php
        $activeDateFilterType = request('date_filter_type');
        if (!$activeDateFilterType && (request('start_date') || request('end_date'))) {
            $activeDateFilterType = 'custom';
        }
        if (!$activeDateFilterType) {
            $activeDateFilterType = 'date';
        }
    @endphp
    <div class="att-filter-card">
        <form method="GET" action="{{ route('admin.attendance.index') }}" id="filterForm">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="att-filter-label">Date Filter</label>
                    <select name="date_filter_type" id="dateFilterType" class="att-filter-select">
                        <option value="date" {{ $activeDateFilterType == 'date' ? 'selected' : '' }}>Single Date</option>
                        <option value="month" {{ $activeDateFilterType == 'month' ? 'selected' : '' }}>Monthly</option>
                        <option value="year" {{ $activeDateFilterType == 'year' ? 'selected' : '' }}>Yearly</option>
                        <option value="custom" {{ $activeDateFilterType == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        <option value="all" {{ $activeDateFilterType == 'all' ? 'selected' : '' }}>All Dates</option>
                    </select>
                </div>

                <div class="col-md-2" id="singleDateDiv">
                    <label class="att-filter-label">Select Date</label>
                    <input type="date" name="date" class="att-filter-input" value="{{ request('date', date('Y-m-d')) }}">
                </div>

                <div class="col-md-2" id="yearDiv" style="display: none;">
                    <label class="att-filter-label">Year</label>
                    <select name="year" class="att-filter-select">
                        @for($y = date('Y'); $y >= date('Y')-3; $y--)
                            <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-2" id="monthDiv" style="display: none;">
                    <label class="att-filter-label">Month</label>
                    <select name="month" class="att-filter-select">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ sprintf('%02d', $m) }}" {{ request('month', date('m')) == sprintf('%02d', $m) ? 'selected' : '' }}>
                                {{ date('F', mktime(0,0,0,$m,1)) }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-2" id="startDiv" style="display: none;">
                    <label class="att-filter-label">Start Date</label>
                    <input type="date" name="start_date" class="att-filter-input" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2" id="endDiv" style="display: none;">
                    <label class="att-filter-label">End Date</label>
                    <input type="date" name="end_date" class="att-filter-input" value="{{ request('end_date') }}">
                </div>

                <div class="col-md-2">
                    <label class="att-filter-label">Department</label>
                    <select name="department_id" class="att-filter-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="att-filter-label">Employee</label>
                    <select name="employee_id" class="att-filter-select">
                        <option value="">All Employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="att-filter-label">Status</label>
                    <select name="status" class="att-filter-select">
                        <option value="">All Status</option>
                        <option value="Present" {{ request('status') == 'Present' ? 'selected' : '' }}>Present</option>
                        <option value="Absent" {{ request('status') == 'Absent' ? 'selected' : '' }}>Absent</option>
                        <option value="Late" {{ request('status') == 'Late' ? 'selected' : '' }}>Late</option>
                        <option value="Half-Day" {{ request('status') == 'Half-Day' ? 'selected' : '' }}>Half-Day</option>
                        <option value="Leave" {{ request('status') == 'Leave' ? 'selected' : '' }}>Leave</option>
                        <option value="Not Punched In" {{ request('status') == 'Not Punched In' ? 'selected' : '' }}>Not Punched In</option>
                    </select>
                </div>

                <div class="col-md-12 d-flex justify-content-end gap-2 mt-4 pt-2 border-top">
                    @if(request()->anyFilled(['department_id', 'employee_id', 'status', 'start_date', 'end_date', 'month', 'year']) || request('date_filter_type', 'date') != 'date')
                        <a href="{{ route('admin.attendance.index') }}" class="btn-outline-custom text-decoration-none text-center">Clear</a>
                    @endif
                    <button type="button" class="btn-outline-custom" onclick="exportExcel()"><i class="fa-solid fa-file-excel me-2"></i>Export to Excel</button>
                    <button type="submit" class="btn-primary-custom">Apply Filters</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="att-table-card">
        <div class="att-table-header">Attendance Records</div>
        @if($attendances->isEmpty())
            <div class="text-center py-5">
                <i class="fa-regular fa-calendar-xmark text-muted mb-3" style="font-size: 2.5rem;"></i>
                <h6 class="fw-bold text-dark">No records found</h6>
                <p class="text-muted small">Try adjusting your filters.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="att-table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Location</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $att)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $att->employee->name ?? 'Unknown' }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $att->employee->employee_id ?? '' }} • {{ $att->employee->departmentRel->name ?? 'General' }}</div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ \Carbon\Carbon::parse($att->date)->format('d-m-Y') }}</div>
                                </td>
                                <td>
                                    @php
                                        $pillClass = match($att->status) {
                                            'Present' => 'pill-present',
                                            'Absent' => 'pill-absent',
                                            'Late' => 'pill-late',
                                            'Half-Day' => 'pill-half',
                                            'Leave' => 'pill-leave',
                                            'Not Punched In' => 'pill-not-punched',
                                            default => 'pill-present'
                                        };
                                    @endphp
                                    <span class="status-pill {{ $pillClass }}">{{ $att->status }}</span>
                                </td>
                                <td class="fw-medium text-dark">{{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('h:i A') : '—' }}</td>
                                <td class="fw-medium text-dark">{{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('h:i A') : '—' }}</td>
                                
                                <td>
                                    @if($att->location_verified)
                                        <span class="badge bg-success"><i class="fa-solid fa-location-dot"></i> Verified</span>
                                    @else
                                        <span class="badge bg-secondary">Unverified</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button type="button" class="bg-light text-primary border rounded me-1" style="padding: 4px 10px; transition: 0.2s;" title="Mark / Edit Status" onclick="openEditAttendanceModal({{ $att->employee_id }}, '{{ addslashes($att->employee->name ?? 'Employee') }}', '{{ $att->date }}', '{{ $att->status === 'Not Punched In' ? 'Present' : $att->status }}', '{{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '' }}', '{{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '' }}')">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    @if($att->id && ($att->check_in_latitude || $att->checkin_photo))
                                        <button class="bg-light text-primary border rounded me-1" style="padding: 4px 10px; transition: 0.2s;" title="View Location" onclick="viewLocation({{ $att->id }}, {{ $att->check_in_latitude ?? 'null' }}, {{ $att->check_in_longitude ?? 'null' }}, '{{ $att->checkin_distance ? round($att->checkin_distance) . 'm' : 'N/A' }}', '{{ $att->checkin_accuracy ? round($att->checkin_accuracy) . 'm' : 'N/A' }}', '{{ $att->checkin_photo ? Storage::url($att->checkin_photo) : '' }}', '{{ $att->checkout_photo ? Storage::url($att->checkout_photo) : '' }}')"><i class="fa-solid fa-map-location-dot"></i></button>
                                    @endif
                                    
                                    @if($att->id)
                                        <form action="{{ route('admin.attendance.destroy', $att->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this record?');">
                                            @csrf @method('DELETE')
                                            <button class="bg-light text-secondary border rounded" style="padding: 4px 10px; transition: 0.2s;"><i class="fa-solid fa-trash-can"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>


<!-- Location Modal -->
<div class="modal fade" id="locationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-map-location-dot text-primary me-2"></i>Attendance Punch Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3 pb-4">
                <div class="row mb-3">
                    <div class="col-6 text-center">
                        <strong>Check In Photo:</strong><br>
                        <img id="modalCheckinPhoto" src="" alt="Check In Photo" class="img-fluid rounded mt-2 shadow-sm" style="max-height: 200px; display: none;">
                        <span id="modalNoCheckinPhoto" class="text-muted d-block mt-2">No photo available</span>
                    </div>
                    <div class="col-6 text-center">
                        <strong>Check Out Photo:</strong><br>
                        <img id="modalCheckoutPhoto" src="" alt="Check Out Photo" class="img-fluid rounded mt-2 shadow-sm" style="max-height: 200px; display: none;">
                        <span id="modalNoCheckoutPhoto" class="text-muted d-block mt-2">No photo available</span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <strong>Distance from Office:</strong> <span id="modalDistance"></span>
                    </div>
                    <div class="col-6">
                        <strong>GPS Accuracy:</strong> <span id="modalAccuracy"></span>
                    </div>
                </div>
                <div id="mapContainer" style="height: 400px; width: 100%; border-radius: 8px; background: #f1f5f9; display: flex; align-items: center; justify-content: center;">
                    <div id="map" style="height: 100%; width: 100%; border-radius: 8px; display: none;"></div>
                    <span id="noLocationMsg" class="text-muted">No location data recorded for this punch.</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addAttModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle"><i class="fa-solid fa-plus-circle text-primary me-2"></i>Add Attendance Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3 pb-4">
                <form action="{{ route('admin.attendance.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary text-uppercase" style="font-size: 0.75rem;">Employee</label>
                        <select name="employee_id" required class="form-control">
                            <option value="">Select Employee...</option>
                            @foreach($employees as $e)
                                <option value="{{ $e->id }}">{{ $e->employee_id }} - {{ $e->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold text-secondary text-uppercase" style="font-size: 0.75rem;">Date</label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-secondary text-uppercase" style="font-size: 0.75rem;">Status</label>
                            <select name="status" required class="form-control">
                                <option value="Present">Present</option>
                                <option value="Absent">Absent</option>
                                <option value="Half-Day">Half-Day</option>
                                <option value="Late">Late</option>
                                <option value="Leave">Leave</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-6">
                            <label class="form-label fw-bold text-secondary text-uppercase" style="font-size: 0.75rem;">Check In</label>
                            <input type="time" name="check_in" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-secondary text-uppercase" style="font-size: 0.75rem;">Check Out</label>
                            <input type="time" name="check_out" class="form-control">
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-bold py-2">Save Log</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit / Mark Status Modal -->
<div class="modal fade" id="editAttModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i> Mark / Edit Attendance Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3 pb-4">
                <form action="{{ route('admin.attendance.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="employee_id" id="edit_employee_id">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary text-uppercase" style="font-size: 0.75rem;">Employee</label>
                        <input type="text" id="edit_employee_name" class="form-control bg-light" readonly>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold text-secondary text-uppercase" style="font-size: 0.75rem;">Date</label>
                            <input type="date" name="date" id="edit_date" required class="form-control bg-light" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-secondary text-uppercase" style="font-size: 0.75rem;">Status *</label>
                            <select name="status" id="edit_status" required class="form-control">
                                <option value="Present">Present</option>
                                <option value="Absent">Absent</option>
                                <option value="Half-Day">Half-Day</option>
                                <option value="Late">Late</option>
                                <option value="Leave">Leave</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-6">
                            <label class="form-label fw-bold text-secondary text-uppercase" style="font-size: 0.75rem;">Check In Time</label>
                            <input type="time" name="check_in" id="edit_check_in" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-secondary text-uppercase" style="font-size: 0.75rem;">Check Out Time</label>
                            <input type="time" name="check_out" id="edit_check_out" class="form-control">
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-bold py-2"><i class="fa-solid fa-check me-1"></i> Update Attendance</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&loading=async&callback=initMap" async defer></script>

<script>
    function openEditAttendanceModal(empId, empName, dateStr, status, checkIn, checkOut) {
        $('#edit_employee_id').val(empId);
        $('#edit_employee_name').val(empName);
        $('#edit_date').val(dateStr);
        $('#edit_status').val(status);
        $('#edit_check_in').val(checkIn);
        $('#edit_check_out').val(checkOut);
        
        var editModal = new bootstrap.Modal(document.getElementById('editAttModal'));
        editModal.show();
    }
    
    let detailMap, empMarker, officeMarker, circle, polyline;
    // Define the default office location from DB or default
    const officeLat = {{ \App\Models\OfficeLocation::first()->latitude ?? 9.99419969778416 }};
    const officeLng = {{ \App\Models\OfficeLocation::first()->longitude ?? 76.2922585260837 }};
    const officeRadius = {{ \App\Models\OfficeLocation::first()->radius ?? 100 }};

    function initMap() {
        // Will initialize on demand
    }

    function viewLocation(id, lat, lng, dist, acc, checkinPhoto, checkoutPhoto) {
        document.getElementById('modalDistance').innerText = dist;
        document.getElementById('modalAccuracy').innerText = acc;
        
        // Handle Photos
        const imgCheckin = document.getElementById('modalCheckinPhoto');
        const noImgCheckin = document.getElementById('modalNoCheckinPhoto');
        if (checkinPhoto) {
            imgCheckin.src = checkinPhoto;
            imgCheckin.style.display = 'inline-block';
            noImgCheckin.style.display = 'none';
        } else {
            imgCheckin.style.display = 'none';
            noImgCheckin.style.display = 'block';
        }

        const imgCheckout = document.getElementById('modalCheckoutPhoto');
        const noImgCheckout = document.getElementById('modalNoCheckoutPhoto');
        if (checkoutPhoto) {
            imgCheckout.src = checkoutPhoto;
            imgCheckout.style.display = 'inline-block';
            noImgCheckout.style.display = 'none';
        } else {
            imgCheckout.style.display = 'none';
            noImgCheckout.style.display = 'block';
        }
        
        const myModal = new bootstrap.Modal(document.getElementById('locationModal'));
        myModal.show();
        
        if (!lat || !lng) {
            document.getElementById('map').style.display = 'none';
            document.getElementById('noLocationMsg').style.display = 'block';
            return;
        }
        
        document.getElementById('map').style.display = 'block';
        document.getElementById('noLocationMsg').style.display = 'none';
        
        setTimeout(() => {
            const empPos = { lat: parseFloat(lat), lng: parseFloat(lng) };
            const officePos = { lat: parseFloat(officeLat), lng: parseFloat(officeLng) };
            
            if (!detailMap) {
                detailMap = new google.maps.Map(document.getElementById("map"), {
                    zoom: 17,
                    center: officePos,
                    mapTypeId: 'satellite'
                });
            } else {
                detailMap.setCenter(officePos);
            }
            
            if (empMarker) empMarker.setMap(null);
            if (officeMarker) officeMarker.setMap(null);
            if (circle) circle.setMap(null);
            if (polyline) polyline.setMap(null);
            
            officeMarker = new google.maps.Marker({
                position: officePos,
                map: detailMap,
                title: "Office Location",
                icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
            });
            
            empMarker = new google.maps.Marker({
                position: empPos,
                map: detailMap,
                title: "Employee Location",
                icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
            });
            
            circle = new google.maps.Circle({
                map: detailMap,
                radius: parseFloat(officeRadius),
                fillColor: '#00AA00',
                fillOpacity: 0.1,
                strokeColor: '#00AA00',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                center: officePos
            });
            
            polyline = new google.maps.Polyline({
                path: [officePos, empPos],
                geodesic: true,
                strokeColor: '#FF0000',
                strokeOpacity: 1.0,
                strokeWeight: 2,
                map: detailMap
            });
            
            const bounds = new google.maps.LatLngBounds();
            bounds.extend(officePos);
            bounds.extend(empPos);
            detailMap.fitBounds(bounds);
        }, 300); // wait for modal transition
    }

    function toggleDateInputs() {
        const type = document.getElementById('dateFilterType').value;
        document.getElementById('singleDateDiv').style.display = (type === 'date') ? 'block' : 'none';
        document.getElementById('yearDiv').style.display = (type === 'year' || type === 'month') ? 'block' : 'none';
        document.getElementById('monthDiv').style.display = (type === 'month') ? 'block' : 'none';
        document.getElementById('startDiv').style.display = (type === 'custom') ? 'block' : 'none';
        document.getElementById('endDiv').style.display = (type === 'custom') ? 'block' : 'none';
    }

    document.getElementById('dateFilterType').addEventListener('change', toggleDateInputs);

    const startInput = document.querySelector('input[name="start_date"]');
    const endInput = document.querySelector('input[name="end_date"]');
    if (startInput) {
        startInput.addEventListener('change', function() {
            if (this.value) {
                document.getElementById('dateFilterType').value = 'custom';
                toggleDateInputs();
            }
        });
    }
    if (endInput) {
        endInput.addEventListener('change', function() {
            if (this.value) {
                document.getElementById('dateFilterType').value = 'custom';
                toggleDateInputs();
            }
        });
    }

    toggleDateInputs();

    function exportExcel() {
        const form = document.getElementById('filterForm');
        const originalAction = form.action;
        form.action = "{{ route('admin.attendance.export') }}";
        form.submit();
        form.action = originalAction;
    }
</script>
@endpush
@endsection
