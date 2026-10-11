@extends('layouts.admin')

@push('styles')
<!-- jQuery UI CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css">
<!-- SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
<!-- Cropper.js -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<style>
    .form-container { max-width: 100%; margin: 0 auto; background: transparent; }
    .card { border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.04); margin-bottom: 25px; border: 1px solid #eef0f2; }
    .card-header { background-color: #ffffff; border-bottom: 1px solid #f1f1f1; border-radius: 12px 12px 0 0 !important; padding: 20px 25px; }
    .card-header h5 { margin: 0; font-weight: 600; color: #333; font-size: 1.1rem; }
    .card-header i { color: #0d6efd; margin-right: 8px; }
    .card-body { padding: 25px; background-color: #ffffff; border-radius: 0 0 12px 12px; }
    .form-label { font-weight: 500; color: #555; font-size: 0.9rem; margin-bottom: 6px; }
    .section-title { font-weight: 600; color: #444; border-bottom: 2px solid #eef0f2; padding-bottom: 8px; margin-bottom: 20px; margin-top: 10px; font-size: 1.05rem;}
    #loader { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.9); z-index: 9999; text-align: center; padding-top: 20vh; }
    .dynamic-row { border: 1px solid #eef0f2; padding: 20px; margin-bottom: 15px; border-radius: 8px; position: relative; background-color: #fafbfc; }
    .remove-row { position: absolute; top: 15px; right: 15px; border-radius: 50%; width: 30px; height: 30px; padding: 0; line-height: 30px; text-align: center; }
    .btn-submit { border-radius: 8px; padding: 12px 30px; font-weight: 600; font-size: 1.1rem; box-shadow: 0 4px 6px rgba(25, 135, 84, 0.2); transition: all 0.3s; }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 12px rgba(25, 135, 84, 0.3); }
</style>
@endpush

@section('content')
<div id="loader">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <h5 class="mt-3 text-secondary">Updating employee information...</h5>
</div>

<div class="container-fluid py-4 form-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark mb-0">Edit Employee: {{ $employee->name }}</h3>
        <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back</a>
    </div>
    
    <form id="employeeForm" action="{{ route('admin.employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- Status & Role -->
        <div class="card">
            <div class="card-header"><h5><i class="fa-solid fa-shield-halved"></i> Account & Role</h5></div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="Active" {{ $employee->status == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ $employee->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="Resigned" {{ $employee->status == 'Resigned' ? 'selected' : '' }}>Resigned</option>
                            <option value="Terminated" {{ $employee->status == 'Terminated' ? 'selected' : '' }}>Terminated</option>
                            <option value="On Leave" {{ $employee->status == 'On Leave' ? 'selected' : '' }}>On Leave</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Teamleader</label>
                        <select name="is_teamleader" class="form-select">
                            <option value="0" {{ !$employee->is_teamleader ? 'selected' : '' }}>No</option>
                            <option value="1" {{ $employee->is_teamleader ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="card">
            <div class="card-header"><h5><i class="fa-solid fa-user"></i> Personal Information</h5></div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $employee->name }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Employee ID</label>
                        <input type="text" name="employee_id" class="form-control" value="{{ $employee->employee_id }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Department</label>
                        <select name="department_id" class="form-select">
                            <option value="">Select Department</option>
                            @php $departments = \App\Models\Department::all(); @endphp
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ $employee->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @php
                        $shiftDetails = $employee->getShiftDetails();
                        $standardShifts = ['9:00 AM - 6:00 PM', '8:00 AM - 5:00 PM', '10:00 AM - 7:00 PM', '11:00 AM - 8:00 PM'];
                        $isCustomShift = !in_array($employee->shift, $standardShifts);
                    @endphp
                    <div class="col-md-6">
                        <label class="form-label">Shift</label>
                        <select name="shift" id="edit_shift_select" class="form-select" onchange="toggleCustomShiftInput('edit')">
                            <option value="9:00 AM - 6:00 PM" {{ (!$isCustomShift && $employee->shift == '9:00 AM - 6:00 PM') ? 'selected' : '' }}>9:00 AM - 6:00 PM (Standard)</option>
                            <option value="8:00 AM - 5:00 PM" {{ (!$isCustomShift && $employee->shift == '8:00 AM - 5:00 PM') ? 'selected' : '' }}>8:00 AM - 5:00 PM (Early)</option>
                            <option value="10:00 AM - 7:00 PM" {{ (!$isCustomShift && $employee->shift == '10:00 AM - 7:00 PM') ? 'selected' : '' }}>10:00 AM - 7:00 PM (Late)</option>
                            <option value="11:00 AM - 8:00 PM" {{ (!$isCustomShift && $employee->shift == '11:00 AM - 8:00 PM') ? 'selected' : '' }}>11:00 AM - 8:00 PM (Evening)</option>
                            <option value="custom" {{ $isCustomShift ? 'selected' : '' }}>Custom Time Shift...</option>
                        </select>
                    </div>
                    <div class="col-md-6 {{ $isCustomShift ? '' : 'd-none' }}" id="edit_custom_shift_container">
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label text-muted small">Shift Start Time</label>
                                <input type="time" name="custom_shift_start" id="edit_custom_shift_start" class="form-control" value="{{ $shiftDetails['start_time'] }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted small">Shift End Time</label>
                                <input type="time" name="custom_shift_end" id="edit_custom_shift_end" class="form-control" value="{{ $shiftDetails['end_time'] }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Designation</label>
                        <input type="text" name="designation" class="form-control" value="{{ $employee->designation }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Company Email</label>
                        <input type="email" name="company_email" class="form-control" value="{{ $employee->company_email }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Personal Email</label>
                        <input type="email" name="personal_email" class="form-control" value="{{ $employee->personal_email }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Personal Mobile</label>
                        <input type="text" name="personal_phone" class="form-control" value="{{ $employee->personal_phone }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Office Mobile</label>
                        <input type="text" name="office_phone" class="form-control" value="{{ $employee->office_phone }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="form-control" value="{{ $employee->dob }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="Male" {{ $employee->gender == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ $employee->gender == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ $employee->gender == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Blood Group</label>
                        <select name="blood_group" class="form-select">
                            @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                <option value="{{ $bg }}" {{ $employee->blood_group == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Marital Status</label>
                        <select name="marital_status" class="form-select">
                            <option value="">Select</option>
                            <option value="Single" {{ $employee->marital_status == 'Single' ? 'selected' : '' }}>Single</option>
                            <option value="Married" {{ $employee->marital_status == 'Married' ? 'selected' : '' }}>Married</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Employee Photo (Leave blank to keep current)</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                        @if($employee->photo)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $employee->photo) }}" height="50" class="rounded">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Addresses -->
        <div class="card">
            <div class="card-header"><h5><i class="fa-solid fa-map-marker-alt"></i> Address Details</h5></div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Present Address</label>
                        <textarea name="present_address" id="present_address" class="form-control" rows="4">{{ $employee->present_address }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label d-flex justify-content-between">
                            Permanent Address
                            <div class="form-check ms-3 fw-normal">
                                <input class="form-check-input" type="checkbox" id="sameAddress">
                                <label class="form-check-label text-muted" for="sameAddress">Same as Present</label>
                            </div>
                        </label>
                        <textarea name="permanent_address" id="permanent_address" class="form-control" rows="4">{{ $employee->permanent_address }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Emergency Contact & Identity -->
        <div class="card">
            <div class="card-header"><h5><i class="fa-solid fa-id-card"></i> Emergency Contact & Identity Details</h5></div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-12"><h6 class="section-title">Emergency Contact</h6></div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="emergency_name" class="form-control" value="{{ $employee->emergency_name }}">
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label">Relationship</label>
                        <select name="emergency_relation" class="form-select">
                            @foreach(['Father', 'Mother', 'Spouse', 'Brother', 'Sister', 'Friend', 'Other'] as $rel)
                                <option value="{{ $rel }}" {{ $employee->emergency_relation == $rel ? 'selected' : '' }}>{{ $rel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label">Mobile Number</label>
                        <input type="text" name="emergency_phone" class="form-control" value="{{ $employee->emergency_phone }}">
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label">Alternate Mobile</label>
                        <input type="text" name="emergency_alt_phone" class="form-control" value="{{ $employee->emergency_alt_phone }}">
                    </div>

                    <div class="col-md-12"><h6 class="section-title">Identity Details</h6></div>
                    <div class="col-md-6">
                        <label class="form-label">Aadhaar Number</label>
                        <input type="text" name="aadhaar" class="form-control mb-2" value="{{ $employee->aadhaar }}">
                        <label class="form-label text-muted small">Upload Aadhaar (Leave blank to keep current)</label>
                        <input type="file" name="aadhaar_file" class="form-control" accept="image/*,.pdf">
                        @if($employee->aadhaar_file)
                            <a href="{{ asset('storage/' . $employee->aadhaar_file) }}" target="_blank" class="small mt-1 d-block">View Current Aadhaar</a>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">PAN Number (if available)</label>
                        <input type="text" name="pan" class="form-control mb-2" value="{{ $employee->pan }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Passport Number (if available)</label>
                        <input type="text" name="passport" class="form-control mb-2" value="{{ $employee->passport }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Driving License (if available)</label>
                        <input type="text" name="driving_license" class="form-control mb-2" value="{{ $employee->driving_license }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Bank Details & Employment Details -->
        <div class="card">
            <div class="card-header"><h5><i class="fa-solid fa-bank"></i> Bank & Employment Details</h5></div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-12"><h6 class="section-title">Bank Details</h6></div>
                    <div class="col-md-4 col-sm-6">
                        <label class="form-label">Bank Name</label>
                        <input type="text" name="bank_name" class="form-control" value="{{ $employee->bank_name }}">
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <label class="form-label">Account Holder Name</label>
                        <input type="text" name="account_holder" class="form-control" value="{{ $employee->account_holder }}">
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <label class="form-label">Account Number</label>
                        <input type="text" name="account_number" class="form-control" value="{{ $employee->account_number }}">
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <label class="form-label">IFSC Code</label>
                        <input type="text" name="ifsc" class="form-control" value="{{ $employee->ifsc }}">
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <label class="form-label">Branch</label>
                        <input type="text" name="branch" class="form-control" value="{{ $employee->branch }}">
                    </div>

                    <div class="col-md-12"><h6 class="section-title">Employment Details</h6></div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label">Date of Joining</label>
                        <input type="date" name="joining_date" class="form-control" value="{{ $employee->joining_date }}">
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label">Employment Type</label>
                        <input type="text" name="employment_type" class="form-control" value="{{ $employee->employment_type }}">
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label">Reporting Manager</label>
                        <input type="text" name="reporting_manager" class="form-control" value="{{ $employee->reporting_manager }}">
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label">Work Location (Branch)</label>
                        <select name="branch_id" class="form-select">
                            <option value="">Select Branch</option>
                            @php $branches = \App\Models\CompanyBranch::all(); @endphp
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $employee->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Educational Qualifications -->
        <div class="card">
            <div class="card-header">
                <h5><i class="fa-solid fa-graduation-cap"></i> Educational Qualifications</h5>
            </div>
            <div class="card-body">
                <div id="educationContainer">
                    @foreach($employee->education as $index => $edu)
                    <div class="dynamic-row">
                        <button type="button" class="btn btn-sm btn-danger remove-row" data-id="{{ $edu->id }}"><i class="fa fa-times"></i></button>
                        <input type="hidden" name="education[{{ $index }}][id]" value="{{ $edu->id }}">
                        <div class="row g-3 mb-2">
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Qualification</label>
                                <input type="text" name="education[{{ $index }}][qualification]" class="form-control form-control-sm" value="{{ $edu->qualification }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Course</label>
                                <input type="text" name="education[{{ $index }}][course]" class="form-control form-control-sm" value="{{ $edu->course }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Institution</label>
                                <input type="text" name="education[{{ $index }}][institution]" class="form-control form-control-sm" value="{{ $edu->institution }}">
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small text-muted">University / Board</label>
                                <input type="text" name="education[{{ $index }}][university]" class="form-control form-control-sm" value="{{ $edu->university }}">
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <label class="form-label small text-muted">Passing Year</label>
                                <input type="text" name="education[{{ $index }}][year]" class="form-control form-control-sm" value="{{ $edu->year }}">
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <label class="form-label small text-muted">% / CGPA</label>
                                <input type="text" name="education[{{ $index }}][percentage]" class="form-control form-control-sm" value="{{ $edu->percentage }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Upload Certificate (Leave blank to keep)</label>
                                <input type="file" name="education[{{ $index }}][certificate]" class="form-control form-control-sm">
                                @if($edu->certificate)
                                    <a href="{{ asset('storage/' . $edu->certificate) }}" target="_blank" class="small mt-1 d-block">View Current</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addEducation"><i class="fa fa-plus"></i> Add Qualification</button>
            </div>
        </div>

        <!-- Previous Experience -->
        <div class="card">
            <div class="card-header"><h5><i class="fa-solid fa-briefcase"></i> Previous Experience</h5></div>
            <div class="card-body">
                <div id="experienceContainer">
                    @foreach($employee->experience as $index => $exp)
                    <div class="dynamic-row">
                        <button type="button" class="btn btn-sm btn-danger remove-row" data-id="{{ $exp->id }}"><i class="fa fa-times"></i></button>
                        <input type="hidden" name="experience[{{ $index }}][id]" value="{{ $exp->id }}">
                        <div class="row g-3 mb-2">
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Company Name</label>
                                <input type="text" name="experience[{{ $index }}][company]" class="form-control form-control-sm" value="{{ $exp->company }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Designation</label>
                                <input type="text" name="experience[{{ $index }}][designation]" class="form-control form-control-sm" value="{{ $exp->designation }}">
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label small text-muted">From Date</label>
                                <input type="date" name="experience[{{ $index }}][from_date]" class="form-control form-control-sm" value="{{ $exp->from_date }}">
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label small text-muted">To Date</label>
                                <input type="date" name="experience[{{ $index }}][to_date]" class="form-control form-control-sm" value="{{ $exp->to_date }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small text-muted">Total Years</label>
                                <input type="text" name="experience[{{ $index }}][years]" class="form-control form-control-sm" value="{{ $exp->years }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Upload Certificate</label>
                                <input type="file" name="experience[{{ $index }}][certificate]" class="form-control form-control-sm">
                                @if($exp->certificate)
                                    <a href="{{ asset('storage/' . $exp->certificate) }}" target="_blank" class="small mt-1 d-block">View Current</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addExperience"><i class="fa fa-plus"></i> Add Experience</button>
            </div>
        </div>

        <!-- Certifications -->
        <div class="card">
            <div class="card-header"><h5><i class="fa-solid fa-certificate"></i> Certifications</h5></div>
            <div class="card-body">
                <div id="certificateContainer">
                    @foreach($employee->certificates as $index => $cert)
                    <div class="dynamic-row row g-3">
                        <button type="button" class="btn btn-sm btn-danger remove-row" data-id="{{ $cert->id }}"><i class="fa fa-times"></i></button>
                        <input type="hidden" name="certificates[{{ $index }}][id]" value="{{ $cert->id }}">
                        <div class="col-md-6 col-lg-3"><label class="form-label small text-muted">Certificate Name</label><input type="text" name="certificates[{{ $index }}][certificate_name]" class="form-control form-control-sm" value="{{ $cert->certificate_name }}"></div>
                        <div class="col-md-6 col-lg-3"><label class="form-label small text-muted">Issued By</label><input type="text" name="certificates[{{ $index }}][issued_by]" class="form-control form-control-sm" value="{{ $cert->issued_by }}"></div>
                        <div class="col-md-4 col-lg-2"><label class="form-label small text-muted">Issued Date</label><input type="date" name="certificates[{{ $index }}][issued_date]" class="form-control form-control-sm" value="{{ $cert->issued_date }}"></div>
                        <div class="col-md-4 col-lg-2"><label class="form-label small text-muted">Expiry Date</label><input type="date" name="certificates[{{ $index }}][expiry_date]" class="form-control form-control-sm" value="{{ $cert->expiry_date }}"></div>
                        <div class="col-md-4 col-lg-2">
                            <label class="form-label small text-muted">File</label><input type="file" name="certificates[{{ $index }}][file]" class="form-control form-control-sm">
                            @if($cert->file)
                                <a href="{{ asset('storage/' . $cert->file) }}" target="_blank" class="small mt-1 d-block">View</a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addCertificate"><i class="fa fa-plus"></i> Add Certificate</button>
            </div>
        </div>
        
        @php
            $providedAssets = $employee->provided_assets ?? [];
            if (is_string($providedAssets)) {
                $providedAssets = json_decode($providedAssets, true) ?? [];
            }
        @endphp

        <!-- Company Assets -->
        <div class="card">
            <div class="card-header"><h5><i class="fa-solid fa-laptop"></i> Company Assets Provided</h5></div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-12">
                        <label class="form-label fw-bold mb-3">Check all assets provided:</label>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="provided_assets[laptop]" value="Yes" id="assetLaptop" {{ isset($providedAssets['laptop']) ? 'checked' : '' }}>
                                <label class="form-check-label" for="assetLaptop">Laptop</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="provided_assets[pc]" value="Yes" id="assetPC" {{ isset($providedAssets['pc']) ? 'checked' : '' }}>
                                <label class="form-check-label" for="assetPC">PC</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="provided_assets[monitor]" value="Yes" id="assetMonitor" {{ isset($providedAssets['monitor']) ? 'checked' : '' }}>
                                <label class="form-check-label" for="assetMonitor">Extra Monitor</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="provided_assets[phone]" value="Yes" id="assetPhone" {{ isset($providedAssets['phone']) ? 'checked' : '' }}>
                                <label class="form-check-label" for="assetPhone">Phone</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="assetSim" {{ isset($providedAssets['sim_number']) ? 'checked' : '' }}>
                                <label class="form-check-label" for="assetSim">SIM Card</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="assetAnother" {{ isset($providedAssets['other_details']) ? 'checked' : '' }}>
                                <label class="form-check-label" for="assetAnother">Another (Other)</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6" id="simNumberContainer" style="{{ isset($providedAssets['sim_number']) ? '' : 'display: none;' }}">
                        <label class="form-label">SIM Card Number</label>
                        <input type="text" name="provided_assets[sim_number]" class="form-control" value="{{ $providedAssets['sim_number'] ?? '' }}">
                    </div>
                    
                    <div class="col-md-12" id="anotherAssetContainer" style="{{ isset($providedAssets['other_details']) ? '' : 'display: none;' }}">
                        <label class="form-label">Other Assets Details</label>
                        <textarea name="provided_assets[other_details]" class="form-control" rows="2">{{ $providedAssets['other_details'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end mb-5">
            <button type="submit" class="btn btn-success btn-submit px-5"><i class="fa-solid fa-save me-2"></i> Save Changes</button>
        </div>
    </form>
</div>

<!-- Crop Modal -->
<div class="modal fade" id="cropModal" tabindex="-1" aria-labelledby="cropModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cropModalLabel"><i class="fa-solid fa-crop-simple"></i> Crop Image</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center" style="background-color: #f8f9fa;">
        <div class="img-container" style="max-height: 400px; display: flex; justify-content: center; align-items: center;">
          <img id="imageToCrop" src="" alt="Picture" style="max-width: 100%; max-height: 400px;">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="cropImageBtn">Crop & Save</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<!-- jQuery UI -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<!-- Cropper.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<script>
$(document).ready(function() {
    
    // Initialize jQuery UI Datepicker for all date fields
    function initDatepickers(context) {
        $(context).find('input[type="date"]').each(function() {
            var $this = $(this);
            var dateValue = $this.val();
            
            // Change type to text for datepicker
            $this.attr('type', 'text');
            
            // Setup hidden field for submission
            var originalName = $this.attr('name');
            $this.removeAttr('name');
            var $hidden = $('<input type="hidden" name="' + originalName + '">');
            $this.after($hidden);
            
            // Initialize datepicker
            if (!$this.hasClass('hasDatepicker')) {
                $this.datepicker({
                    dateFormat: 'dd-mm-yy',
                    altField: $hidden,
                    altFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '1950:2030',
                    showButtonPanel: true
                });
            }
            
            // Restore value if exists
            if (dateValue) {
                var parts = dateValue.split('-');
                if (parts.length === 3) {
                    var d = new Date(parts[0], parts[1] - 1, parts[2]);
                    $this.datepicker('setDate', d);
                }
            }
        });
    }

    initDatepickers(document);
    
    // Same Address Checkbox
    $('#sameAddress').change(function() {
        if(this.checked) {
            $('#permanent_address').val($('#present_address').val());
        }
    });

    // Dynamic Education
    let eduIndex = {{ max(count($employee->education), 1) }};
    $('#addEducation').click(function() {
        let html = `
        <div class="dynamic-row">
            <button type="button" class="btn btn-sm btn-danger remove-row"><i class="fa fa-times"></i></button>
            <div class="row g-3 mb-2">
                <div class="col-md-4">
                    <label class="form-label small text-muted">Qualification</label>
                    <input type="text" name="education[${eduIndex}][qualification]" class="form-control form-control-sm" placeholder="e.g. Bachelor's Degree">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">Course</label>
                    <input type="text" name="education[${eduIndex}][course]" class="form-control form-control-sm" placeholder="e.g. B.Tech Computer Science">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">Institution</label>
                    <input type="text" name="education[${eduIndex}][institution]" class="form-control form-control-sm" placeholder="College Name">
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small text-muted">University / Board</label>
                    <input type="text" name="education[${eduIndex}][university]" class="form-control form-control-sm" placeholder="University Name">
                </div>
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small text-muted">Passing Year</label>
                    <input type="text" name="education[${eduIndex}][year]" class="form-control form-control-sm" placeholder="YYYY">
                </div>
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small text-muted">% / CGPA</label>
                    <input type="text" name="education[${eduIndex}][percentage]" class="form-control form-control-sm" placeholder="e.g. 85%">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">Upload Certificate (Optional)</label>
                    <input type="file" name="education[${eduIndex}][certificate]" class="form-control form-control-sm" accept="image/*,.pdf">
                </div>
            </div>
        </div>`;
        $('#educationContainer').append(html);
        initDatepickers($('#educationContainer').children().last());
        eduIndex++;
    });

    // Dynamic Experience
    let expIndex = {{ count($employee->experience) }};
    $('#addExperience').click(function() {
        let html = `
        <div class="dynamic-row">
            <button type="button" class="btn btn-sm btn-danger remove-row"><i class="fa fa-times"></i></button>
            <div class="row g-3 mb-2">
                <div class="col-md-6">
                    <label class="form-label small text-muted">Company Name</label>
                    <input type="text" name="experience[${expIndex}][company]" class="form-control form-control-sm" placeholder="e.g. Tech Solutions Inc.">
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted">Designation</label>
                    <input type="text" name="experience[${expIndex}][designation]" class="form-control form-control-sm" placeholder="e.g. Senior Developer">
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small text-muted">From Date</label>
                    <input type="date" name="experience[${expIndex}][from_date]" class="form-control form-control-sm">
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small text-muted">To Date</label>
                    <input type="date" name="experience[${expIndex}][to_date]" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Total Years</label>
                    <input type="text" name="experience[${expIndex}][years]" class="form-control form-control-sm" placeholder="e.g. 2.5">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">Upload Certificate</label>
                    <input type="file" name="experience[${expIndex}][certificate]" class="form-control form-control-sm" accept="image/*,.pdf">
                </div>
            </div>
        </div>`;
        $('#experienceContainer').append(html);
        initDatepickers($('#experienceContainer').children().last());
        expIndex++;
    });

    // Dynamic Certifications
    let certIndex = {{ count($employee->certificates) }};
    $('#addCertificate').click(function() {
        let html = `
        <div class="dynamic-row row g-3">
            <button type="button" class="btn btn-sm btn-danger remove-row"><i class="fa fa-times"></i></button>
            <div class="col-md-6 col-lg-3"><label class="form-label small text-muted">Certificate Name</label><input type="text" name="certificates[${certIndex}][certificate_name]" class="form-control form-control-sm"></div>
            <div class="col-md-6 col-lg-3"><label class="form-label small text-muted">Issued By</label><input type="text" name="certificates[${certIndex}][issued_by]" class="form-control form-control-sm"></div>
            <div class="col-md-4 col-lg-2"><label class="form-label small text-muted">Issued Date</label><input type="date" name="certificates[${certIndex}][issued_date]" class="form-control form-control-sm"></div>
            <div class="col-md-4 col-lg-2"><label class="form-label small text-muted">Expiry Date</label><input type="date" name="certificates[${certIndex}][expiry_date]" class="form-control form-control-sm"></div>
            <div class="col-md-4 col-lg-2"><label class="form-label small text-muted">File</label><input type="file" name="certificates[${certIndex}][file]" class="form-control form-control-sm" accept="image/*,.pdf"></div>
        </div>`;
        $('#certificateContainer').append(html);
        initDatepickers($('#certificateContainer').children().last());
        certIndex++;
    });

    // Remove row
    $(document).on('click', '.remove-row', function() {
        let id = $(this).data('id');
        if (id) {
            // Append a hidden input to mark this ID for deletion on the server side
            // For now, we will simply not send it, and the server will sync based on what is sent.
        }
        $(this).closest('.dynamic-row').remove();
    });

    // Asset Checkboxes toggle
    $('#assetSim').change(function() {
        if(this.checked) {
            $('#simNumberContainer').slideDown();
        } else {
            $('#simNumberContainer').slideUp();
            $('input[name="provided_assets[sim_number]"]').val('');
        }
    });

    $('#assetAnother').change(function() {
        if(this.checked) {
            $('#anotherAssetContainer').slideDown();
        } else {
            $('#anotherAssetContainer').slideUp();
            $('textarea[name="provided_assets[other_details]"]').val('');
        }
    });

    // Image Cropping Logic
    let currentFileInput;
    let cropper;
    let originalFileName = 'image.jpg';
    const cropModal = new bootstrap.Modal(document.getElementById('cropModal'));
    const imageToCrop = document.getElementById('imageToCrop');

    $(document).on('change', 'input[type="file"]', function(e) {
        const file = e.target.files[0];
        if (file && file.type.match('image.*')) {
            currentFileInput = e.target;
            originalFileName = file.name;
            const reader = new FileReader();
            reader.onload = function(event) {
                imageToCrop.src = event.target.result;
                cropModal.show();
            };
            reader.readAsDataURL(file);
            $(this).val('');
        }
    });

    $('#cropModal').on('shown.bs.modal', function () {
        cropper = new Cropper(imageToCrop, {
            viewMode: 2,
            autoCropArea: 1,
            responsive: true,
        });
    }).on('hidden.bs.modal', function () {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        imageToCrop.src = '';
    });

    $('#cropImageBtn').click(function() {
        if (!cropper) return;
        const btn = $(this);
        const originalText = btn.html();
        btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...').prop('disabled', true);
        
        const canvas = cropper.getCroppedCanvas();
        canvas.toBlob(function(blob) {
            if (!blob) {
                btn.html(originalText).prop('disabled', false);
                Swal.fire('Error', 'Could not crop image.', 'error');
                return;
            }
            let sizeKb = blob.size / 1024;
            if (sizeKb > 200) {
                canvas.toBlob(function(compressedBlob) {
                    let compSizeKb = compressedBlob.size / 1024;
                    if (compSizeKb > 200) {
                        btn.html(originalText).prop('disabled', false);
                        Swal.fire({
                            title: 'Image Too Large',
                            text: 'Cropped image is ' + compSizeKb.toFixed(2) + ' KB even after compression. Maximum allowed size is 200 KB.',
                            icon: 'warning'
                        });
                    } else {
                        finalizeCrop(compressedBlob, btn, originalText);
                    }
                }, 'image/jpeg', 0.6);
            } else {
                finalizeCrop(blob, btn, originalText);
            }
        }, 'image/jpeg', 0.9);
    });

    function finalizeCrop(blob, btn, originalText) {
        const ext = originalFileName.split('.').pop().toLowerCase();
        let fileName = originalFileName;
        if (!['jpg', 'jpeg'].includes(ext)) {
             fileName = fileName.replace('.' + ext, '.jpg');
        }
        const file = new File([blob], fileName, { type: 'image/jpeg' });
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        
        if (currentFileInput) {
            currentFileInput.files = dataTransfer.files;
            let $input = $(currentFileInput);
            $input.siblings('.crop-success-msg').remove();
            $input.after('<div class="crop-success-msg text-success small mt-1"><i class="fa-solid fa-check-circle"></i> Image ready (' + (blob.size / 1024).toFixed(2) + ' KB)</div>');
        }
        btn.html(originalText).prop('disabled', false);
        cropModal.hide();
    }

    // Ajax Form Submit
    $('#employeeForm').on('submit', function(e) {
        e.preventDefault();
        
        $('#loader').fadeIn();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        var formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'Accept': 'application/json'
            },
            success: function(response) {
                $('#loader').fadeOut();
                if(response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message || 'Employee updated successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        window.location.href = "{{ route('admin.employees.index') }}";
                    });
                }
            },
            error: function(xhr) {
                $('#loader').fadeOut();
                if (xhr.status === 422) { 
                    var errors = xhr.responseJSON.errors;
                    var errorMsg = 'Please check the highlighted fields:\n';
                    $.each(errors, function(key, value) {
                        var inputName = key;
                        if(key.includes('.')) {
                            var parts = key.split('.');
                            inputName = parts[0];
                            for(var i=1; i<parts.length; i++) {
                                inputName += '[' + parts[i] + ']';
                            }
                        }
                        var input = $('[name="'+inputName+'"]');
                        if(input.length) {
                            input.addClass('is-invalid');
                            input.after('<div class="invalid-feedback">'+value[0]+'</div>');
                        }
                        errorMsg += '- ' + value[0] + '\n';
                    });
                    Swal.fire({
                        title: 'Validation Error',
                        text: errorMsg,
                        icon: 'error'
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred while submitting. Please try again.',
                        icon: 'error'
                    });
                }
            }
        });
    });

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
