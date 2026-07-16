@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Leave Types</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Days Allowed/Year</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaveTypes as $type)
                        <tr>
                            <td>{{ $type->id }}</td>
                            <td>{{ $type->name }}</td>
                            <td>{{ $type->days_allowed }}</td>
                            <td>
                                <span class="badge bg-{{ $type->is_active ? 'success' : 'danger' }}">{{ $type->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary edit-btn" 
                                    data-id="{{ $type->id }}" 
                                    data-name="{{ $type->name }}" 
                                    data-days="{{ $type->days_allowed }}"
                                    data-active="{{ $type->is_active }}">Edit</button>
                                
                                <form action="{{ route('admin.leave-types.destroy', $type->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No leave types defined.</td>
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
                <h5 id="formTitle">Add Leave Type</h5>
            </div>
            <div class="card-body">
                <form id="ltForm" action="{{ route('admin.leave-types.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="POST" id="formMethod">
                    
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" id="ltName" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label>Days Allowed (per year)</label>
                        <input type="number" name="days_allowed" id="ltDays" class="form-control" required min="0">
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" id="ltActive" class="form-check-input" value="1" checked>
                        <label class="form-check-label">Is Active</label>
                    </div>
                    
                    <button type="submit" class="btn btn-success" id="submitBtn">Save</button>
                    <button type="button" class="btn btn-secondary d-none" id="cancelBtn">Cancel</button>
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
        
        $('#formTitle').text('Edit Leave Type');
        $('#submitBtn').text('Update');
        $('#cancelBtn').removeClass('d-none');
        
        $('#ltForm').attr('action', "{{ url('admin/leave-types') }}/" + id);
        $('#formMethod').val('PUT');
    });
    
    $('#cancelBtn').click(function() {
        $('#ltName').val('');
        $('#ltDays').val('');
        $('#ltActive').prop('checked', true);
        
        $('#formTitle').text('Add Leave Type');
        $('#submitBtn').text('Save');
        $('#cancelBtn').addClass('d-none');
        
        $('#ltForm').attr('action', "{{ route('admin.leave-types.store') }}");
        $('#formMethod').val('POST');
    });
</script>
@endpush
