@extends('layouts.admin')

@section('content')
<h2 class="mb-4">Dashboard</h2>

<div class="row">
    <div class="col-md-3 mb-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <h5 class="card-title">Total Employees</h5>
                <h3>{{ $totalEmployees }}</h3>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('admin.employees.index') }}" class="text-white text-decoration-none">View Details</a>
                <i class="fa fa-arrow-circle-right"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <h5 class="card-title">Active Employees</h5>
                <h3>{{ $activeEmployees }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card bg-danger text-white h-100">
            <div class="card-body">
                <h5 class="card-title">Inactive Employees</h5>
                <h3>{{ $inactiveEmployees }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <h5 class="card-title">Departments</h5>
                <h3>{{ $departments }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">Recent Entries</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Emp ID</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Designation</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentEntries as $emp)
                            <tr>
                                <td>{{ $emp->employee_id }}</td>
                                <td>{{ $emp->name }}</td>
                                <td>{{ $emp->departmentRel->name ?? $emp->department }}</td>
                                <td>{{ $emp->designationRel->name ?? $emp->designation }}</td>
                                <td>{{ $emp->joining_date }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center">No recent entries found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
