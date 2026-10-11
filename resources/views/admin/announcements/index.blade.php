@extends('layouts.admin')

@section('title', 'Broadcast Announcements')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0 text-gray-800 font-weight-bold">Broadcast Announcements</h2>
            <p class="text-muted mb-0">Send immediate notifications to all employees or specific teams</p>
        </div>
        <button class="btn btn-primary shadow-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
            <i class="fa fa-bullhorn me-2"></i> Send Notification
        </button>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="fa fa-exclamation-triangle me-2"></i> {{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-4">Announcement</th>
                            <th>Target</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($announcements as $announcement)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $announcement->title }}</div>
                                <div class="small text-muted text-truncate" style="max-width: 350px;">{{ $announcement->message }}</div>
                            </td>
                            <td>
                                @if($announcement->target_type === 'all')
                                    <span class="badge bg-indigo text-indigo border border-indigo-subtle rounded-pill px-3 py-1">
                                        <i class="fa fa-users me-1"></i> All Employees
                                    </span>
                                @elseif($announcement->target_type === 'department')
                                    <span class="badge bg-info text-dark rounded-pill px-3 py-1">
                                        <i class="fa fa-building me-1"></i> {{ $announcement->department->name ?? 'Team' }}
                                    </span>
                                @else
                                    <span class="badge bg-primary rounded-pill px-3 py-1" title="{{ $announcement->employees->pluck('name')->implode(', ') }}">
                                        <i class="fa fa-user me-1"></i> {{ $announcement->employees->count() }} Specific {{ Str::plural('Employee', $announcement->employees->count()) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($announcement->type === 'urgent')
                                    <span class="badge bg-danger rounded-pill px-3 py-1">
                                        <i class="fa fa-exclamation-circle me-1"></i> Urgent
                                    </span>
                                @elseif($announcement->type === 'warning')
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1">
                                        <i class="fa fa-exclamation-triangle me-1"></i> Warning
                                    </span>
                                @else
                                    <span class="badge bg-secondary rounded-pill px-3 py-1">
                                        <i class="fa fa-info-circle me-1"></i> Info
                                    </span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.announcements.toggle', $announcement->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $announcement->is_active ? 'btn-success' : 'btn-outline-secondary' }} rounded-pill px-3">
                                        {{ $announcement->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td class="small text-muted">
                                {{ $announcement->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="text-end pe-4">
                                <form action="{{ route('admin.announcements.destroy', $announcement->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa fa-bullhorn fa-3x mb-3 text-secondary opacity-50"></i>
                                <p class="mb-0">No broadcast announcements created yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Send Notification -->
<div class="modal fade" id="createAnnouncementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold">
                    <i class="fa fa-paper-plane me-2"></i> Send Immediate Broadcast Notification
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.announcements.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Notification Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g., Important Maintenance / Personal Notice" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Message Details</label>
                        <textarea name="message" class="form-control" rows="4" placeholder="Enter complete notification message..." required></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Urgency Level</label>
                            <select name="type" class="form-select" required>
                                <option value="info" selected>Info (Blue)</option>
                                <option value="warning">Warning (Amber)</option>
                                <option value="urgent">Urgent (Red Alert)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Target Audience</label>
                            <select name="target_type" id="targetTypeSelect" class="form-select" required onchange="toggleTargetSelect(this.value)">
                                <option value="all" selected>All Employees</option>
                                <option value="department">Specific Team / Department</option>
                                <option value="individual">Specific Employee(s)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3" id="departmentSelectWrapper" style="display: none;">
                        <label class="form-label font-weight-bold">Select Team / Department</label>
                        <select name="department_id" class="form-select">
                            <option value="">-- Choose Department --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3" id="employeeSelectWrapper" style="display: none;">
                        <label class="form-label font-weight-bold">Select Specific Employee(s)</label>
                        <div class="border rounded p-2" style="max-height: 180px; overflow-y: auto; background-color: #f8fafc;">
                            @foreach($employees as $emp)
                                <div class="form-check py-1 border-bottom border-light">
                                    <input class="form-check-input" type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" id="emp_check_{{ $emp->id }}">
                                    <label class="form-check-label d-flex justify-content-between pe-2 w-100 cursor-pointer" for="emp_check_{{ $emp->id }}">
                                        <span><strong>{{ $emp->name }}</strong> <small class="text-muted">({{ $emp->employee_id }})</small></span>
                                        <small class="text-secondary">{{ $emp->departmentRel->name ?? 'General' }}</small>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted">Select all employees who should receive this notification.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Expiration Date & Time (Optional)</label>
                        <input type="datetime-local" name="expires_at" class="form-control">
                        <small class="text-muted">Notification will automatically hide after this time.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa fa-paper-plane me-1"></i> Send Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleTargetSelect(target) {
        const deptWrapper = document.getElementById('departmentSelectWrapper');
        const empWrapper = document.getElementById('employeeSelectWrapper');
        
        if (target === 'department') {
            deptWrapper.style.display = 'block';
            empWrapper.style.display = 'none';
        } else if (target === 'individual') {
            deptWrapper.style.display = 'none';
            empWrapper.style.display = 'block';
        } else {
            deptWrapper.style.display = 'none';
            empWrapper.style.display = 'none';
        }
    }
</script>
@endsection
