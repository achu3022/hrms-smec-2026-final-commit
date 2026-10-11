@extends('layouts.admin')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
    <div>
        <h4 class="mb-1 text-primary"><i class="fa fa-users me-2"></i>Employee Directory</h4>
        <p class="mb-0 text-muted">Manage your team members</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
            <i class="fa fa-user-plus me-2"></i> Add Employee
        </button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="fa fa-upload me-2"></i> Import Excel
        </button>
    </div>
</div>

<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.employees.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by Name or Emp ID..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="department_id" class="form-select">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="Terminated" {{ request('status') == 'Terminated' ? 'selected' : '' }}>Terminated</option>
                    <option value="Resigned" {{ request('status') == 'Resigned' ? 'selected' : '' }}>Resigned</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="fa fa-filter me-1"></i> Filter</button>
                <a href="{{ route('admin.employees.index') }}" class="btn btn-light" title="Clear Filters"><i class="fa fa-undo"></i></a>
            </div>
        </form>
    </div>
</div>
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mb-4">
    @forelse($employees as $employee)
        <div class="col">
            <div class="ec-card" onclick="viewEmployee({{ $employee->id }})">
                <!-- Left: Square Image -->
                <div class="ec-image-side">
                    @if($employee->photo)
                        <img src="{{ Storage::url($employee->photo) }}" class="ec-img" alt="{{ $employee->name }}">
                    @else
                        <div class="ec-img-placeholder">
                            <i class="fa fa-user fa-2x"></i>
                        </div>
                    @endif
                </div>

                <!-- Right: Info -->
                <div class="ec-content-side">
                    <p class="ec-name">{{ $employee->name }}</p>
                    <p class="ec-empid"><i class="fa fa-id-badge me-1"></i>{{ $employee->employee_id }}</p>
                    <p class="ec-dept"><i class="fa fa-building me-1"></i>{{ Str::limit($employee->departmentRel->name ?? $employee->department ?? 'N/A', 18) }}</p>
                    <p class="ec-role">
                        <i class="fa fa-briefcase me-1"></i>{{ Str::limit($employee->designationRel->name ?? $employee->designation ?? 'N/A', 18) }}
                        @if($employee->is_teamleader)
                            <span class="badge bg-warning text-dark ms-1" style="font-size: 0.6rem;">Teamleader</span>
                        @endif
                        @if(!$employee->checkIsProfileComplete())
                            <span class="badge bg-danger text-white ms-1" style="font-size: 0.6rem;" title="Missing: {{ implode(', ', $employee->getMissingProfileFields()) }}">Incomplete Profile</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="empty-state">
                <i class="fa fa-users"></i>
                <h5>No Employees Found</h5>
                <p class="text-muted">Start by adding your first employee</p>
            </div>
        </div>
    @endforelse
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $employees->links('pagination::bootstrap-5') }}
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('admin.employees.import') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title"><i class="fa fa-upload me-2"></i>Import Employees</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
                <label class="form-label fw-bold">Select Excel/CSV File</label>
                <input type="file" name="file" class="form-control" required accept=".xlsx,.xls,.csv">
            </div>
            <div class="alert alert-info mb-0">
                <small><strong>Required columns:</strong> employee_id, full_name, department, dob, personal_phone</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary"><i class="fa fa-upload me-1"></i>Import</button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- Add Employee Modal -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('admin.employees.store') }}" method="POST">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title"><i class="fa fa-user-plus me-2"></i>Add Temporary Employee Login</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="alert alert-info py-2">
                <small><i class="fa fa-info-circle me-1"></i> The employee's password will be their DOB in <strong>DDMMYYYY</strong> format.</small>
            </div>
            <div class="mb-3">
                <label class="form-label">Employee ID <span class="text-danger">*</span></label>
                <input type="text" name="employee_id" class="form-control @error('employee_id') is-invalid @enderror" value="{{ old('employee_id') }}" required placeholder="SA00000">
                @error('employee_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="John Doe">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Department <span class="text-danger">*</span></label>
                <select name="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                    <option value="">Select Department</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
                @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Shift <span class="text-danger">*</span></label>
                <select name="shift" id="create_shift_select" class="form-select @error('shift') is-invalid @enderror" required onchange="toggleCustomShiftInput('create')">
                    <option value="9:00 AM - 6:00 PM" {{ old('shift', '9:00 AM - 6:00 PM') == '9:00 AM - 6:00 PM' ? 'selected' : '' }}>9:00 AM - 6:00 PM (Standard)</option>
                    <option value="8:00 AM - 5:00 PM" {{ old('shift') == '8:00 AM - 5:00 PM' ? 'selected' : '' }}>8:00 AM - 5:00 PM (Early)</option>
                    <option value="10:00 AM - 7:00 PM" {{ old('shift') == '10:00 AM - 7:00 PM' ? 'selected' : '' }}>10:00 AM - 7:00 PM (Late)</option>
                    <option value="11:00 AM - 8:00 PM" {{ old('shift') == '11:00 AM - 8:00 PM' ? 'selected' : '' }}>11:00 AM - 8:00 PM (Evening)</option>
                    <option value="custom" {{ old('shift') == 'custom' ? 'selected' : '' }}>Custom Time Shift...</option>
                </select>
                @error('shift') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="row mb-3 {{ old('shift') == 'custom' ? '' : 'd-none' }}" id="create_custom_shift_container">
                <div class="col-6">
                    <label class="form-label text-muted small">Shift Start Time</label>
                    <input type="time" name="custom_shift_start" id="create_custom_shift_start" class="form-control" value="{{ old('custom_shift_start', '09:00') }}">
                </div>
                <div class="col-6">
                    <label class="form-label text-muted small">Shift End Time</label>
                    <input type="time" name="custom_shift_end" id="create_custom_shift_end" class="form-control" value="{{ old('custom_shift_end', '18:00') }}">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror" value="{{ old('dob') }}" required>
                @error('dob') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Personal Phone <span class="text-danger">*</span></label>
                <input type="text" name="personal_phone" class="form-control @error('personal_phone') is-invalid @enderror" value="{{ old('personal_phone') }}" required placeholder="Phone number">
                @error('personal_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success"><i class="fa fa-save me-1"></i>Create Login</button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content overflow-hidden">
      <div class="modal-body p-0" id="viewModalBody">
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Loading employee details...</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
/* Horizontal Employee Cards */
.ec-card {
    display: flex;
    flex-direction: row;
    background: #ffffff;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #f0f2f5;
    height: 110px;
}
.ec-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(47,48,146,0.12);
    border-color: #00aa9e;
}
/* Left square image */
.ec-image-side {
    flex: 0 0 110px;
    width: 110px;
    height: 110px;
    background: linear-gradient(135deg, #2f3092, #0070bc);
    overflow: hidden;
    position: relative;
}
.ec-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: top center;
}
.ec-img-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255,255,255,0.4);
}
/* Right info */
.ec-content-side {
    flex: 1;
    padding: 10px 12px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-width: 0;
}
.ec-name {
    margin: 0 0 2px;
    font-size: 0.88rem;
    font-weight: 700;
    color: #2f3092;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.3;
}
.ec-empid {
    margin: 0 0 2px;
    font-size: 0.72rem;
    font-weight: 600;
    color: #0070bc;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ec-dept {
    margin: 0 0 2px;
    font-size: 0.72rem;
    color: #6b7280;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ec-role {
    margin: 0 0 4px;
    font-size: 0.7rem;
    color: #9ca3af;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Page Header */
.page-header h4 {
    font-weight: 700;
    font-size: 1.6rem;
}


/* Empty State */
.empty-state {
    text-align: center;
    padding: 80px 20px;
    background: linear-gradient(135deg, rgba(47,48,146,0.03), rgba(0,170,158,0.03));
    border-radius: 20px;
    border: 2px dashed rgba(0, 170, 158, 0.3);
}

.empty-state i {
    font-size: 4rem;
    color: #00aa9e;
    margin-bottom: 20px;
    opacity: 0.5;
}

.empty-state h5 {
    color: #2f3092;
    font-weight: 600;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    @if($errors->hasAny(['name', 'employee_id', 'department_id', 'shift', 'dob', 'personal_phone']))
        var addEmployeeModal = new bootstrap.Modal(document.getElementById('addEmployeeModal'));
        addEmployeeModal.show();
    @endif

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('show_import') === '1') {
        var importModal = new bootstrap.Modal(document.getElementById('importModal'));
        importModal.show();
    }
});

function viewEmployee(id) {
    $('#viewModalBody').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Loading employee details...</p></div>');
    var myModal = new bootstrap.Modal(document.getElementById('viewModal'));
    myModal.show();
    
    $.ajax({
        url: "{{ url('admin/employees') }}/" + id,
        type: "GET",
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            $('#viewModalBody').html(response);
        },
        error: function(xhr) {
            var errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Error loading employee data (Status " + xhr.status + "). Please try again.";
            console.error("View Employee Error:", xhr.responseText);
            $('#viewModalBody').html('<div class="alert alert-danger m-3"><i class="fa fa-exclamation-circle me-2"></i>' + errMsg + '</div>');
        }
    });
}

function toggleCustomShiftInput(prefix) {
    var select = document.getElementById(prefix + '_shift_select');
    var container = document.getElementById(prefix + '_custom_shift_container');
    if (select && container) {
        if (select.value === 'custom') {
            container.classList.remove('d-none');
        } else {
            container.classList.add('d-none');
        }
    }
}
</script>
@endpush
