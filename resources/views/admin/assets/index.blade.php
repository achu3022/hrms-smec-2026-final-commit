@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Assets Inventory</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-sm text-sm">
                    <thead>
                        <tr>
                            <th>SN</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Current Assignee</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $asset)
                        <tr>
                            <td>{{ $asset->serial_number }}</td>
                            <td>{{ $asset->name }}</td>
                            <td>{{ $asset->type }}</td>
                            <td>
                                @php
                                    $color = 'success';
                                    if($asset->status == 'Assigned') $color = 'primary';
                                    if($asset->status == 'Maintenance') $color = 'warning';
                                    if($asset->status == 'Retired') $color = 'danger';
                                @endphp
                                <span class="badge bg-{{ $color }}">{{ $asset->status }}</span>
                            </td>
                            <td>
                                @if($asset->status == 'Assigned' && $asset->assignments->isNotEmpty())
                                    {{ $asset->assignments->first()->employee->name ?? 'Unknown' }}
                                    <br>
                                    <small class="text-muted">Since: {{ $asset->assignments->first()->assigned_date }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($asset->status == 'Available')
                                    <button class="btn btn-sm btn-info assign-btn" 
                                            data-id="{{ $asset->id }}" 
                                            data-name="{{ $asset->name }}"
                                            data-type="{{ $asset->type }}"
                                            data-serial="{{ $asset->serial_number }}"
                                            data-status="{{ $asset->status }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#assignModal">
                                        <i class="fa fa-handshake"></i> Assign
                                    </button>
                                @elseif($asset->status == 'Assigned' && $asset->assignments->isNotEmpty())
                                    <button class="btn btn-sm btn-warning return-btn" 
                                            data-assignment-id="{{ $asset->assignments->first()->id }}"
                                            data-name="{{ $asset->name }}"
                                            data-serial="{{ $asset->serial_number }}"
                                            data-employee="{{ $asset->assignments->first()->employee->name ?? 'Unknown' }}"
                                            data-assigned-date="{{ $asset->assignments->first()->assigned_date }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#returnModal">
                                        <i class="fa fa-undo"></i> Return
                                    </button>
                                @endif
                                
                                <form action="{{ route('admin.assets.destroy', $asset->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this asset?')"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No assets found.</td>
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
                <h5>Add Asset</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.assets.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label>Asset Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. MacBook Pro M2" required>
                    </div>
                    <div class="mb-3">
                        <label>Asset Type</label>
                        <select name="type" class="form-select" required>
                            <option value="Laptop">Laptop</option>
                            <option value="Phone">Phone</option>
                            <option value="Monitor">Monitor</option>
                            <option value="Mouse/Keyboard">Mouse/Keyboard</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Serial Number</label>
                        <input type="text" name="serial_number" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Purchase Date</label>
                        <input type="date" name="purchase_date" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-success w-100">Save Asset</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Assign Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="assignForm" method="POST">
          @csrf
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title"><i class="fa fa-handshake me-2"></i>Assign Asset</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <!-- Asset Details Section -->
            <div class="alert alert-info mb-3" id="assetDetailsSection" style="display: none;">
                <h6 class="fw-bold mb-2"><i class="fa fa-info-circle me-2"></i>Asset Details</h6>
                <div class="row g-2 small">
                    <div class="col-6"><strong>Name:</strong> <span id="modalAssetName"></span></div>
                    <div class="col-6"><strong>Type:</strong> <span id="modalAssetType"></span></div>
                    <div class="col-6"><strong>Serial Number:</strong> <span id="modalAssetSerial"></span></div>
                    <div class="col-6"><strong>Status:</strong> <span id="modalAssetStatus"></span></div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label"><i class="fa fa-user me-1"></i>Employee *</label>
                <select name="employee_id" class="form-select" required>
                    <option value="">Select Employee</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->employee_id }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fa fa-calendar me-1"></i>Assigned Date *</label>
                <input type="date" name="assigned_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fa fa-clipboard me-1"></i>Condition Given (Optional)</label>
                <input type="text" name="condition_given" class="form-control" placeholder="e.g., Good, New, Minor scratches">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary"><i class="fa fa-check me-1"></i>Assign Asset</button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- Return Modal -->
<div class="modal fade" id="returnModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="returnForm" method="POST">
          @csrf
          <div class="modal-header bg-warning text-dark">
            <h5 class="modal-title"><i class="fa fa-undo me-2"></i>Return Asset</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <!-- Asset Return Details Section -->
            <div class="alert alert-warning mb-3" id="returnAssetDetailsSection" style="display: none;">
                <h6 class="fw-bold mb-2"><i class="fa fa-info-circle me-2"></i>Asset & Assignment Details</h6>
                <div class="row g-2 small">
                    <div class="col-6"><strong>Asset:</strong> <span id="returnAssetName"></span></div>
                    <div class="col-6"><strong>Employee:</strong> <span id="returnEmployeeName"></span></div>
                    <div class="col-6"><strong>Assigned Date:</strong> <span id="returnAssignedDate"></span></div>
                    <div class="col-6"><strong>Serial:</strong> <span id="returnAssetSerial"></span></div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label"><i class="fa fa-calendar me-1"></i>Returned Date *</label>
                <input type="date" name="returned_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fa fa-clipboard me-1"></i>Condition Returned (Optional)</label>
                <input type="text" name="condition_returned" class="form-control" placeholder="e.g., Good, Damaged, Needs repair">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-warning"><i class="fa fa-check me-1"></i>Return Asset</button>
          </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
    // Assign Button Click Handler
    $('.assign-btn').click(function() {
        let assetId = $(this).data('id');
        let assetName = $(this).data('name');
        let assetType = $(this).data('type');
        let assetSerial = $(this).data('serial');
        let assetStatus = $(this).data('status');
        
        // Set form action
        $('#assignForm').attr('action', "{{ url('admin/assets') }}/" + assetId + "/assign");
        
        // Populate asset details
        $('#modalAssetName').text(assetName);
        $('#modalAssetType').text(assetType);
        $('#modalAssetSerial').text(assetSerial);
        $('#modalAssetStatus').html('<span class="badge bg-success">' + assetStatus + '</span>');
        
        // Show asset details section
        $('#assetDetailsSection').slideDown();
    });
    
    // Return Button Click Handler
    $('.return-btn').click(function() {
        let assignmentId = $(this).data('assignment-id');
        let assetName = $(this).data('name');
        let assetSerial = $(this).data('serial');
        let employeeName = $(this).data('employee');
        let assignedDate = $(this).data('assigned-date');
        
        // Set form action
        $('#returnForm').attr('action', "{{ url('admin/assets/return') }}/" + assignmentId);
        
        // Populate return details
        $('#returnAssetName').text(assetName);
        $('#returnAssetSerial').text(assetSerial);
        $('#returnEmployeeName').text(employeeName);
        $('#returnAssignedDate').text(assignedDate);
        
        // Show return details section
        $('#returnAssetDetailsSection').slideDown();
    });
    
    // Reset modals when closed
    $('#assignModal').on('hidden.bs.modal', function () {
        $('#assetDetailsSection').hide();
    });
    
    $('#returnModal').on('hidden.bs.modal', function () {
        $('#returnAssetDetailsSection').hide();
    });
</script>
@endpush
