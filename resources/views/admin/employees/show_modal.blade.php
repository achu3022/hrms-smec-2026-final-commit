<style>
    .modal-header-custom {
        background: linear-gradient(135deg, #00aa9e 0%, #0070bc 100%);
        color: white;
        padding: 40px 30px;
        position: relative;
    }
    .modal-close-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .modal-close-btn:hover {
        background: rgba(255,255,255,0.4);
    }
    .profile-img-container {
        position: absolute;
        bottom: -50px;
        left: 40px;
        padding: 4px;
        background: linear-gradient(135deg, #00aa9e, #0070bc);
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        z-index: 2;
    }
    .profile-img {
        width: 120px;
        height: 120px;
        border-radius: 8px;
        object-fit: cover;
        border: 4px solid white;
    }
    .profile-placeholder {
        width: 120px;
        height: 120px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f8fafb, #e8f0f8);
        border: 4px solid white;
    }
    .profile-info {
        margin-left: 190px;
        min-height: 50px;
    }
    .custom-tabs {
        border-bottom: 2px solid #e8f4f8;
        padding-left: 30px;
    }
    .custom-tabs .nav-link {
        color: #666;
        font-weight: 600;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 15px 25px;
        margin-bottom: -2px;
        transition: all 0.2s;
    }
    .custom-tabs .nav-link:hover {
        color: #00aa9e;
        background: #f8fafb;
    }
    .custom-tabs .nav-link.active {
        color: #00aa9e;
        border-bottom-color: #00aa9e;
        background: transparent;
    }
    .info-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        border: 1px solid #f0f4f8;
    }
    .info-card-title {
        color: #2f3092;
        font-weight: 700;
        margin-bottom: 20px;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
    }
    .info-card-title i {
        color: #00aa9e;
        margin-right: 10px;
        font-size: 1.2rem;
    }
    .info-label {
        font-size: 0.8rem;
        color: #888;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }
    .info-value {
        font-size: 1rem;
        color: #333;
        font-weight: 500;
        margin-bottom: 20px;
    }
    .badge-status {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .modal-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 18px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .modal-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .btn-modal-email  { background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.4); }
    .btn-modal-email:hover  { background: white; color: #0070bc; }
    .btn-modal-edit   { background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.4); }
    .btn-modal-edit:hover   { background: white; color: #00aa9e; }
    .btn-modal-idcard { background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.4); }
    .btn-modal-idcard:hover { background: white; color: #2f3092; }
    .btn-modal-cert   { background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.4); }
    .btn-modal-cert:hover   { background: white; color: #6f42c1; }
    .btn-modal-delete { background: rgba(220,53,69,0.25); color: white; border: 1px solid rgba(220,53,69,0.5); }
    .btn-modal-delete:hover { background: #dc3545; color: white; }
</style>

<div class="modal-header-custom">
    <button type="button" class="modal-close-btn" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
    <div class="profile-img-container">
        @if($employee->photo)
            <img src="{{ Storage::url($employee->photo) }}" class="profile-img">
        @else
            <div class="profile-placeholder">
                <i class="fa fa-user fa-4x text-secondary"></i>
            </div>
        @endif
    </div>
    <div class="profile-info">
        <h2 class="mb-1 fw-bold text-white">{{ $employee->name }}</h2>
        <p class="mb-3 fs-5 text-white-50"><i class="fa fa-id-badge me-2"></i>{{ $employee->employee_id }} &nbsp;|&nbsp; <i class="fa fa-briefcase me-2"></i>{{ $employee->designationRel->name ?? $employee->designation }}</p>
        <div class="d-flex flex-wrap gap-2">
            <a href="mailto:{{ $employee->company_email }}" class="modal-action-btn btn-modal-email">
                <i class="fa fa-envelope"></i> Email
            </a>
            <a href="{{ route('admin.employees.edit', $employee->id) }}" class="modal-action-btn btn-modal-edit">
                <i class="fa fa-edit"></i> Edit
            </a>
            {{--
            <a href="{{ route('admin.employees.id-card', $employee->id) }}" target="_blank" class="modal-action-btn btn-modal-idcard">
                <i class="fa fa-id-card"></i> ID Card
            </a>
            <a href="{{ route('admin.employees.experience-certificate', $employee->id) }}" target="_blank" class="modal-action-btn btn-modal-cert">
                <i class="fa fa-certificate"></i> Certificate
            </a>
            --}}
            <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Delete this employee?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="modal-action-btn btn-modal-delete">
                    <i class="fa fa-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>
</div>

<div class="pt-5 mt-3 bg-light">
    <ul class="nav nav-tabs custom-tabs" id="employeeTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">Overview</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="docs-tab" data-bs-toggle="tab" data-bs-target="#docs" type="button" role="tab">Identity & Bank</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="edu-tab" data-bs-toggle="tab" data-bs-target="#edu" type="button" role="tab">Education & Experience</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="assets-tab" data-bs-toggle="tab" data-bs-target="#assets" type="button" role="tab">Assets</button>
        </li>
    </ul>

    <div class="tab-content p-4" id="employeeTabsContent">
        <!-- Overview Tab -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-card">
                        <div class="info-card-title"><i class="fa fa-building"></i> Employment Details</div>
                        <div class="row">
                            <div class="col-sm-6"><div class="info-label">Department</div><div class="info-value">{{ $employee->departmentRel->name ?? $employee->department }}</div></div>
                            <div class="col-sm-6"><div class="info-label">Status</div><div class="info-value"><span class="badge {{ $employee->status == 'Active' ? 'bg-success' : 'bg-danger' }} badge-status">{{ $employee->status }}</span></div></div>
                            <div class="col-sm-6"><div class="info-label">Joining Date</div><div class="info-value">{{ $employee->joining_date ?? 'N/A' }}</div></div>
                            <div class="col-sm-6"><div class="info-label">Employment Type</div><div class="info-value">{{ $employee->employment_type ?? 'N/A' }}</div></div>
                            <div class="col-sm-6"><div class="info-label">Reporting Manager</div><div class="info-value">{{ $employee->reporting_manager ?? 'N/A' }}</div></div>
                            <div class="col-sm-6"><div class="info-label">Work Location</div><div class="info-value">{{ $employee->work_location ?? 'N/A' }}</div></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card h-100">
                        <div class="info-card-title"><i class="fa fa-envelope"></i> Contact Information</div>
                        <div class="row">
                            <div class="col-sm-6"><div class="info-label">Company Email</div><div class="info-value">{{ $employee->company_email ?? 'N/A' }}</div></div>
                            <div class="col-sm-6"><div class="info-label">Personal Email</div><div class="info-value">{{ $employee->personal_email ?? 'N/A' }}</div></div>
                            <div class="col-sm-6"><div class="info-label">Office Phone</div><div class="info-value">{{ $employee->office_phone ?? 'N/A' }}</div></div>
                            <div class="col-sm-6"><div class="info-label">Personal Phone</div><div class="info-value">{{ $employee->personal_phone ?? 'N/A' }}</div></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="info-card">
                        <div class="info-card-title"><i class="fa fa-user"></i> Personal Information</div>
                        <div class="row">
                            <div class="col-md-3 col-sm-6"><div class="info-label">DOB</div><div class="info-value">{{ $employee->dob ?? 'N/A' }}</div></div>
                            <div class="col-md-3 col-sm-6"><div class="info-label">Gender</div><div class="info-value">{{ $employee->gender ?? 'N/A' }}</div></div>
                            <div class="col-md-3 col-sm-6"><div class="info-label">Blood Group</div><div class="info-value">{{ $employee->blood_group ?? 'N/A' }}</div></div>
                            <div class="col-md-3 col-sm-6"><div class="info-label">Marital Status</div><div class="info-value">{{ $employee->marital_status ?? 'N/A' }}</div></div>
                            <div class="col-md-6"><div class="info-label">Present Address</div><div class="info-value">{{ $employee->present_address ?? 'N/A' }}</div></div>
                            <div class="col-md-6"><div class="info-label">Permanent Address</div><div class="info-value">{{ $employee->permanent_address ?? 'N/A' }}</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Identity & Bank Tab -->
        <div class="tab-pane fade" id="docs" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-card h-100">
                        <div class="info-card-title"><i class="fa fa-id-card"></i> Identity Documents</div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <tbody>
                                    <tr>
                                        <td class="text-muted fw-semibold" style="width: 35%;">Aadhaar</td>
                                        <td class="fw-medium">{{ $employee->aadhaar ?? 'N/A' }}</td>
                                        <td class="text-end">@if($employee->aadhaar_file) <a href="{{ Storage::url($employee->aadhaar_file) }}" target="_blank" class="btn btn-sm btn-light text-primary"><i class="fa fa-eye"></i> View</a> @endif</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">PAN</td>
                                        <td class="fw-medium">{{ $employee->pan ?? 'N/A' }}</td>
                                        <td class="text-end">@if($employee->pan_file) <a href="{{ Storage::url($employee->pan_file) }}" target="_blank" class="btn btn-sm btn-light text-primary"><i class="fa fa-eye"></i> View</a> @endif</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Passport</td>
                                        <td class="fw-medium">{{ $employee->passport ?? 'N/A' }}</td>
                                        <td class="text-end">@if($employee->passport_file) <a href="{{ Storage::url($employee->passport_file) }}" target="_blank" class="btn btn-sm btn-light text-primary"><i class="fa fa-eye"></i> View</a> @endif</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Driving License</td>
                                        <td class="fw-medium">{{ $employee->driving_license ?? 'N/A' }}</td>
                                        <td class="text-end">@if($employee->license_file) <a href="{{ Storage::url($employee->license_file) }}" target="_blank" class="btn btn-sm btn-light text-primary"><i class="fa fa-eye"></i> View</a> @endif</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card h-100">
                        <div class="info-card-title"><i class="fa fa-bank"></i> Bank Details</div>
                        <div class="row">
                            <div class="col-sm-6"><div class="info-label">Bank Name</div><div class="info-value">{{ $employee->bank_name ?? 'N/A' }}</div></div>
                            <div class="col-sm-6"><div class="info-label">Account Holder</div><div class="info-value">{{ $employee->account_holder ?? 'N/A' }}</div></div>
                            <div class="col-sm-6"><div class="info-label">Account Number</div><div class="info-value">{{ $employee->account_number ?? 'N/A' }}</div></div>
                            <div class="col-sm-6"><div class="info-label">IFSC Code</div><div class="info-value">{{ $employee->ifsc ?? 'N/A' }}</div></div>
                            <div class="col-sm-6"><div class="info-label">Branch</div><div class="info-value">{{ $employee->branch ?? 'N/A' }}</div></div>
                            <div class="col-sm-6"><div class="info-label">Bank Document</div><div class="info-value">@if($employee->bank_document) <a href="{{ Storage::url($employee->bank_document) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fa fa-external-link-alt"></i> View File</a> @else N/A @endif</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Education & Experience Tab -->
        <div class="tab-pane fade" id="edu" role="tabpanel">
            <div class="info-card mb-4">
                <div class="info-card-title"><i class="fa fa-graduation-cap"></i> Education</div>
                @if($employee->education->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted">
                            <tr><th>Qualification</th><th>Course</th><th>Institution</th><th>Year</th><th>% / CGPA</th><th class="text-end">Document</th></tr>
                        </thead>
                        <tbody>
                            @foreach($employee->education as $edu)
                            <tr>
                                <td><span class="fw-bold text-dark">{{ $edu->qualification }}</span></td>
                                <td>{{ $edu->course }}</td>
                                <td>{{ $edu->institution }}</td>
                                <td>{{ $edu->year }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $edu->percentage }}</span></td>
                                <td class="text-end">@if($edu->certificate) <a href="{{ Storage::url($edu->certificate) }}" target="_blank" class="btn btn-sm btn-light text-primary py-1"><i class="fa fa-eye"></i> View</a> @else <span class="text-muted">-</span> @endif</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted"><i class="fa fa-folder-open fs-3 mb-2 opacity-50"></i><p class="mb-0">No education details found.</p></div>
                @endif
            </div>

            <div class="info-card mb-4">
                <div class="info-card-title"><i class="fa fa-briefcase"></i> Experience</div>
                @if($employee->experience->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted">
                            <tr><th>Company</th><th>Designation</th><th>Duration</th><th>Years</th><th class="text-end">Document</th></tr>
                        </thead>
                        <tbody>
                            @foreach($employee->experience as $exp)
                            <tr>
                                <td><span class="fw-bold text-dark">{{ $exp->company }}</span></td>
                                <td>{{ $exp->designation }}</td>
                                <td>{{ $exp->from_date }} <i class="fa fa-arrow-right mx-1 text-muted fs-7"></i> {{ $exp->to_date }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $exp->years }} yrs</span></td>
                                <td class="text-end">@if($exp->certificate) <a href="{{ Storage::url($exp->certificate) }}" target="_blank" class="btn btn-sm btn-light text-primary py-1"><i class="fa fa-eye"></i> View</a> @else <span class="text-muted">-</span> @endif</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted"><i class="fa fa-folder-open fs-3 mb-2 opacity-50"></i><p class="mb-0">No experience details found.</p></div>
                @endif
            </div>
            
            <div class="info-card">
                <div class="info-card-title"><i class="fa fa-certificate"></i> Certifications</div>
                @if($employee->certificates->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted">
                            <tr><th>Certificate Name</th><th>Issued By</th><th>Issue Date</th><th>Expiry Date</th><th class="text-end">Document</th></tr>
                        </thead>
                        <tbody>
                            @foreach($employee->certificates as $cert)
                            <tr>
                                <td><span class="fw-bold text-dark">{{ $cert->certificate_name }}</span></td>
                                <td>{{ $cert->issued_by }}</td>
                                <td>{{ $cert->issued_date }}</td>
                                <td>{{ $cert->expiry_date }}</td>
                                <td class="text-end">@if($cert->file) <a href="{{ Storage::url($cert->file) }}" target="_blank" class="btn btn-sm btn-light text-primary py-1"><i class="fa fa-eye"></i> View</a> @else <span class="text-muted">-</span> @endif</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted"><i class="fa fa-folder-open fs-3 mb-2 opacity-50"></i><p class="mb-0">No certifications found.</p></div>
                @endif
            </div>
        </div>

        <!-- Assets Tab -->
        <div class="tab-pane fade" id="assets" role="tabpanel">

            {{-- Provided Assets from Registration --}}
            @if($employee->provided_assets && count(array_filter($employee->provided_assets)))
            <div class="info-card mb-4">
                <div class="info-card-title"><i class="fa fa-box-open"></i> Assets Provided at Joining</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($employee->provided_assets as $key => $value)
                        @if($value && $value !== 'null' && $key !== 'sim_number' && $key !== 'other_details')
                            <span class="badge px-3 py-2 fs-6" style="background:linear-gradient(135deg,#2f3092,#0070bc);color:white;border-radius:8px;">
                                <i class="fa fa-check-circle me-1"></i>
                                {{ ucfirst(str_replace('_', ' ', $key)) }}
                            </span>
                        @endif
                    @endforeach
                    @if(!empty($employee->provided_assets['sim_number']))
                        <span class="badge px-3 py-2 fs-6" style="background:linear-gradient(135deg,#0070bc,#00aa9e);color:white;border-radius:8px;">
                            <i class="fa fa-sim-card me-1"></i> SIM: {{ $employee->provided_assets['sim_number'] }}
                        </span>
                    @endif
                    @if(!empty($employee->provided_assets['other_details']))
                        <span class="badge px-3 py-2 fs-6 bg-secondary">
                            <i class="fa fa-info-circle me-1"></i> {{ $employee->provided_assets['other_details'] }}
                        </span>
                    @endif
                </div>
            </div>
            @endif

            {{-- Formally Assigned Assets --}}
            <div class="info-card">
                <div class="info-card-title"><i class="fa fa-laptop"></i> Formally Assigned Assets</div>
                @if($employee->assetAssignments && $employee->assetAssignments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted">
                            <tr>
                                <th>Asset Name</th>
                                <th>Type</th>
                                <th>Serial Number</th>
                                <th>Assigned Date</th>
                                <th>Condition Given</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employee->assetAssignments as $assignment)
                            <tr>
                                <td><span class="fw-bold text-dark">{{ $assignment->asset->name ?? 'N/A' }}</span></td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $assignment->asset->type ?? 'N/A' }}
                                    </span>
                                </td>
                                <td><code>{{ $assignment->asset->serial_number ?? 'N/A' }}</code></td>
                                <td>{{ $assignment->assigned_date ?? 'N/A' }}</td>
                                <td>{{ $assignment->condition_given ?? '—' }}</td>
                                <td>
                                    @if($assignment->returned_date)
                                        <span class="badge bg-secondary">
                                            <i class="fa fa-undo me-1"></i>Returned on {{ $assignment->returned_date }}
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            <i class="fa fa-check me-1"></i>In Possession
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="fa fa-desktop fs-3 mb-2 d-block opacity-50"></i>
                    <p class="mb-0">No formal asset assignments found.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
