@extends('layouts.admin')

@section('content')
@push('styles')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@endpush
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Manage Holidays</h2>
            <p class="text-muted small mb-0">Add and manage company holidays for the calendar year.</p>
        </div>
        
        <form method="GET" action="{{ route('admin.holidays.index') }}" class="d-flex align-items-center gap-2">
            <label for="year" class="form-label mb-0 fw-semibold text-muted">Year:</label>
            <select name="year" id="year" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                @for($y = now()->year + 1; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom border-light pt-3 pb-2">
                    <h5 class="h6 mb-0 text-primary"><i class="fa-solid fa-calendar-plus me-2"></i>Add New Holiday</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.holidays.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Holiday Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Independence Day" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="text" name="date" id="holiday_date" class="form-control" placeholder="YYYY-MM-DD" autocomplete="off" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold">Save Holiday</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom border-light pt-3 pb-2 d-flex justify-content-between align-items-center">
                    <h5 class="h6 mb-0 text-dark"><i class="fa-solid fa-list-ul me-2 text-muted"></i>Holidays in {{ $year }}</h5>
                    <span class="badge bg-light text-dark border">{{ $holidays->count() }} records</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4">Date</th>
                                    <th>Day</th>
                                    <th>Holiday Name</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse($holidays as $holiday)
                                    @php $date = \Carbon\Carbon::parse($holiday->date); @endphp
                                    <tr>
                                        <td class="ps-4 fw-semibold">{{ $date->format('M d, Y') }}</td>
                                        <td><span class="badge bg-secondary bg-opacity-10 text-secondary border">{{ $date->format('l') }}</span></td>
                                        <td class="text-dark">{{ $holiday->name }}</td>
                                        <td class="text-end pe-4">
                                            <form action="{{ route('admin.holidays.destroy', $holiday->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this holiday?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="fa-regular fa-calendar-xmark fs-2 mb-3 text-light"></i>
                                            <p class="mb-0 fw-medium">No holidays added for {{ $year }} yet.</p>
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

@push('scripts')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
    $(document).ready(function() {
        $('#holiday_date').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true
        });
    });
</script>
@endpush
@endsection
