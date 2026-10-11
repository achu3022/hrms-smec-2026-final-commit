@if(!request()->ajax())
@extends('layouts.admin')
@section('content')
@endif
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5>Employee Details: {{ $employee->name }} ({{ $employee->employee_id }})</h5>
        <div>
            <a href="{{ route('admin.employees.index') }}" class="btn btn-sm btn-secondary">Back</a>
            <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-sm btn-primary">Edit</a>
        </div>
    </div>
    <div class="card-body">
        
        <div class="row">
            <div class="col-md-3 text-center mb-4">
                @if($employee->photo)
                    <img src="{{ Storage::url($employee->photo) }}" class="img-fluid rounded" style="max-height:200px;">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px; width: 150px; margin:0 auto;">
                        No Photo
                    </div>
                @endif
                <div class="mt-3">
                    <span class="badge bg-{{ $employee->status == 'Active' ? 'success' : 'danger' }} fs-6">{{ $employee->status }}</span>
                </div>
            </div>

            <div class="col-md-9">
                <table class="table table-bordered">
                    <tr><th>Name</th><td>{{ $employee->name }}</td><th>Emp ID</th><td>{{ $employee->employee_id }}</td></tr>
                    <tr><th>Department</th><td>{{ $employee->departmentRel->name ?? $employee->department }}</td><th>Designation</th><td>{{ $employee->designationRel->name ?? $employee->designation }}</td></tr>
                    <tr><th>Company Email</th><td>{{ $employee->company_email }}</td><th>Personal Email</th><td>{{ $employee->personal_email }}</td></tr>
                    <tr><th>Personal Phone</th><td>{{ $employee->personal_phone }}</td><th>Office Phone</th><td>{{ $employee->office_phone }}</td></tr>
                    <tr><th>DOB</th><td>{{ $employee->dob ? \Carbon\Carbon::parse($employee->dob)->format('d-m-Y') : '' }}</td><th>Gender</th><td>{{ $employee->gender }}</td></tr>
                    <tr><th>Blood Group</th><td>{{ $employee->blood_group }}</td><th>Marital Status</th><td>{{ $employee->marital_status }}</td></tr>
                </table>
            </div>
        </div>

        <hr>
        
        <div class="row">
            <div class="col-md-6">
                <h5>Present Address</h5>
                <p>{{ $employee->present_address }}</p>
            </div>
            <div class="col-md-6">
                <h5>Permanent Address</h5>
                <p>{{ $employee->permanent_address }}</p>
            </div>
        </div>

        <hr>
        <h5>Emergency Contact</h5>
        <table class="table table-bordered">
            <tr>
                <th>Name</th><td>{{ $employee->emergency_name }}</td>
                <th>Relation</th><td>{{ $employee->emergency_relation }}</td>
            </tr>
            <tr>
                <th>Phone</th><td>{{ $employee->emergency_phone }}</td>
                <th>Alt Phone</th><td>{{ $employee->emergency_alt_phone }}</td>
            </tr>
        </table>

        <hr>
        <h5>Identity & Documents</h5>
        <table class="table table-bordered">
            <tr>
                <th>Aadhaar</th>
                <td>{{ $employee->aadhaar }} @if($employee->aadhaar_file) <a href="{{ Storage::url($employee->aadhaar_file) }}" target="_blank">View File</a> @endif</td>
            </tr>
            <tr>
                <th>PAN</th>
                <td>{{ $employee->pan }} @if($employee->pan_file) <a href="{{ Storage::url($employee->pan_file) }}" target="_blank">View File</a> @endif</td>
            </tr>
            <tr>
                <th>Passport</th>
                <td>{{ $employee->passport }} @if($employee->passport_file) <a href="{{ Storage::url($employee->passport_file) }}" target="_blank">View File</a> @endif</td>
            </tr>
            <tr>
                <th>Driving License</th>
                <td>{{ $employee->driving_license }} @if($employee->license_file) <a href="{{ Storage::url($employee->license_file) }}" target="_blank">View File</a> @endif</td>
            </tr>
        </table>

        <hr>
        <h5>Bank Details</h5>
        <table class="table table-bordered">
            <tr>
                <th>Bank Name</th><td>{{ $employee->bank_name }}</td>
                <th>Account Holder</th><td>{{ $employee->account_holder }}</td>
            </tr>
            <tr>
                <th>Account Number</th><td>{{ $employee->account_number }}</td>
                <th>IFSC Code</th><td>{{ $employee->ifsc }}</td>
            </tr>
            <tr>
                <th>Branch</th><td>{{ $employee->branch }}</td>
                <th>Document</th>
                <td>@if($employee->bank_document) <a href="{{ Storage::url($employee->bank_document) }}" target="_blank">View File</a> @endif</td>
            </tr>
        </table>

        <hr>
        <h5>Employment Details</h5>
        <table class="table table-bordered">
            <tr>
                <th>Joining Date</th><td>{{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('d-m-Y') : '' }}</td>
                <th>Employment Type</th><td>{{ $employee->employment_type }}</td>
            </tr>
            <tr>
                <th>Reporting Manager</th><td>{{ $employee->reporting_manager }}</td>
                <th>Work Location</th><td>{{ $employee->work_location }}</td>
            </tr>
        </table>

        <hr>
        <h5>Education Details</h5>
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Qualification</th><th>Course</th><th>Institution</th><th>University</th><th>Year</th><th>% / CGPA</th><th>Certificate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employee->education as $edu)
                <tr>
                    <td>{{ $edu->qualification }}</td><td>{{ $edu->course }}</td><td>{{ $edu->institution }}</td>
                    <td>{{ $edu->university }}</td><td>{{ $edu->year }}</td><td>{{ $edu->percentage }}</td>
                    <td>@if($edu->certificate) <a href="{{ Storage::url($edu->certificate) }}" target="_blank">View</a> @endif</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <hr>
        <h5>Experience Details</h5>
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Company</th><th>Designation</th><th>From</th><th>To</th><th>Years</th><th>Certificate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employee->experience as $exp)
                <tr>
                    <td>{{ $exp->company }}</td><td>{{ $exp->designation }}</td><td>{{ $exp->from_date ? \Carbon\Carbon::parse($exp->from_date)->format('d-m-Y') : '' }}</td>
                    <td>{{ $exp->to_date ? \Carbon\Carbon::parse($exp->to_date)->format('d-m-Y') : '' }}</td><td>{{ $exp->years }}</td>
                    <td>@if($exp->certificate) <a href="{{ Storage::url($exp->certificate) }}" target="_blank">View</a> @endif</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <hr>
        <h5>Certifications</h5>
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Certificate</th><th>Issued By</th><th>Issue Date</th><th>Expiry Date</th><th>File</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employee->certificates as $cert)
                <tr>
                    <td>{{ $cert->certificate_name }}</td><td>{{ $cert->issued_by }}</td>
                    <td>{{ $cert->issued_date ? \Carbon\Carbon::parse($cert->issued_date)->format('d-m-Y') : '' }}</td><td>{{ $cert->expiry_date ? \Carbon\Carbon::parse($cert->expiry_date)->format('d-m-Y') : '' }}</td>
                    <td>@if($cert->file) <a href="{{ Storage::url($cert->file) }}" target="_blank">View</a> @endif</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
@if(!request()->ajax())
@endsection
@endif
