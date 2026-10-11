@extends('employee.portal.layout')

@push('styles')
<style>
    .pf-card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:20px; margin-bottom:20px; }
    .pf-section-title { font-size:.7rem; font-weight:800; color:#6366f1; text-transform:uppercase; letter-spacing:.6px; margin-bottom:14px; padding-bottom:8px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:6px; }
    .pf-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    @media(max-width:640px){ .pf-grid{ grid-template-columns:1fr; } }
    .pf-field { display:flex; flex-direction:column; gap:3px; }
    .pf-label { font-size:.68rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.4px; }
    .pf-value { font-size:.88rem; font-weight:600; color:#1e293b; }
    .pf-value.empty { color:#cbd5e1; font-style:italic; font-weight:400; }
    .pf-avatar-ring {
        width:80px; height:80px; border-radius:50%;
        background:linear-gradient(135deg,#6366f1,#2b3a67);
        display:flex; align-items:center; justify-content:center;
        font-size:2rem; font-weight:800; color:#fff; overflow:hidden;
        box-shadow:0 4px 14px rgba(99,102,241,.3);
    }
    .pf-status-pill { display:inline-flex; align-items:center; gap:4px; font-size:.72rem; font-weight:700; padding:3px 10px; border-radius:6px; }
    .status-active   { background:#dcfce7; color:#16a34a; }
    .status-inactive { background:#fee2e2; color:#dc2626; }
    .pf-doc-link { display:inline-flex; align-items:center; gap:6px; font-size:0.75rem; font-weight:600; color:#4f46e5; background:#e0e7ff; padding:4px 10px; border-radius:6px; text-decoration:none; transition:background 0.2s; }
    .pf-doc-link:hover { background:#c7d2fe; color:#4338ca; }
</style>
@endpush

@section('content')
<div class="mb-5">
    <h2 class="text-xl font-bold text-slate-800">My Profile</h2>
    <p class="text-sm text-slate-400 mt-0.5">Your personal and employment information</p>
</div>

@if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold rounded-xl px-4 py-3 mb-5 flex items-center gap-2 shadow-sm">
        <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>{{ session('success') }}
    </div>
@endif

@php
    $missingFields = $employee->getMissingProfileFields();
@endphp

@if(count($missingFields) > 0)
    <div class="bg-amber-50 border-l-4 border-amber-500 text-amber-900 p-4 rounded-xl shadow-sm mb-5 flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 text-lg mt-0.5">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h4 class="font-bold text-sm text-amber-900">Incomplete Profile Information</h4>
                <p class="text-xs text-amber-800 mt-0.5">
                    Your profile is missing required information: 
                    <strong class="font-bold text-amber-900 underline">{{ implode(', ', $missingFields) }}</strong>. 
                    Please complete all required fields.
                </p>
            </div>
        </div>
        <a href="{{ route('employee.profile.edit') }}" class="shrink-0 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold px-4 py-2 rounded-lg transition-colors inline-flex items-center gap-1.5 shadow-sm">
            <i class="fa-solid fa-user-pen"></i> Complete Profile
        </a>
    </div>
@endif

{{-- Profile Header --}}
<div class="pf-card">
    <div class="flex flex-col sm:flex-row items-center sm:items-start justify-between gap-5">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5">
            <div class="pf-avatar-ring">
                @if($employee->photo)
                    <img src="{{ asset('storage/'.$employee->photo) }}" class="h-full w-full object-cover">
                @else
                    {{ strtoupper(substr($employee->name,0,1)) }}
                @endif
            </div>
            <div class="text-center sm:text-left">
                <h3 class="text-xl font-bold text-slate-800">{{ $employee->name }}</h3>
                <p class="text-sm text-slate-500 mt-0.5">{{ $employee->designationRel->name ?? $employee->designation ?? 'Staff' }} &bull; {{ $employee->departmentRel->name ?? $employee->department ?? 'General' }}</p>
                <div class="flex flex-wrap items-center gap-2 mt-3 justify-center sm:justify-start">
                    <span class="pf-status-pill {{ $employee->status==='Active' ? 'status-active' : 'status-inactive' }}">
                        <i class="fa-solid fa-circle" style="font-size:.5rem;"></i> {{ $employee->status }}
                    </span>
                    @if(count($missingFields) > 0)
                        <span class="pf-status-pill" style="background:#fef3c7;color:#b45309;border:1px solid #fde68a;" title="Missing: {{ implode(', ', $missingFields) }}">
                            <i class="fa-solid fa-triangle-exclamation text-amber-600"></i> Incomplete Profile
                        </span>
                    @else
                        <span class="pf-status-pill" style="background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i> Profile Complete
                        </span>
                    @endif
                    <span class="pf-status-pill" style="background:#ede9fe;color:#7c3aed;"><i class="fa-solid fa-id-badge"></i> {{ $employee->employee_id }}</span>
                    @if($employee->employment_type)
                    <span class="pf-status-pill" style="background:#fff0f6;color:#d6336c;"><i class="fa-solid fa-briefcase"></i> {{ $employee->employment_type }}</span>
                    @endif
                    <a href="{{ route('employee.profile.edit') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-200/80 hover:bg-indigo-600 hover:text-white px-3.5 py-1 rounded-md transition-all shadow-xs hover:shadow-sm">
                        <i class="fa-solid fa-user-pen text-[11px]"></i> Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Personal Info --}}
<div class="pf-card">
    <div class="pf-section-title"><i class="fa-solid fa-user"></i> Personal Information</div>
    <div class="pf-grid">
        <div class="pf-field"><span class="pf-label">Full Name</span><span class="pf-value">{{ $employee->name }}</span></div>
        <div class="pf-field"><span class="pf-label">Date of Birth</span><span class="pf-value {{ !$employee->dob ? 'empty' : '' }}">{{ $employee->dob ? \Carbon\Carbon::parse($employee->dob)->format('d-m-Y') : 'Not provided' }}</span></div>
        <div class="pf-field"><span class="pf-label">Gender</span><span class="pf-value {{ !$employee->gender ? 'empty' : '' }}">{{ $employee->gender ?? 'Not provided' }}</span></div>
        <div class="pf-field"><span class="pf-label">Blood Group</span><span class="pf-value {{ !$employee->blood_group ? 'empty' : '' }}">{{ $employee->blood_group ?? 'Not provided' }}</span></div>
        <div class="pf-field"><span class="pf-label">Marital Status</span><span class="pf-value {{ !$employee->marital_status ? 'empty' : '' }}">{{ $employee->marital_status ?? 'Not provided' }}</span></div>
        <div class="pf-field"><span class="pf-label">Personal Email</span><span class="pf-value {{ !$employee->personal_email ? 'empty' : '' }}">{{ $employee->personal_email ?? 'Not provided' }}</span></div>
        <div class="pf-field"><span class="pf-label">Personal Phone</span><span class="pf-value {{ !$employee->personal_phone ? 'empty' : '' }}">{{ $employee->personal_phone ?? 'Not provided' }}</span></div>
        <div class="pf-field"><span class="pf-label">Office Phone</span><span class="pf-value {{ !$employee->office_phone ? 'empty' : '' }}">{{ $employee->office_phone ?? 'Not provided' }}</span></div>
    </div>
    @if($employee->present_address || $employee->permanent_address)
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-14 mt-4">
        <div class="pf-field"><span class="pf-label">Present Address</span><span class="pf-value" style="white-space:pre-line;">{{ $employee->present_address ?? '—' }}</span></div>
        <div class="pf-field"><span class="pf-label">Permanent Address</span><span class="pf-value" style="white-space:pre-line;">{{ $employee->permanent_address ?? '—' }}</span></div>
    </div>
    @endif
</div>

{{-- Identity & Documents --}}
<div class="pf-card">
    <div class="pf-section-title"><i class="fa-solid fa-id-card"></i> Identity Documents & Numbers</div>
    <div class="pf-grid mb-4">
        <div class="pf-field"><span class="pf-label">Aadhaar Number</span><span class="pf-value {{ !$employee->aadhaar ? 'empty' : '' }}">{{ $employee->aadhaar ?? 'Not provided' }}</span></div>
        <div class="pf-field"><span class="pf-label">PAN Number</span><span class="pf-value {{ !$employee->pan ? 'empty' : '' }}">{{ $employee->pan ?? 'Not provided' }}</span></div>
        <div class="pf-field"><span class="pf-label">Passport Number</span><span class="pf-value {{ !$employee->passport ? 'empty' : '' }}">{{ $employee->passport ?? 'Not provided' }}</span></div>
        <div class="pf-field"><span class="pf-label">Driving License</span><span class="pf-value {{ !$employee->driving_license ? 'empty' : '' }}">{{ $employee->driving_license ?? 'Not provided' }}</span></div>
    </div>
    <div class="flex flex-wrap gap-3 mt-2 border-t border-slate-100 pt-4">
        @if($employee->aadhaar_file)
            <a href="{{ asset('storage/'.$employee->aadhaar_file) }}" target="_blank" class="pf-doc-link"><i class="fa-solid fa-file-pdf"></i> View Aadhaar Card</a>
        @endif
        @if($employee->pan_file)
            <a href="{{ asset('storage/'.$employee->pan_file) }}" target="_blank" class="pf-doc-link"><i class="fa-solid fa-file-pdf"></i> View PAN Card</a>
        @endif
        @if($employee->passport_file)
            <a href="{{ asset('storage/'.$employee->passport_file) }}" target="_blank" class="pf-doc-link"><i class="fa-solid fa-file-pdf"></i> View Passport</a>
        @endif
        @if($employee->license_file)
            <a href="{{ asset('storage/'.$employee->license_file) }}" target="_blank" class="pf-doc-link"><i class="fa-solid fa-file-pdf"></i> View Driving License</a>
        @endif
        @if(!$employee->aadhaar_file && !$employee->pan_file && !$employee->passport_file && !$employee->license_file)
            <span class="text-xs text-slate-400 italic">No document files uploaded yet.</span>
        @endif
    </div>
</div>

{{-- Employment Info --}}
<div class="pf-card">
    <div class="pf-section-title"><i class="fa-solid fa-briefcase"></i> Employment Details</div>
    <div class="pf-grid">
        <div class="pf-field"><span class="pf-label">Employee ID</span><span class="pf-value">{{ $employee->employee_id }}</span></div>
        <div class="pf-field"><span class="pf-label">Company Email</span><span class="pf-value {{ !$employee->company_email ? 'empty' : '' }}">{{ $employee->company_email ?? 'Not provided' }}</span></div>
        <div class="pf-field"><span class="pf-label">Department</span><span class="pf-value {{ !($employee->departmentRel->name ?? $employee->department) ? 'empty' : '' }}">{{ $employee->departmentRel->name ?? $employee->department ?? 'Not assigned' }}</span></div>
        <div class="pf-field"><span class="pf-label">Designation</span><span class="pf-value {{ !($employee->designationRel->name ?? $employee->designation) ? 'empty' : '' }}">{{ $employee->designationRel->name ?? $employee->designation ?? 'Not assigned' }}</span></div>
        <div class="pf-field"><span class="pf-label">Joining Date</span><span class="pf-value {{ !$employee->joining_date ? 'empty' : '' }}">{{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('d-m-Y') : 'Not provided' }}</span></div>
        <div class="pf-field"><span class="pf-label">Employment Type</span><span class="pf-value {{ !$employee->employment_type ? 'empty' : '' }}">{{ $employee->employment_type ?? 'Not provided' }}</span></div>
        <div class="pf-field"><span class="pf-label">Work Location</span><span class="pf-value {{ !$employee->work_location ? 'empty' : '' }}">{{ $employee->work_location ?? 'Not provided' }}</span></div>
        <div class="pf-field"><span class="pf-label">Reporting Manager</span><span class="pf-value {{ !$employee->reporting_manager ? 'empty' : '' }}">{{ $employee->reporting_manager ?? 'Not provided' }}</span></div>
    </div>
</div>

{{-- Emergency Contact --}}
@if($employee->emergency_name || $employee->emergency_phone)
<div class="pf-card">
    <div class="pf-section-title"><i class="fa-solid fa-phone-volume"></i> Emergency Contact</div>
    <div class="pf-grid">
        <div class="pf-field"><span class="pf-label">Name</span><span class="pf-value">{{ $employee->emergency_name ?? '—' }}</span></div>
        <div class="pf-field"><span class="pf-label">Relation</span><span class="pf-value">{{ $employee->emergency_relation ?? '—' }}</span></div>
        <div class="pf-field"><span class="pf-label">Phone</span><span class="pf-value">{{ $employee->emergency_phone ?? '—' }}</span></div>
        <div class="pf-field"><span class="pf-label">Alternate Phone</span><span class="pf-value">{{ $employee->emergency_alt_phone ?? '—' }}</span></div>
    </div>
</div>
@endif

{{-- Bank Details --}}
@if($employee->bank_name || $employee->account_number || $employee->bank_document)
<div class="pf-card">
    <div class="pf-section-title"><i class="fa-solid fa-building-columns"></i> Bank Details</div>
    <div class="pf-grid mb-4">
        <div class="pf-field"><span class="pf-label">Bank Name</span><span class="pf-value">{{ $employee->bank_name ?? '—' }}</span></div>
        <div class="pf-field"><span class="pf-label">Account Holder</span><span class="pf-value">{{ $employee->account_holder ?? '—' }}</span></div>
        <div class="pf-field"><span class="pf-label">Account Number</span><span class="pf-value">{{ $employee->account_number ? str_repeat('•',strlen($employee->account_number)-4).substr($employee->account_number,-4) : '—' }}</span></div>
        <div class="pf-field"><span class="pf-label">IFSC Code</span><span class="pf-value">{{ $employee->ifsc ?? '—' }}</span></div>
    </div>
    @if($employee->bank_document)
    <div class="border-t border-slate-100 pt-4">
        <a href="{{ asset('storage/'.$employee->bank_document) }}" target="_blank" class="pf-doc-link"><i class="fa-solid fa-file-pdf"></i> View Bank Document</a>
    </div>
    @endif
</div>
@endif

{{-- Education Details --}}
@if($employee->education && $employee->education->isNotEmpty())
<div class="pf-card">
    <div class="pf-section-title"><i class="fa-solid fa-graduation-cap"></i> Education Details</div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse mt-2">
            <thead>
                <tr>
                    <th class="py-2 px-3 bg-slate-50 text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-100 rounded-tl-lg">Qualification</th>
                    <th class="py-2 px-3 bg-slate-50 text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-100">Course</th>
                    <th class="py-2 px-3 bg-slate-50 text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-100">Institution</th>
                    <th class="py-2 px-3 bg-slate-50 text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-100">University / Board</th>
                    <th class="py-2 px-3 bg-slate-50 text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-100">Year</th>
                    <th class="py-2 px-3 bg-slate-50 text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-100">Percentage</th>
                    <th class="py-2 px-3 bg-slate-50 text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-100 rounded-tr-lg text-center">Certificate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employee->education as $edu)
                <tr class="border-b border-slate-50 last:border-0 hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-3 text-sm font-semibold text-slate-800">{{ $edu->qualification ?? '—' }}</td>
                    <td class="py-3 px-3 text-sm text-slate-600">{{ $edu->course ?? '—' }}</td>
                    <td class="py-3 px-3 text-sm text-slate-600">{{ $edu->institution ?? '—' }}</td>
                    <td class="py-3 px-3 text-sm text-slate-600">{{ $edu->university ?? '—' }}</td>
                    <td class="py-3 px-3 text-sm font-medium text-slate-700">{{ $edu->year ?? '—' }}</td>
                    <td class="py-3 px-3 text-sm font-medium text-indigo-600">{{ $edu->percentage ? $edu->percentage . '%' : '—' }}</td>
                    <td class="py-3 px-3 text-sm text-center">
                        @if($edu->certificate)
                            <a href="{{ asset('storage/'.$edu->certificate) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800" title="View Certificate"><i class="fa-solid fa-file-certificate text-lg"></i> View</a>
                        @else
                            <span class="text-slate-300 text-xs">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Experience Details --}}
@if($employee->experience && $employee->experience->isNotEmpty())
<div class="pf-card">
    <div class="pf-section-title"><i class="fa-solid fa-briefcase"></i> Experience Details</div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse mt-2">
            <thead>
                <tr>
                    <th class="py-2 px-3 bg-slate-50 text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-100 rounded-tl-lg">Company</th>
                    <th class="py-2 px-3 bg-slate-50 text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-100">Designation</th>
                    <th class="py-2 px-3 bg-slate-50 text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-100">Duration</th>
                    <th class="py-2 px-3 bg-slate-50 text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-100">Total Years</th>
                    <th class="py-2 px-3 bg-slate-50 text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-100 rounded-tr-lg text-center">Certificate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employee->experience as $exp)
                <tr class="border-b border-slate-50 last:border-0 hover:bg-slate-50/50 transition-colors">
                    <td class="py-3 px-3 text-sm font-semibold text-slate-800">{{ $exp->company ?? '—' }}</td>
                    <td class="py-3 px-3 text-sm text-slate-600">{{ $exp->designation ?? '—' }}</td>
                    <td class="py-3 px-3 text-sm text-slate-600">
                        {{ $exp->from_date ? \Carbon\Carbon::parse($exp->from_date)->format('M Y') : '—' }} 
                        to 
                        {{ $exp->to_date ? \Carbon\Carbon::parse($exp->to_date)->format('M Y') : '—' }}
                    </td>
                    <td class="py-3 px-3 text-sm font-medium text-indigo-600">{{ $exp->years ? $exp->years . ' Years' : '—' }}</td>
                    <td class="py-3 px-3 text-sm text-center">
                        @if($exp->certificate)
                            <a href="{{ asset('storage/'.$exp->certificate) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800" title="View Certificate"><i class="fa-solid fa-file-contract text-lg"></i> View</a>
                        @else
                            <span class="text-slate-300 text-xs">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
