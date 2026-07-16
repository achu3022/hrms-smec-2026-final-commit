@extends('layouts.admin')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
    <div>
        <h4 class="mb-1 text-primary"><i class="fa fa-users me-2"></i>Employee Directory</h4>
        <p class="mb-0 text-muted">Manage your team members</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
        <i class="fa fa-upload me-2"></i> Import Excel
    </button>
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
                    <p class="ec-role"><i class="fa fa-briefcase me-1"></i>{{ Str::limit($employee->designationRel->name ?? $employee->designation ?? 'N/A', 18) }}</p>
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
                <small><strong>Required columns:</strong> employee_id, name, company_email, department, designation</small>
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
function viewEmployee(id) {
    $('#viewModalBody').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Loading employee details...</p></div>');
    var myModal = new bootstrap.Modal(document.getElementById('viewModal'));
    myModal.show();
    
    $.ajax({
        url: "{{ url('admin/employees') }}/" + id,
        type: "GET",
        success: function(response) {
            $('#viewModalBody').html(response);
        },
        error: function(xhr) {
            $('#viewModalBody').html('<div class="alert alert-danger m-3">Error loading employee data. Please try again.</div>');
        }
    });
}
</script>
@endpush
