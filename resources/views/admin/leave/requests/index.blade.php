@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Leave Requests</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>From - To</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                        <tr>
                            <td>{{ $req->employee->name ?? 'Unknown' }}</td>
                            <td>{{ $req->leaveType->name ?? 'Unknown' }}</td>
                            <td>{{ $req->start_date }} to {{ $req->end_date }}</td>
                            <td>{{ $req->total_days }}</td>
                            <td>
                                <span class="badge bg-{{ $req->status == 'Approved' ? 'success' : ($req->status == 'Rejected' ? 'danger' : 'warning') }}">{{ $req->status }}</span>
                            </td>
                            <td>
                                <form action="{{ route('admin.leave-requests.destroy', $req->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this request?')"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No leave requests found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Log New Leave</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.leave-requests.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label>Employee</label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->employee_id }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Leave Type</label>
                        <select name="leave_type_id" class="form-select" required>
                            <option value="">Select Type</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->days_allowed }} days)</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3 row">
                        <div class="col">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col">
                            <label>End Date</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label>Reason (Optional)</label>
                        <textarea name="reason" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100">Approve & Log Leave</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
