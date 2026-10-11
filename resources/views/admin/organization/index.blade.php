@extends('layouts.admin')

@section('content')
<style>
    .org-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e9ecef;
        padding: 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .org-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px -8px rgba(79, 70, 229, 0.15);
        border-color: #cbd5e1;
    }
    .org-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--brand-primary);
        opacity: 0;
        transition: opacity 0.3s;
    }
    .org-card:hover::before {
        opacity: 1;
    }
    .card-header-flex {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 12px;
    }
    .org-icon {
        width: 38px;
        height: 38px;
        background: #f1f5f9;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--brand-primary);
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .org-title-wrap {
        flex-grow: 1;
        overflow: hidden;
    }
    .org-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--brand-dark);
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .org-meta {
        font-size: 0.75rem;
        color: #64748b;
    }
    .org-actions {
        display: flex;
        gap: 6px;
        margin-top: auto;
    }
    .org-btn {
        flex: 1;
        padding: 5px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-align: center;
        transition: all 0.2s;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
    }
    .org-btn:hover { background: #f1f5f9; color: #1e293b; border-color: #cbd5e1; }
    .org-btn-danger { color: #ef4444; }
    .org-btn-danger:hover { background: #fef2f2; border-color: #fecaca; }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 4px;
    }
    .status-active .status-dot { background: #10b981; box-shadow: 0 0 6px #10b981; }
    .status-inactive .status-dot { background: #ef4444; }
    .status-text { font-size: 0.7rem; font-weight: 600; }
    .status-active .status-text { color: #10b981; }
    .status-inactive .status-text { color: #ef4444; }
    
    .form-control { border-radius: 8px; border: 1px solid #cbd5e1; padding: 10px 14px; font-size: 0.9rem; }
    .form-control:focus { border-color: var(--brand-primary); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
</style>

<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold text-dark mb-1">{{ $title }}</h4>
            <p class="text-muted small mb-0">Manage and organize your company {{ strtolower($title) }}</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-light text-dark border px-3 py-2" style="font-size: 0.85rem;">{{ $items->count() }} Total</span>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOrgModal" onclick="resetForm()">
                <i class="fa-solid fa-plus me-1"></i> Add {{ Str::singular($title) }}
            </button>
        </div>
    </div>
</div>

@if($items->isEmpty())
    <div class="text-center py-5 bg-white rounded-4 border">
        <i class="fa-regular fa-folder-open text-muted mb-3" style="font-size: 2.5rem;"></i>
        <h6 class="fw-bold text-dark">No {{ $title }} Found</h6>
        <p class="text-muted small">You haven't added any {{ strtolower($title) }} yet. Create one using the button above.</p>
    </div>
@else
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3">
        @foreach($items as $item)
        <div class="col">
            <div class="org-card">
                <div class="card-header-flex">
                    <div class="org-icon">
                        <i class="fa-solid {{ $type == 'branches' ? 'fa-building' : 'fa-sitemap' }}"></i>
                    </div>
                    <div class="org-title-wrap">
                        <div class="org-title" title="{{ $item->name }}">{{ $item->name }}</div>
                        <div class="status-{{ $item->is_active ? 'active' : 'inactive' }} d-flex align-items-center">
                            <span class="status-dot"></span>
                            <span class="status-text">{{ $item->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="org-meta mb-3">
                    @if($type == 'branches' && $item->address)
                        <i class="fa-solid fa-location-dot me-1 text-muted"></i> <span title="{{ $item->address }}">{{ Str::limit($item->address, 30) }}</span>
                    @else
                        <i class="fa-solid fa-fingerprint me-1 text-muted"></i> ID: {{ $item->id }}
                    @endif
                </div>
                
                <div class="org-actions">
                    <button type="button" class="org-btn edit-btn"
                        data-id="{{ $item->id }}" 
                        data-name="{{ $item->name }}" 
                        @if($type == 'branches') data-address="{{ $item->address }}" @endif
                        data-active="{{ $item->is_active }}">
                        <i class="fa-solid fa-pen"></i> Edit
                    </button>
                    
                    <form action="{{ route('admin.organization.destroy', [$type, $item->id]) }}" method="POST" class="d-inline" style="flex: 1;">
                        @csrf @method('DELETE')
                        <button type="submit" class="org-btn org-btn-danger w-100" onclick="return confirm('Delete this {{ strtolower(Str::singular($title)) }}?');">
                            <i class="fa-solid fa-trash-can"></i> Del
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif

<!-- Add/Edit Modal -->
<div class="modal fade" id="addOrgModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle"><i class="fa-solid fa-plus-circle text-primary me-2"></i>Add {{ Str::singular($title) }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3 pb-4">
                <form id="orgForm" action="{{ route('admin.organization.store', $type) }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="POST" id="formMethod">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary text-uppercase" style="font-size: 0.75rem;">Name</label>
                        <input type="text" name="name" id="orgName" class="form-control" placeholder="Enter name" required>
                    </div>
                    
                    @if($type == 'branches')
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary text-uppercase" style="font-size: 0.75rem;">Address</label>
                        <textarea name="address" id="orgAddress" class="form-control" rows="3" placeholder="Full address"></textarea>
                    </div>
                    @endif
                    
                    <div class="mb-4 form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="orgActive" value="1" checked style="width: 2.5em; height: 1.25em; cursor: pointer;">
                        <label class="form-check-label ms-2 fw-medium text-dark mt-1" for="orgActive" style="cursor: pointer;">Set as Active</label>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-bold py-2" id="submitBtn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function resetForm() {
        $('#orgName').val('');
        $('#orgActive').prop('checked', true);
        
        @if($type == 'branches')
        $('#orgAddress').val('');
        @endif
        
        $('#modalTitle').html('<i class="fa-solid fa-plus-circle text-primary me-2"></i>Add {{ Str::singular($title) }}');
        $('#submitBtn').text('Save');
        
        $('#orgForm').attr('action', "{{ route('admin.organization.store', $type) }}");
        $('#formMethod').val('POST');
    }

    $('.edit-btn').click(function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        let active = $(this).data('active');
        
        $('#orgName').val(name);
        $('#orgActive').prop('checked', active == 1);
        
        @if($type == 'branches')
        $('#orgAddress').val($(this).data('address'));
        @endif
        
        $('#modalTitle').html('<i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit {{ Str::singular($title) }}');
        $('#submitBtn').text('Update');
        
        $('#orgForm').attr('action', "{{ url('admin/organization') }}/{{ $type }}/" + id);
        $('#formMethod').val('PUT');
        
        // Open Modal
        var myModal = new bootstrap.Modal(document.getElementById('addOrgModal'));
        myModal.show();
    });
</script>
@endpush
