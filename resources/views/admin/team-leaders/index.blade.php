@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">Team Leaders</h2>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Assign Team Leader</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.team-leaders.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="employee_id" class="form-label fw-bold">Select Employee <span class="text-danger">*</span></label>
                            <select name="employee_id" id="employee_id" class="form-select select2" required>
                                <option value="">-- Choose Employee --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->employee_id }} - {{ $emp->name }}</option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="department_id" class="form-label fw-bold">Select Department <span class="text-danger">*</span></label>
                            <select name="department_id" id="department_id" class="form-select select2" required>
                                <option value="">-- Choose Department --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-user-plus me-1"></i> Add Team Leader
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Current Team Leaders</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teamLeaders as $leader)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary fw-bold me-3" style="width: 40px; height: 40px;">
                                                {{ substr($leader->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $leader->name }}</div>
                                                <div class="text-muted small">{{ $leader->employee_id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-dark rounded-pill px-3 py-2">
                                            {{ $leader->department ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.team-leaders.destroy', $leader->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove team leader status from {{ $leader->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove Status">
                                                <i class="fa fa-times"></i> Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        <i class="fa fa-users mb-2 fs-4 d-block text-secondary"></i>
                                        No team leaders assigned yet.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 0.375rem;
        padding: 0.375rem 0.75rem;
        min-height: calc(1.5em + 0.75rem + 2px);
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    });
</script>
@endpush
