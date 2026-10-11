<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Registration</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Cropper.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .form-container { max-width: 900px; margin: 40px auto; background: transparent; }
        .card { border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.04); margin-bottom: 25px; border: 1px solid #eef0f2; }
        .card-header { background-color: #ffffff; border-bottom: 1px solid #f1f1f1; border-radius: 12px 12px 0 0 !important; padding: 20px 25px; }
        .card-header h5 { margin: 0; font-weight: 600; color: #333; font-size: 1.1rem; }
        .card-header i { color: #0d6efd; margin-right: 8px; }
        .card-body { padding: 25px; background-color: #ffffff; border-radius: 0 0 12px 12px; }
        .form-label { font-weight: 500; color: #555; font-size: 0.9rem; margin-bottom: 6px; }
        .form-control, .form-select { border-radius: 8px; border: 1px solid #ced4da; padding: 10px 15px; font-size: 0.95rem; }
        .form-control:focus, .form-select:focus { border-color: #86b7fe; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15); }
        .section-title { font-weight: 600; color: #444; border-bottom: 2px solid #eef0f2; padding-bottom: 8px; margin-bottom: 20px; margin-top: 10px; font-size: 1.05rem;}
        #loader { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.9); z-index: 9999; text-align: center; padding-top: 20vh; }
        .dynamic-row { border: 1px solid #eef0f2; padding: 20px; margin-bottom: 15px; border-radius: 8px; position: relative; background-color: #fafbfc; }
        .remove-row { position: absolute; top: 15px; right: 15px; border-radius: 50%; width: 30px; height: 30px; padding: 0; line-height: 30px; text-align: center; }
        .btn-submit { border-radius: 8px; padding: 12px 30px; font-weight: 600; font-size: 1.1rem; box-shadow: 0 4px 6px rgba(25, 135, 84, 0.2); transition: all 0.3s; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 12px rgba(25, 135, 84, 0.3); }
        .logo-container { text-align: center; margin-bottom: 30px; }
        .logo-container img { max-height: 70px; object-fit: contain; }
    </style>
</head>
<body>

<div id="loader">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <h5 class="mt-3 text-secondary">Submitting your information...</h5>
</div>

<div class="container py-4 form-container">
    @if(auth()->guard('employee')->check())
        <div class="d-flex justify-content-end mb-2">
            <form action="{{ route('employee.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fa fa-sign-out-alt me-1"></i> Logout</button>
            </form>
        </div>
    @endif
    <div class="logo-container">
        <img src="{{ asset('logo-new.webp') }}" alt="Company Logo">
        <h3 class="mt-3 fw-bold text-dark">Employee Profile Completion</h3>
        <p class="text-muted">Please complete your profile details to proceed.</p>
    </div>
    
    <form id="employeeForm" action="{{ route('employee.complete-profile.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Personal Information -->
        <div class="card">
            <div class="card-header"><h5><i class="fa-solid fa-user"></i> Personal Information</h5></div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="John Doe" value="{{ $employee ? $employee->name : '' }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Employee ID</label>
                        <input type="text" name="employee_id" class="form-control" placeholder="SA00000" value="{{ $employee ? $employee->employee_id : '' }}" readonly style="background-color: #e9ecef;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Department</label>
                        <select name="department_id" class="form-select" style="pointer-events: none; background-color: #e9ecef;" tabindex="-1">
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ ($employee && $employee->department_id == $dept->id) ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Designation <span class="text-danger">*</span></label>
                        <input type="text" name="designation" class="form-control" placeholder="e.g. Software Engineer" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Company Email (Optional)</label>
                        <input type="email" name="company_email" class="form-control" placeholder="john@company.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Personal Email <span class="text-danger">*</span></label>
                        <input type="email" name="personal_email" class="form-control" placeholder="john.doe@gmail.com" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Personal Mobile <span class="text-danger">*</span></label>
                        <input type="text" name="personal_phone" class="form-control" placeholder="+1 234 567 8900" value="{{ $employee ? $employee->personal_phone : '' }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Office Mobile (Optional)</label>
                        <input type="text" name="office_phone" class="form-control" placeholder="+1 234 567 8901">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="form-control" value="{{ $employee ? $employee->dob : '' }}" readonly style="background-color: #e9ecef;">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Gender <span class="text-danger">*</span></label>
                        <select name="gender" class="form-select" required>
                            <option value="">Select</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Blood Group <span class="text-danger">*</span></label>
                        <select name="blood_group" class="form-select" required>
                            <option value="">Select Blood Group</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Marital Status <span class="text-danger">*</span></label>
                        <select name="marital_status" class="form-select" required>
                            <option value="">Select</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Employee Photo (Max 200KB, 3.5cm x 4.5cm) <span class="text-danger">*</span></label>
                        <input type="file" name="photo" class="form-control" accept="image/*" {{ ($employee && $employee->photo) ? '' : 'required' }}>
                    </div>
                </div>
            </div>
        </div>

        <!-- Addresses -->
        <div class="card">
            <div class="card-header"><h5><i class="fa-solid fa-map-marker-alt"></i> Address Details</h5></div>
            <div class="card-body">
                <div class="row g-4">
                    <!-- Present Address -->
                    <div class="col-md-6">
                        <label class="form-label">Present Address <span class="text-danger">*</span></label>
                        <textarea name="present_address" id="present_address" class="form-control" rows="4" placeholder="House, Street, City, State, PIN..." required></textarea>
                    </div>
                    <!-- Permanent Address -->
                    <div class="col-md-6">
                        <label class="form-label d-flex justify-content-between">
                            <span>Permanent Address <span class="text-danger">*</span></span>
                            <div class="form-check ms-3 fw-normal">
                                <input class="form-check-input" type="checkbox" id="sameAddress">
                                <label class="form-check-label text-muted" for="sameAddress">Same as Present</label>
                            </div>
                        </label>
                        <textarea name="permanent_address" id="permanent_address" class="form-control" rows="4" placeholder="House, Street, City, State, PIN..." required></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Emergency Contact & Identity -->
        <div class="card">
            <div class="card-header"><h5><i class="fa-solid fa-id-card"></i> Emergency Contact & Identity Details</h5></div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-12"><h6 class="section-title">Emergency Contact <span class="text-danger">*</span></h6></div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label">Contact Person <span class="text-danger">*</span></label>
                        <input type="text" name="emergency_name" class="form-control" required>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label">Relationship <span class="text-danger">*</span></label>
                        <select name="emergency_relation" class="form-select" required>
                            <option value="">Select Relation</option>
                            <option value="Father">Father</option>
                            <option value="Mother">Mother</option>
                            <option value="Spouse">Spouse</option>
                            <option value="Brother">Brother</option>
                            <option value="Sister">Sister</option>
                            <option value="Friend">Friend</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" name="emergency_phone" class="form-control" required>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label">Alternate Mobile (Optional)</label>
                        <input type="text" name="emergency_alt_phone" class="form-control">
                    </div>

                    <div class="col-md-12"><h6 class="section-title">Identity Details</h6></div>
                    <div class="col-md-6">
                        <label class="form-label">Aadhaar Number <span class="text-danger">*</span></label>
                        <input type="text" name="aadhaar" class="form-control mb-2" required>
                        <label class="form-label text-muted small">Upload Aadhaar (PDF/JPG) (Optional)</label>
                        <input type="file" name="aadhaar_file" class="form-control" accept="image/*,.pdf">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">PAN Number (Optional)</label>
                        <input type="text" name="pan" class="form-control mb-2">
                        <label class="form-label text-muted small">Upload PAN (PDF/JPG) (Optional)</label>
                        <input type="file" name="pan_file" class="form-control" accept="image/*,.pdf">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Passport Number (if available)</label>
                        <input type="text" name="passport" class="form-control mb-2">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Driving License (if available)</label>
                        <input type="text" name="driving_license" class="form-control mb-2">
                    </div>
                </div>
            </div>
        </div>

        <!-- Bank Details & Employment Details -->
        <div class="card">
            <div class="card-header"><h5><i class="fa-solid fa-bank"></i> Bank & Employment Details</h5></div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-12"><h6 class="section-title">Bank Details <span class="text-danger">*</span></h6></div>
                    <div class="col-md-4 col-sm-6">
                        <label class="form-label">Bank Name <span class="text-danger">*</span></label>
                        <input type="text" name="bank_name" class="form-control" required>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <label class="form-label">Account Holder Name <span class="text-danger">*</span></label>
                        <input type="text" name="account_holder" class="form-control" required>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <label class="form-label">Account Number <span class="text-danger">*</span></label>
                        <input type="text" name="account_number" class="form-control" required>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <label class="form-label">IFSC Code <span class="text-danger">*</span></label>
                        <input type="text" name="ifsc" class="form-control" required>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <label class="form-label">Branch <span class="text-danger">*</span></label>
                        <input type="text" name="branch" class="form-control" required>
                    </div>


                    <div class="col-md-12"><h6 class="section-title">Employment Details <span class="text-danger">*</span></h6></div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label">Date of Joining <span class="text-danger">*</span></label>
                        <input type="date" name="joining_date" class="form-control" required>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label">Employment Type <span class="text-danger">*</span></label>
                        <input type="text" name="employment_type" class="form-control" placeholder="e.g. Full-time" required>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label">Reporting Manager <span class="text-danger">*</span></label>
                        <input type="text" name="reporting_manager" class="form-control" required>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label">Work Location (Branch) <span class="text-danger">*</span></label>
                        <select name="branch_id" class="form-select" required>
                            <option value="">Select Branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Educational Qualifications -->
        <div class="card">
            <div class="card-header">
                <h5><i class="fa-solid fa-graduation-cap"></i> Educational Qualifications </h5>
                <p class="text-muted small mb-0 mt-1">Please enter at least your highest qualification.</p>
            </div>
            <div class="card-body">
                <div id="educationContainer"></div>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addEducation"><i class="fa fa-plus"></i> Add Qualification</button>
            </div>
        </div>

        <!-- Previous Experience -->
        <div class="card">
            <div class="card-header"><h5><i class="fa-solid fa-briefcase"></i> Previous Experience</h5></div>
            <div class="card-body">
                <div id="experienceContainer"></div>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addExperience"><i class="fa fa-plus"></i> Add Experience</button>
            </div>
        </div>

        <!-- Certifications -->
        <div class="card">
            <div class="card-header"><h5><i class="fa-solid fa-certificate"></i> Certifications</h5></div>
            <div class="card-body">
                <div id="certificateContainer"></div>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addCertificate"><i class="fa fa-plus"></i> Add Certificate</button>
            </div>
        </div>

        <!-- Company Assets -->
        <div class="card">
            <div class="card-header"><h5><i class="fa-solid fa-laptop"></i> Company Assets Provided</h5></div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-12">
                        <label class="form-label fw-bold mb-3">Check all assets provided to you:</label>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="provided_assets[laptop]" value="Yes" id="assetLaptop">
                                <label class="form-check-label" for="assetLaptop">Laptop</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="provided_assets[pc]" value="Yes" id="assetPC">
                                <label class="form-check-label" for="assetPC">PC</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="provided_assets[monitor]" value="Yes" id="assetMonitor">
                                <label class="form-check-label" for="assetMonitor">Extra Monitor</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="provided_assets[phone]" value="Yes" id="assetPhone">
                                <label class="form-check-label" for="assetPhone">Phone</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="assetSim">
                                <label class="form-check-label" for="assetSim">SIM Card</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="assetAnother">
                                <label class="form-check-label" for="assetAnother">Another (Other)</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6" id="simNumberContainer" style="display: none;">
                        <label class="form-label">SIM Card Number</label>
                        <input type="text" name="provided_assets[sim_number]" class="form-control" placeholder="Enter SIM Number">
                    </div>
                    
                    <div class="col-md-12" id="anotherAssetContainer" style="display: none;">
                        <label class="form-label">Other Assets Details</label>
                        <textarea name="provided_assets[other_details]" class="form-control" rows="2" placeholder="List any other assets provided"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4 mb-4" style="background-color: #f8f9fa; border-left: 4px solid #198754;">
            <div class="card-body py-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="agreed_to_terms" value="1" id="agreeTerms">
                    <label class="form-check-label fw-bold text-dark" for="agreeTerms">
                        I have read and agree to the <a href="{{ route('terms') }}" target="_blank" class="text-primary text-decoration-underline">SMECLABS Human Resource Management System (HRMS) Terms and Conditions</a>. I confirm that all information and documents submitted are true, complete, and accurate to the best of my knowledge. 
                    </label>
                </div>
            </div>
        </div>

        <div class="text-center mb-5">
            <button type="button" class="btn btn-secondary btn-submit px-5 me-3" onclick="if(window.self !== window.top) { window.parent.postMessage('profile_skipped', '*'); } else { window.location.href='{{ route('employee.dashboard') }}'; }">Skip this form</button>
            <button type="submit" class="btn btn-success btn-submit px-5"><i class="fa-solid fa-paper-plane me-2"></i> Submit Information</button>
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

<!-- jQuery & Bootstrap -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
                // If the value is yy-mm-dd from DB, parse it and set datepicker
                // Native date fields have yy-mm-dd
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
        } else {
            $('#permanent_address').val('');
        }
    });

    // Dynamic Education
    let eduIndex = 0;
    $('#addEducation').click(function() {
        let html = `
        <div class="dynamic-row">
            <button type="button" class="btn btn-sm btn-danger remove-row"><i class="fa fa-times"></i></button>
            <div class="row g-3 mb-2">
                <div class="col-md-4">
                    <label class="form-label small text-muted">Qualification </label>
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
        eduIndex++;
    });

    // Dynamic Experience
    let expIndex = 0;
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
                    <label class="form-label small text-muted">Upload Certificate (Optional)</label>
                    <input type="file" name="experience[${expIndex}][certificate]" class="form-control form-control-sm" accept="image/*,.pdf">
                </div>
            </div>
        </div>`;
        $('#experienceContainer').append(html);
        initDatepickers($('#experienceContainer').children().last());
        expIndex++;
    });

    // Dynamic Certifications
    let certIndex = 0;
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

    // Add 1 default row
    $('#addEducation').click();
    $('#addExperience').click();

    // Remove row
    $(document).on('click', '.remove-row', function() {
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

    $(document).on('change', 'input[name="photo"]', function(e) {
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
            
            // Clear the input value so if they cancel, they can select it again
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
        
        // Show loading state on button
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
                // Try to compress it
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
                }, 'image/jpeg', 0.6); // Lower quality to compress
            } else {
                finalizeCrop(blob, btn, originalText);
            }
        }, 'image/jpeg', 0.9);
    });

    function finalizeCrop(blob, btn, originalText) {
        // Create a new File object
        const ext = originalFileName.split('.').pop().toLowerCase();
        let fileName = originalFileName;
        if (!['jpg', 'jpeg'].includes(ext)) {
             fileName = fileName.replace('.' + ext, '.jpg');
        }
        
        const file = new File([blob], fileName, { type: 'image/jpeg' });
        
        // Set the file to the input using DataTransfer
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        
        if (currentFileInput) {
            currentFileInput.files = dataTransfer.files;
            
            // Add a visual indicator to the input's parent or near it
            let $input = $(currentFileInput);
            $input.siblings('.crop-success-msg').remove();
            $input.after('<div class="crop-success-msg text-success small mt-1"><i class="fa-solid fa-check-circle"></i> Image ready (' + (blob.size / 1024).toFixed(2) + ' KB)</div>');
        }
        
        btn.html(originalText).prop('disabled', false);
        cropModal.hide();
        
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Image cropped successfully (' + (blob.size / 1024).toFixed(2) + ' KB)',
            showConfirmButton: false,
            timer: 3000
        });
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
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-success px-4'
                        }
                    }).then((result) => {
                        if (response.redirect) {
                            if(window.self !== window.top) {
                                window.parent.postMessage('profile_completed', '*');
                            } else {
                                window.location.href = response.redirect;
                            }
                        } else {
                            if(window.self !== window.top) {
                                window.parent.postMessage('profile_completed', '*');
                            } else {
                                window.location.reload();
                            }
                        }
                    });
                }
            },
            error: function(xhr) {
                $('#loader').fadeOut();
                
                if (xhr.status === 422) { // Validation errors
                    var errors = xhr.responseJSON.errors;
                    var errorMsg = 'Please check the highlighted fields:\n';
                    
                    $.each(errors, function(key, value) {
                        // Fix for array inputs (e.g. education.0.certificate -> education[0][certificate])
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
                        icon: 'error',
                        customClass: {
                            confirmButton: 'btn btn-danger px-4'
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred while submitting the form. Please try again.',
                        icon: 'error',
                        customClass: {
                            confirmButton: 'btn btn-danger px-4'
                        }
                    });
                }
            }
        });
    });

});
</script>

</body>
</html>
