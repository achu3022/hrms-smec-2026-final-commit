@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>{{ $title }} List</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            @if($type == 'branches') <th>Address</th> @endif
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->name }}</td>
                            @if($type == 'branches') <td>{{ $item->address }}</td> @endif
                            <td>
                                <span class="badge bg-{{ $item->is_active ? 'success' : 'danger' }}">{{ $item->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary edit-btn" 
                                    data-id="{{ $item->id }}" 
                                    data-name="{{ $item->name }}" 
                                    @if($type == 'branches') data-address="{{ $item->address }}" @endif
                                    data-active="{{ $item->is_active }}">Edit</button>
                                
                                <form action="{{ route('admin.organization.destroy', [$type, $item->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No records found.</td>
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
                <h5 id="formTitle">Add New {{ $title }}</h5>
            </div>
            <div class="card-body">
                <form id="orgForm" action="{{ route('admin.organization.store', $type) }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="POST" id="formMethod">
                    
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" id="orgName" class="form-control" required>
                    </div>
                    
                    @if($type == 'branches')
                    <div class="mb-3">
                        <label>Address</label>
                        <textarea name="address" id="orgAddress" class="form-control" rows="3"></textarea>
                    </div>
                    @endif
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" id="orgActive" class="form-check-input" value="1" checked>
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
        let active = $(this).data('active');
        
        $('#orgName').val(name);
        $('#orgActive').prop('checked', active == 1);
        
        @if($type == 'branches')
        $('#orgAddress').val($(this).data('address'));
        @endif
        
        $('#formTitle').text('Edit {{ $title }}');
        $('#submitBtn').text('Update');
        $('#cancelBtn').removeClass('d-none');
        
        $('#orgForm').attr('action', "{{ url('admin/organization') }}/{{ $type }}/" + id);
        $('#formMethod').val('PUT');
    });
    
    $('#cancelBtn').click(function() {
        $('#orgName').val('');
        $('#orgActive').prop('checked', true);
        
        @if($type == 'branches')
        $('#orgAddress').val('');
        @endif
        
        $('#formTitle').text('Add New {{ $title }}');
        $('#submitBtn').text('Save');
        $('#cancelBtn').addClass('d-none');
        
        $('#orgForm').attr('action', "{{ route('admin.organization.store', $type) }}");
        $('#formMethod').val('POST');
    });
</script>
@endpush
