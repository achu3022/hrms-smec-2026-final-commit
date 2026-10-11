@extends('employee.portal.layout')

@push('styles')
<style>
    .ep-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #3730a3 100%);
        border-radius: 20px;
        padding: 28px;
        color: #ffffff;
        margin-bottom: 24px;
        box-shadow: 0 10px 30px rgba(30, 27, 75, 0.25);
        position: relative;
        overflow: hidden;
    }
    .ep-hero::before {
        content: '';
        position: absolute;
        top: -50%; right: -20%;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.3) 0%, rgba(255, 255, 255, 0) 70%);
        pointer-events: none;
    }
    .ep-card {
        background: #ffffff;
        border: 1px solid #e0e7ff;
        border-radius: 18px;
        padding: 26px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .ep-card:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
    }
    .ep-title {
        font-size: 0.9rem;
        font-weight: 800;
        color: #1e1b4b;
        background: linear-gradient(90deg, #e0e7ff 0%, #eef2ff 100%);
        padding: 10px 16px;
        border-radius: 12px;
        border-left: 4px solid #4f46e5;
        margin-bottom: 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .ep-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
    @media(max-width: 640px) { .ep-grid { grid-template-columns: 1fr; } }
    
    .ep-group { display: flex; flex-direction: column; gap: 6px; }
    .ep-label { font-size: 0.78rem; font-weight: 700; color: #475569; display: flex; align-items: center; justify-content: space-between; }
    
    .ep-input, .ep-select, .ep-textarea {
        border: 1.5px solid #cbd5e1;
        border-radius: 11px;
        padding: 10px 14px;
        font-size: 0.88rem;
        color: #1e293b;
        background: #f8fafc;
        outline: none;
        transition: all 0.2s ease;
        font-family: inherit;
        width: 100%;
        box-sizing: border-box;
    }
    .ep-input:focus, .ep-select:focus, .ep-textarea:focus {
        border-color: #6366f1;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
    }
    .ep-input:disabled, .ep-select:disabled {
        background: #f1f5f9;
        color: #64748b;
        border-style: dashed;
        border-color: #cbd5e1;
        cursor: not-allowed;
    }
    .ep-textarea { resize: vertical; min-height: 90px; }
    
    .ep-btn-primary {
        background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%);
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 13px 32px;
        font-size: 0.95rem;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4);
    }
    .ep-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.5);
    }
    .ep-btn-cancel {
        background: #ffffff;
        color: #475569;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        padding: 12px 24px;
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .ep-btn-cancel:hover { background: #f1f5f9; color: #0f172a; border-color: #94a3b8; }
    
    .dynamic-card {
        background: #f8fafc;
        border: 1.5px solid #e0e7ff;
        border-radius: 14px;
        padding: 18px;
        margin-bottom: 14px;
        position: relative;
        transition: all 0.2s;
    }
    .dynamic-card:hover { border-color: #c7d2fe; background: #ffffff; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    .btn-remove-row {
        position: absolute; top: 14px; right: 14px;
        background: #fee2e2; color: #dc2626; border: none;
        border-radius: 8px; width: 30px; height: 30px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; font-size: 0.82rem; transition: background 0.2s;
    }
    .btn-remove-row:hover { background: #fca5a5; }
    .btn-add-row {
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
        color: #3730a3; border: none; border-radius: 10px;
        padding: 9px 18px; font-size: 0.82rem; font-weight: 800;
        cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
        transition: all 0.2s;
    }
    .btn-add-row:hover { background: #a5b4fc; color: #1e1b4b; transform: translateY(-1px); }
    
    .avatar-wrapper {
        position: relative;
        width: 86px; height: 86px;
        border-radius: 50%;
        border: 3px solid #6366f1;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        overflow: hidden;
        background: #4f46e5;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')

{{-- Hero Top Banner --}}
<div class="ep-hero">
    <div class="flex flex-col md:flex-row items-center justify-between gap-5">
        <div class="flex items-center gap-5">
            <div class="avatar-wrapper flex items-center justify-center text-white text-2xl font-black">
                @if($employee->photo)
                    <img src="{{ asset('storage/'.$employee->photo) }}" id="avatar_preview" class="w-full h-full object-cover">
                @else
                    <span id="avatar_initial">{{ strtoupper(substr($employee->name,0,1)) }}</span>
                @endif
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-extrabold tracking-tight text-white">{{ $employee->name }}</h1>
                    <span class="bg-indigo-500/40 text-indigo-200 border border-indigo-400/30 text-[11px] font-extrabold px-2.5 py-0.5 rounded-full">Active</span>
                </div>
                <p class="text-indigo-200 text-sm font-medium mt-1 flex items-center gap-2">
                    <span><i class="fa-solid fa-briefcase text-indigo-300 mr-1"></i>{{ $employee->designationRel->name ?? $employee->designation ?? 'Employee' }}</span>
                    <span>•</span>
                    <span><i class="fa-solid fa-building text-indigo-300 mr-1"></i>{{ $employee->departmentRel->name ?? $employee->department ?? 'General' }}</span>
                </p>
                <div class="mt-2 inline-flex items-center gap-2 bg-white/10 backdrop-blur-md px-3 py-1 rounded-lg text-xs font-semibold text-indigo-100">
                    <i class="fa-solid fa-id-badge text-indigo-300"></i> ID: #{{ $employee->employee_id }}
                </div>
            </div>
        </div>
        <a href="{{ route('employee.profile.index') }}" class="ep-btn-cancel self-center sm:self-start text-xs">
            <i class="fa-solid fa-arrow-left"></i> View Profile
        </a>
    </div>
</div>

@if(isset($errors) && $errors->any())
    <div class="bg-red-50 border-2 border-red-200 text-red-800 text-xs font-semibold rounded-2xl p-4 mb-6 shadow-sm">
        <div class="font-extrabold text-sm mb-1.5 flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation text-red-600 text-base"></i> Please check the required fields below:
        </div>
        <ul class="list-disc pl-6 space-y-1 text-red-700">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('employee.profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- Personal Information --}}
    <div class="ep-card">
        <div class="ep-title">
            <span class="flex items-center gap-2"><i class="fa-solid fa-user-gear text-indigo-600 text-base"></i> Personal Information</span>
            <span class="text-xs font-normal text-slate-500">Fields marked with <span class="text-red-500 font-bold">*</span> are required</span>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-5 mb-6 pb-6 border-b border-slate-100">
            <div class="avatar-wrapper flex items-center justify-center text-white text-2xl font-black">
                @if($employee->photo)
                    <img src="{{ asset('storage/'.$employee->photo) }}" id="avatar_preview_section" class="w-full h-full object-cover">
                @else
                    <span id="avatar_initial_section">{{ strtoupper(substr($employee->name,0,1)) }}</span>
                @endif
            </div>
            <div class="ep-group flex-1">
                <label class="ep-label">Profile Photo <span class="text-red-500 font-bold">*</span></label>
                <input type="file" name="photo" id="photo_input" class="ep-input" accept="image/*" {{ $employee->photo ? '' : 'required' }}>
                <span class="text-[11px] text-slate-400 font-medium"><i class="fa-solid fa-circle-info mr-1"></i> Accepted formats: JPG, PNG, WEBP (Max 5MB)</span>
            </div>
        </div>

        <div class="ep-grid">
            <div class="ep-group">
                <label class="ep-label">Full Name</label>
                <input type="text" value="{{ $employee->name }}" class="ep-input" disabled>
            </div>
            <div class="ep-group">
                <label class="ep-label">
                    Employee ID 
                    <span class="bg-slate-100 text-slate-500 text-[10px] font-extrabold px-2 py-0.5 rounded-md"><i class="fa-solid fa-lock text-[9px] mr-0.5"></i> Restricted</span>
                </label>
                <input type="text" value="{{ $employee->employee_id }}" class="ep-input" disabled>
            </div>
            <div class="ep-group">
                <label class="ep-label">
                    Department
                    <span class="bg-slate-100 text-slate-500 text-[10px] font-extrabold px-2 py-0.5 rounded-md"><i class="fa-solid fa-lock text-[9px] mr-0.5"></i> Restricted</span>
                </label>
                <input type="text" value="{{ $employee->departmentRel->name ?? '' }}" class="ep-input" disabled>
            </div>
            <div class="ep-group">
                <label class="ep-label">
                    Date of Birth
                    <span class="bg-slate-100 text-slate-500 text-[10px] font-extrabold px-2 py-0.5 rounded-md"><i class="fa-solid fa-lock text-[9px] mr-0.5"></i> Restricted</span>
                </label>
                <input type="date" value="{{ $employee->dob }}" class="ep-input" disabled>
            </div>
            <div class="ep-group">
                <label class="ep-label">Personal Email <span class="text-red-500 font-bold">*</span></label>
                <input type="email" name="personal_email" value="{{ old('personal_email', $employee->personal_email) }}" class="ep-input" placeholder="e.g. john@gmail.com" required>
            </div>
            <div class="ep-group">
                <label class="ep-label">Personal Phone <span class="text-red-500 font-bold">*</span></label>
                <input type="text" name="personal_phone" value="{{ old('personal_phone', $employee->personal_phone) }}" class="ep-input" placeholder="e.g. +91 9876543210" required>
            </div>
            <div class="ep-group">
                <label class="ep-label">Office Phone (Optional)</label>
                <input type="text" name="office_phone" value="{{ old('office_phone', $employee->office_phone) }}" class="ep-input" placeholder="e.g. +91 9876543211">
            </div>
            <div class="ep-group">
                <label class="ep-label">Gender <span class="text-red-500 font-bold">*</span></label>
                <select name="gender" class="ep-select" required>
                    <option value="">Select Gender</option>
                    <option value="Male" {{ old('gender', $employee->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender', $employee->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ old('gender', $employee->gender) === 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="ep-group">
                <label class="ep-label">Blood Group <span class="text-red-500 font-bold">*</span></label>
                <select name="blood_group" class="ep-select" required>
                    <option value="">Select Blood Group</option>
                    @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                        <option value="{{ $bg }}" {{ old('blood_group', $employee->blood_group) === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ep-group">
                <label class="ep-label">Marital Status <span class="text-red-500 font-bold">*</span></label>
                <select name="marital_status" class="ep-select" required>
                    <option value="">Select Marital Status</option>
                    <option value="Single" {{ old('marital_status', $employee->marital_status) === 'Single' ? 'selected' : '' }}>Single</option>
                    <option value="Married" {{ old('marital_status', $employee->marital_status) === 'Married' ? 'selected' : '' }}>Married</option>
                    <option value="Divorced" {{ old('marital_status', $employee->marital_status) === 'Divorced' ? 'selected' : '' }}>Divorced</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
            <div class="ep-group">
                <label class="ep-label">Present Address <span class="text-red-500 font-bold">*</span></label>
                <textarea name="present_address" class="ep-textarea" placeholder="Enter present residential address" required>{{ old('present_address', $employee->present_address) }}</textarea>
            </div>
            <div class="ep-group">
                <label class="ep-label">Permanent Address <span class="text-red-500 font-bold">*</span></label>
                <textarea name="permanent_address" class="ep-textarea" placeholder="Enter permanent address" required>{{ old('permanent_address', $employee->permanent_address) }}</textarea>
            </div>
        </div>
    </div>

    {{-- Emergency Contact --}}
    <div class="ep-card">
        <div class="ep-title"><span class="flex items-center gap-2"><i class="fa-solid fa-phone-volume text-indigo-600 text-base"></i> Emergency Contact Details</span></div>
        <div class="ep-grid">
            <div class="ep-group">
                <label class="ep-label">Contact Person Name <span class="text-red-500 font-bold">*</span></label>
                <input type="text" name="emergency_name" value="{{ old('emergency_name', $employee->emergency_name) }}" class="ep-input" placeholder="e.g. Jane Doe" required>
            </div>
            <div class="ep-group">
                <label class="ep-label">Relationship <span class="text-red-500 font-bold">*</span></label>
                <input type="text" name="emergency_relation" value="{{ old('emergency_relation', $employee->emergency_relation) }}" class="ep-input" placeholder="e.g. Spouse / Parent" required>
            </div>
            <div class="ep-group">
                <label class="ep-label">Primary Phone <span class="text-red-500 font-bold">*</span></label>
                <input type="text" name="emergency_phone" value="{{ old('emergency_phone', $employee->emergency_phone) }}" class="ep-input" placeholder="e.g. +91 9876543210" required>
            </div>
            <div class="ep-group">
                <label class="ep-label">Alternate Phone (Optional)</label>
                <input type="text" name="emergency_alt_phone" value="{{ old('emergency_alt_phone', $employee->emergency_alt_phone) }}" class="ep-input" placeholder="e.g. +91 9876543211">
            </div>
        </div>
    </div>

    {{-- Identity & Bank Details --}}
    <div class="ep-card">
        <div class="ep-title"><span class="flex items-center gap-2"><i class="fa-solid fa-id-card text-indigo-600 text-base"></i> Identity Documents & Bank Details</span></div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
            <div class="ep-group">
                <label class="ep-label">Aadhaar Number <span class="text-red-500 font-bold">*</span></label>
                <input type="text" name="aadhaar" value="{{ old('aadhaar', $employee->aadhaar) }}" class="ep-input mb-2" placeholder="12-digit Aadhaar" required>
                <label class="ep-label text-slate-400 font-normal">Aadhaar Document File (Optional)</label>
                <input type="file" name="aadhaar_file" class="ep-input" accept=".pdf,.jpg,.jpeg,.png">
            </div>
            <div class="ep-group">
                <label class="ep-label">PAN Number (Optional)</label>
                <input type="text" name="pan" value="{{ old('pan', $employee->pan) }}" class="ep-input mb-2" placeholder="10-digit PAN">
                <label class="ep-label text-slate-400 font-normal">PAN Document File (Optional)</label>
                <input type="file" name="pan_file" class="ep-input" accept=".pdf,.jpg,.jpeg,.png">
            </div>
            <div class="ep-group">
                <label class="ep-label">Passport Number (Optional)</label>
                <input type="text" name="passport" value="{{ old('passport', $employee->passport) }}" class="ep-input mb-2" placeholder="Passport Number">
                <label class="ep-label text-slate-400 font-normal">Passport File (Optional)</label>
                <input type="file" name="passport_file" class="ep-input" accept=".pdf,.jpg,.jpeg,.png">
            </div>
            <div class="ep-group">
                <label class="ep-label">Driving License Number (Optional)</label>
                <input type="text" name="driving_license" value="{{ old('driving_license', $employee->driving_license) }}" class="ep-input mb-2" placeholder="Driving License Number">
                <label class="ep-label text-slate-400 font-normal">Driving License File (Optional)</label>
                <input type="file" name="license_file" class="ep-input" accept=".pdf,.jpg,.jpeg,.png">
            </div>
        </div>

        <div class="border-t border-slate-100 pt-6">
            <h4 class="font-black text-slate-800 text-xs uppercase tracking-wider mb-5 flex items-center gap-2">
                <i class="fa-solid fa-building-columns text-indigo-600"></i> Bank Account Information
            </h4>
            <div class="ep-grid">
                <div class="ep-group">
                    <label class="ep-label">Bank Name <span class="text-red-500 font-bold">*</span></label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $employee->bank_name) }}" class="ep-input" placeholder="e.g. HDFC Bank" required>
                </div>
                <div class="ep-group">
                    <label class="ep-label">Account Holder Name <span class="text-red-500 font-bold">*</span></label>
                    <input type="text" name="account_holder" value="{{ old('account_holder', $employee->account_holder) }}" class="ep-input" placeholder="Name as per bank" required>
                </div>
                <div class="ep-group">
                    <label class="ep-label">Account Number <span class="text-red-500 font-bold">*</span></label>
                    <input type="text" name="account_number" value="{{ old('account_number', $employee->account_number) }}" class="ep-input" placeholder="Bank Account Number" required>
                </div>
                <div class="ep-group">
                    <label class="ep-label">IFSC Code <span class="text-red-500 font-bold">*</span></label>
                    <input type="text" name="ifsc" value="{{ old('ifsc', $employee->ifsc) }}" class="ep-input" placeholder="e.g. HDFC0001234" required>
                </div>
                <div class="ep-group">
                    <label class="ep-label">Branch Name <span class="text-red-500 font-bold">*</span></label>
                    <input type="text" name="branch" value="{{ old('branch', $employee->branch) }}" class="ep-input" placeholder="Branch location" required>
                </div>
                <div class="ep-group">
                    <label class="ep-label">Bank Document (Optional)</label>
                    <input type="file" name="bank_document" class="ep-input" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>
        </div>
    </div>

    {{-- Education Details --}}
    <div class="ep-card">
        <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-4">
            <div class="ep-title m-0 p-0 bg-none border-none border-0 shadow-none"><span class="flex items-center gap-2"><i class="fa-solid fa-graduation-cap text-indigo-600 text-base"></i> Education Details</span></div>
            <button type="button" id="add_edu_btn" class="btn-add-row"><i class="fa-solid fa-plus"></i> Add Qualification</button>
        </div>
        <div id="edu_wrapper">
            @forelse($employee->education as $index => $edu)
            <div class="dynamic-card edu-row">
                <button type="button" class="btn-remove-row remove-edu-row" title="Remove"><i class="fa-solid fa-trash"></i></button>
                <input type="hidden" name="education[{{ $index }}][id]" value="{{ $edu->id }}">
                <div class="ep-grid">
                    <div class="ep-group">
                        <label class="ep-label">Qualification <span class="text-red-500 font-bold">*</span></label>
                        <input type="text" name="education[{{ $index }}][qualification]" value="{{ old("education.{$index}.qualification", $edu->qualification) }}" class="ep-input" placeholder="e.g. B.Tech / BCA" required>
                    </div>
                    <div class="ep-group">
                        <label class="ep-label">Course / Specialization</label>
                        <input type="text" name="education[{{ $index }}][course]" value="{{ old("education.{$index}.course", $edu->course) }}" class="ep-input" placeholder="e.g. Computer Science">
                    </div>
                    <div class="ep-group">
                        <label class="ep-label">Institution Name</label>
                        <input type="text" name="education[{{ $index }}][institution]" value="{{ old("education.{$index}.institution", $edu->institution) }}" class="ep-input" placeholder="College / School Name">
                    </div>
                    <div class="ep-group">
                        <label class="ep-label">University / Board</label>
                        <input type="text" name="education[{{ $index }}][university]" value="{{ old("education.{$index}.university", $edu->university) }}" class="ep-input" placeholder="University Name">
                    </div>
                    <div class="ep-group">
                        <label class="ep-label">Passing Year</label>
                        <input type="text" name="education[{{ $index }}][year]" value="{{ old("education.{$index}.year", $edu->year) }}" class="ep-input" placeholder="YYYY">
                    </div>
                    <div class="ep-group">
                        <label class="ep-label">Percentage / CGPA</label>
                        <input type="text" name="education[{{ $index }}][percentage]" value="{{ old("education.{$index}.percentage", $edu->percentage) }}" class="ep-input" placeholder="e.g. 85% or 8.5 CGPA">
                    </div>
                </div>
            </div>
            @empty
            <div class="dynamic-card edu-row">
                <button type="button" class="btn-remove-row remove-edu-row" title="Remove"><i class="fa-solid fa-trash"></i></button>
                <div class="ep-grid">
                    <div class="ep-group">
                        <label class="ep-label">Qualification <span class="text-red-500 font-bold">*</span></label>
                        <input type="text" name="education[0][qualification]" class="ep-input" placeholder="e.g. B.Tech / BCA" required>
                    </div>
                    <div class="ep-group">
                        <label class="ep-label">Course / Specialization</label>
                        <input type="text" name="education[0][course]" class="ep-input" placeholder="e.g. Computer Science">
                    </div>
                    <div class="ep-group">
                        <label class="ep-label">Institution Name</label>
                        <input type="text" name="education[0][institution]" class="ep-input" placeholder="College / School Name">
                    </div>
                    <div class="ep-group">
                        <label class="ep-label">University / Board</label>
                        <input type="text" name="education[0][university]" class="ep-input" placeholder="University Name">
                    </div>
                    <div class="ep-group">
                        <label class="ep-label">Passing Year</label>
                        <input type="text" name="education[0][year]" class="ep-input" placeholder="YYYY">
                    </div>
                    <div class="ep-group">
                        <label class="ep-label">Percentage / CGPA</label>
                        <input type="text" name="education[0][percentage]" class="ep-input" placeholder="e.g. 85% or 8.5 CGPA">
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Experience Details --}}
    <div class="ep-card">
        <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-4">
            <div class="ep-title m-0 p-0 bg-none border-none border-0 shadow-none"><span class="flex items-center gap-2"><i class="fa-solid fa-briefcase text-indigo-600 text-base"></i> Work Experience</span></div>
            <button type="button" id="add_exp_btn" class="btn-add-row"><i class="fa-solid fa-plus"></i> Add Experience</button>
        </div>
        <div id="exp_wrapper">
            @forelse($employee->experience as $index => $exp)
            <div class="dynamic-card exp-row">
                <button type="button" class="btn-remove-row remove-exp-row" title="Remove"><i class="fa-solid fa-trash"></i></button>
                <input type="hidden" name="experience[{{ $index }}][id]" value="{{ $exp->id }}">
                <div class="ep-grid">
                    <div class="ep-group">
                        <label class="ep-label">Company Name</label>
                        <input type="text" name="experience[{{ $index }}][company]" value="{{ old("experience.{$index}.company", $exp->company) }}" class="ep-input" placeholder="Company Name">
                    </div>
                    <div class="ep-group">
                        <label class="ep-label">Designation</label>
                        <input type="text" name="experience[{{ $index }}][designation]" value="{{ old("experience.{$index}.designation", $exp->designation) }}" class="ep-input" placeholder="Designation held">
                    </div>
                    <div class="ep-group">
                        <label class="ep-label">From Date</label>
                        <input type="date" name="experience[{{ $index }}][from_date]" value="{{ old("experience.{$index}.from_date", $exp->from_date) }}" class="ep-input">
                    </div>
                    <div class="ep-group">
                        <label class="ep-label">To Date</label>
                        <input type="date" name="experience[{{ $index }}][to_date]" value="{{ old("experience.{$index}.to_date", $exp->to_date) }}" class="ep-input">
                    </div>
                    <div class="ep-group">
                        <label class="ep-label">Total Years</label>
                        <input type="text" name="experience[{{ $index }}][years]" value="{{ old("experience.{$index}.years", $exp->years) }}" class="ep-input" placeholder="e.g. 2.5">
                    </div>
                </div>
            </div>
            @empty
            <div class="dynamic-card exp-row">
                <button type="button" class="btn-remove-row remove-exp-row" title="Remove"><i class="fa-solid fa-trash"></i></button>
                <div class="ep-grid">
                    <div class="ep-group">
                        <label class="ep-label">Company Name</label>
                        <input type="text" name="experience[0][company]" class="ep-input" placeholder="Company Name">
                    </div>
                    <div class="ep-group">
                        <label class="ep-label">Designation</label>
                        <input type="text" name="experience[0][designation]" class="ep-input" placeholder="Designation held">
                    </div>
                    <div class="ep-group">
                        <label class="ep-label">From Date</label>
                        <input type="date" name="experience[0][from_date]" class="ep-input">
                    </div>
                    <div class="ep-group">
                        <label class="ep-label">To Date</label>
                        <input type="date" name="experience[0][to_date]" class="ep-input">
                    </div>
                    <div class="ep-group">
                        <label class="ep-label">Total Years</label>
                        <input type="text" name="experience[0][years]" class="ep-input" placeholder="e.g. 2.5">
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Form Actions --}}
    <div class="flex items-center justify-end gap-4 mb-12">
        <a href="{{ route('employee.profile.index') }}" class="ep-btn-cancel">Cancel</a>
        <button type="submit" class="ep-btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Profile Changes</button>
    </div>
</form>

@push('scripts')
<script>
$(document).ready(function() {
    // Dynamic Education Rows
    var eduCount = {{ count($employee->education) > 0 ? count($employee->education) : 1 }};
    $("#add_edu_btn").on("click", function() {
        var html = '<div class="dynamic-card edu-row">' +
            '<button type="button" class="btn-remove-row remove-edu-row" title="Remove"><i class="fa-solid fa-trash"></i></button>' +
            '<div class="ep-grid">' +
                '<div class="ep-group"><label class="ep-label">Qualification <span class="text-red-500 font-bold">*</span></label><input type="text" name="education[' + eduCount + '][qualification]" class="ep-input" placeholder="e.g. B.Tech / BCA" required></div>' +
                '<div class="ep-group"><label class="ep-label">Course / Specialization</label><input type="text" name="education[' + eduCount + '][course]" class="ep-input" placeholder="e.g. Computer Science"></div>' +
                '<div class="ep-group"><label class="ep-label">Institution Name</label><input type="text" name="education[' + eduCount + '][institution]" class="ep-input" placeholder="College / School Name"></div>' +
                '<div class="ep-group"><label class="ep-label">University / Board</label><input type="text" name="education[' + eduCount + '][university]" class="ep-input" placeholder="University Name"></div>' +
                '<div class="ep-group"><label class="ep-label">Passing Year</label><input type="text" name="education[' + eduCount + '][year]" class="ep-input" placeholder="YYYY"></div>' +
                '<div class="ep-group"><label class="ep-label">Percentage / CGPA</label><input type="text" name="education[' + eduCount + '][percentage]" class="ep-input" placeholder="e.g. 85% or 8.5 CGPA"></div>' +
            '</div>' +
        '</div>';
        $("#edu_wrapper").append(html);
        eduCount++;
    });

    $(document).on("click", ".remove-edu-row", function() {
        if ($(".edu-row").length > 1) {
            $(this).closest(".edu-row").remove();
        } else {
            alert("At least one education record is required.");
        }
    });

    // Dynamic Experience Rows
    var expCount = {{ count($employee->experience) > 0 ? count($employee->experience) : 1 }};
    $("#add_exp_btn").on("click", function() {
        var html = '<div class="dynamic-card exp-row">' +
            '<button type="button" class="btn-remove-row remove-exp-row" title="Remove"><i class="fa-solid fa-trash"></i></button>' +
            '<div class="ep-grid">' +
                '<div class="ep-group"><label class="ep-label">Company Name</label><input type="text" name="experience[' + expCount + '][company]" class="ep-input" placeholder="Company Name"></div>' +
                '<div class="ep-group"><label class="ep-label">Designation</label><input type="text" name="experience[' + expCount + '][designation]" class="ep-input" placeholder="Designation held"></div>' +
                '<div class="ep-group"><label class="ep-label">From Date</label><input type="date" name="experience[' + expCount + '][from_date]" class="ep-input"></div>' +
                '<div class="ep-group"><label class="ep-label">To Date</label><input type="date" name="experience[' + expCount + '][to_date]" class="ep-input"></div>' +
                '<div class="ep-group"><label class="ep-label">Total Years</label><input type="text" name="experience[' + expCount + '][years]" class="ep-input" placeholder="e.g. 2.5"></div>' +
            '</div>' +
        '</div>';
        $("#exp_wrapper").append(html);
        expCount++;
    });

    $(document).on("click", ".remove-exp-row", function() {
        if ($(".exp-row").length > 1) {
            $(this).closest(".exp-row").remove();
        } else {
            $(this).closest(".exp-row").find("input").val("");
        }
    });

    // Photo input live preview
    $("#photo_input").on("change", function() {
        const file = this.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function(event) {
                if ($("#avatar_preview").length) {
                    $("#avatar_preview").attr("src", event.target.result);
                } else {
                    $("#avatar_initial").replaceWith('<img src="' + event.target.result + '" id="avatar_preview" class="w-full h-full object-cover">');
                }
                if ($("#avatar_preview_section").length) {
                    $("#avatar_preview_section").attr("src", event.target.result);
                } else {
                    $("#avatar_initial_section").replaceWith('<img src="' + event.target.result + '" id="avatar_preview_section" class="w-full h-full object-cover">');
                }
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush
@endsection
