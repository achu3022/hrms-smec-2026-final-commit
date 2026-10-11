@extends('layouts.admin')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-4">
    <div>
        <h2 class="h4 mb-1 fw-bold text-dark">Leave Types Configuration</h2>
        <p class="text-muted small mb-0">Define leave categories and annual quotas</p>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-8 order-2 order-lg-1">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark fs-6">Configured Leave Types</h5>
                <span class="badge bg-light text-secondary border fw-normal">{{ count($leaveTypes) }} types</span>
            </div>
            <div class="card-body p-0">
                <!-- Desktop Table -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-uppercase text-muted" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                <th>ID</th>
                                <th>Name</th>
                                <th>Days Allowed/Year</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaveTypes as $type)
                            <tr>
                                <td><span class="text-muted fw-bold">#{{ $type->id }}</span></td>
                                <td><div class="fw-bold text-dark">{{ $type->name }}</div></td>
                                <td><span class="fw-semibold">{{ $type->days_allowed }}</span> Days</td>
                                <td>
                                    <span class="badge bg-{{ $type->is_active ? 'success' : 'danger' }}">
                                        {{ $type->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary me-1 edit-btn" 
                                        data-id="{{ $type->id }}" 
                                        data-name="{{ $type->name }}" 
                                        data-days="{{ $type->days_allowed }}"
                                        data-active="{{ $type->is_active }}">
                                        <i class="fa-solid fa-pen-to-square me-1"></i>Edit
                                    </button>
                                    
                                    <form action="{{ route('admin.leave-types.destroy', $type->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                            <i class="fa-solid fa-trash me-1"></i>Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No leave types defined.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile List Cards -->
                <div class="d-block d-md-none p-3 bg-light">
                    @forelse($leaveTypes as $type)
                    <div class="bg-white rounded-3 border p-3 mb-3 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="fw-bold text-dark fs-6">{{ $type->name }}</div>
                            <span class="badge bg-{{ $type->is_active ? 'success' : 'danger' }}">
                                {{ $type->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="mb-3 text-muted small">
                            <span class="d-inline-block bg-light px-2 py-1 rounded border">
                                <i class="fa-solid fa-calendar-days me-1 text-primary"></i> <strong>{{ $type->days_allowed }}</strong> Days / Year
                            </span>
                        </div>
                        <div class="d-flex gap-2 pt-2 border-top justify-content-end">
                            <button class="btn btn-sm btn-outline-primary px-3 py-1 edit-btn" 
                                data-id="{{ $type->id }}" 
                                data-name="{{ $type->name }}" 
                                data-days="{{ $type->days_allowed }}"
                                data-active="{{ $type->is_active }}">
                                <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                            </button>
                            
                            <form action="{{ route('admin.leave-types.destroy', $type->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger px-3 py-1" onclick="return confirm('Are you sure?')">
                                    <i class="fa-solid fa-trash me-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">No leave types defined.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-lg-4 order-1 order-lg-2">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="mb-0 fw-bold text-dark fs-6" id="formTitle"><i class="fa-solid fa-plus-circle text-primary me-2"></i>Add Leave Type</h5>
            </div>
            <div class="card-body p-3 p-md-4">
                <form id="ltForm" action="{{ route('admin.leave-types.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="POST" id="formMethod">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Name</label>
                        <input type="text" name="name" id="ltName" class="form-control" placeholder="e.g. Annual Leave" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Days Allowed (per year)</label>
                        <input type="number" name="days_allowed" id="ltDays" class="form-control" placeholder="e.g. 12" required min="0">
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" id="ltActive" class="form-check-input" value="1" checked>
                        <label class="form-check-label small fw-semibold text-dark" for="ltActive">Is Active</label>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success flex-grow-1 py-2 fw-bold" id="submitBtn">Save</button>
                        <button type="button" class="btn btn-secondary d-none px-3" id="cancelBtn">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('.edit-btn').click(function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        let days = $(this).data('days');
        let active = $(this).data('active');
        
        $('#ltName').val(name);
        $('#ltDays').val(days);
        $('#ltActive').prop('checked', active == 1);
        
        $('#formTitle').html('<i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Leave Type');
        $('#submitBtn').text('Update');
        $('#cancelBtn').removeClass('d-none');
        
        $('#ltForm').attr('action', "{{ url('admin/leave-types') }}/" + id);
        $('#formMethod').val('PUT');

        // Scroll to form smoothly on mobile
        if ($(window).width() < 992) {
            $('html, body').animate({
                scrollTop: $("#ltForm").offset().top - 100
            }, 300);
        }
    });
    
    $('#cancelBtn').click(function() {
        $('#ltName').val('');
        $('#ltDays').val('');
        $('#ltActive').prop('checked', true);
        
        $('#formTitle').html('<i class="fa-solid fa-plus-circle text-primary me-2"></i>Add Leave Type');
        $('#submitBtn').text('Save');
        $('#cancelBtn').addClass('d-none');
        
        $('#ltForm').attr('action', "{{ route('admin.leave-types.store') }}");
        $('#formMethod').val('POST');
    });
</script>
@endpush
