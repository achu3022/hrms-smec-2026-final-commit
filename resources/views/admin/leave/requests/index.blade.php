@extends('layouts.admin')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-4">
    <div>
        <h2 class="h4 mb-1 fw-bold text-dark">Leave Requests Log</h2>
        <p class="text-muted small mb-0">View all requests and manually log new leave for employees</p>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-8 order-2 order-lg-1">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark fs-6">Existing Requests</h5>
                <span class="badge bg-light text-secondary border fw-normal">{{ count($requests) }} total</span>
            </div>
            <div class="card-body p-0">
                <!-- Desktop Table -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-uppercase text-muted" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                <th>Employee</th>
                                <th>Type</th>
                                <th>From - To</th>
                                <th>Days</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $req)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $req->employee->name ?? 'Unknown' }}</div>
                                    <small class="text-muted">{{ $req->employee->employee_id ?? '' }}</small>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $req->leaveType->name ?? 'Unknown' }}</span></td>
                                <td class="small">{{ \Carbon\Carbon::parse($req->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($req->end_date)->format('d M Y') }}</td>
                                <td class="fw-semibold">{{ $req->total_days }}</td>
                                <td>
                                    <span class="badge bg-{{ $req->status == 'Approved' ? 'success' : ($req->status == 'Rejected' ? 'danger' : 'warning') }}">
                                        {{ $req->status }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('admin.leave-requests.destroy', $req->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this request?')"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No leave requests found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile List Cards -->
                <div class="d-block d-md-none p-3 bg-light">
                    @forelse($requests as $req)
                    <div class="bg-white rounded-3 border p-3 mb-3 shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <div class="fw-bold text-dark">{{ $req->employee->name ?? 'Unknown' }}</div>
                                <span class="badge bg-light text-secondary border" style="font-size: 0.75rem;">{{ $req->employee->employee_id ?? '' }}</span>
                            </div>
                            <span class="badge bg-{{ $req->status == 'Approved' ? 'success' : ($req->status == 'Rejected' ? 'danger' : 'warning') }}">
                                {{ $req->status }}
                            </span>
                        </div>
                        <div class="row g-2 mb-2 text-muted small">
                            <div class="col-6">
                                <span class="d-block text-uppercase text-xs fw-semibold">Type</span>
                                <span class="text-dark fw-medium">{{ $req->leaveType->name ?? 'Unknown' }}</span>
                            </div>
                            <div class="col-6">
                                <span class="d-block text-uppercase text-xs fw-semibold">Duration</span>
                                <span class="text-dark fw-bold">{{ $req->total_days }} Days</span>
                            </div>
                            <div class="col-12 mt-1">
                                <span class="d-block text-uppercase text-xs fw-semibold">Dates</span>
                                <span class="text-dark fw-medium bg-light px-2 py-1 rounded d-inline-block border">
                                    {{ \Carbon\Carbon::parse($req->start_date)->format('d M Y') }} to {{ \Carbon\Carbon::parse($req->end_date)->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                        <div class="pt-2 border-top text-end">
                            <form action="{{ route('admin.leave-requests.destroy', $req->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger px-3 py-1" onclick="return confirm('Delete this request?')">
                                    <i class="fa fa-trash me-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">No leave requests found.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-lg-4 order-1 order-lg-2">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="mb-0 fw-bold text-dark fs-6"><i class="fa-solid fa-plus-circle text-primary me-2"></i>Log New Leave</h5>
            </div>
            <div class="card-body p-3 p-md-4">
                <form action="{{ route('admin.leave-requests.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Employee</label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->employee_id }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Leave Type</label>
                        <select name="leave_type_id" class="form-select" required>
                            <option value="">Select Type</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->days_allowed }} days)</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3 row g-2">
                        <div class="col-12 col-sm-6">
                            <label class="form-label small fw-bold text-secondary">Start Date</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label small fw-bold text-secondary">End Date</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Reason (Optional)</label>
                        <textarea name="reason" class="form-control" rows="2" placeholder="Enter reason..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100 py-2 fw-bold">
                        <i class="fa-solid fa-check me-1"></i> Approve & Log Leave
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
