@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Edit Employee: {{ $employee->name }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $employee->name }}" required>
                </div>
                <div class="col-md-6">
                    <label>Department (ID)</label>
                    <input type="text" name="department_id" class="form-control" value="{{ $employee->department_id }}">
                </div>
                <div class="col-md-6">
                    <label>Designation (ID)</label>
                    <input type="text" name="designation_id" class="form-control" value="{{ $employee->designation_id }}">
                </div>
                <div class="col-md-6">
                    <label>Status</label>
                    <select name="status" class="form-select">
                        <option value="Active" {{ $employee->status == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ $employee->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="Resigned" {{ $employee->status == 'Resigned' ? 'selected' : '' }}>Resigned</option>
                        <option value="Terminated" {{ $employee->status == 'Terminated' ? 'selected' : '' }}>Terminated</option>
                        <option value="On Leave" {{ $employee->status == 'On Leave' ? 'selected' : '' }}>On Leave</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Update Employee</button>
                <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
