@extends('layouts.admin')

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.attendance.index') }}" method="GET" class="row align-items-center">
                    <div class="col-auto">
                        <label class="col-form-label">View Attendance For Date:</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="date" class="form-control" value="{{ $date }}" required>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Load</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Attendance Records for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Status</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $att)
                        <tr>
                            <td>{{ $att->employee->name ?? 'Unknown' }}</td>
                            <td>
                                @php
                                    $color = 'success';
                                    if($att->status == 'Absent') $color = 'danger';
                                    if($att->status == 'Half-Day' || $att->status == 'Late') $color = 'warning';
                                    if($att->status == 'Leave') $color = 'info';
                                @endphp
                                <span class="badge bg-{{ $color }}">{{ $att->status }}</span>
                            </td>
                            <td>{{ $att->check_in ?? '--:--' }}</td>
                            <td>{{ $att->check_out ?? '--:--' }}</td>
                            <td>
                                <form action="{{ route('admin.attendance.destroy', $att->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?')"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No attendance logged for this date.</td>
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
                <h5>Mark Attendance</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.attendance.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="date" value="{{ $date }}">
                    
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
                        <label>Status</label>
                        <select name="status" class="form-select" required>
                            <option value="Present">Present</option>
                            <option value="Absent">Absent</option>
                            <option value="Half-Day">Half-Day</option>
                            <option value="Leave">Leave</option>
                            <option value="Late">Late</option>
                        </select>
                    </div>
                    
                    <div class="mb-3 row">
                        <div class="col">
                            <label>Check In</label>
                            <input type="time" name="check_in" class="form-control">
                        </div>
                        <div class="col">
                            <label>Check Out</label>
                            <input type="time" name="check_out" class="form-control">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100">Save Attendance</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
