<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Employee extends Model
{
    use LogsActivity;

    protected $guarded = [];

    protected $casts = [
        'provided_assets' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }

    public function education()
    {
        return $this->hasMany(EducationDetail::class);
    }

    public function experience()
    {
        return $this->hasMany(ExperienceDetail::class);
    }

    public function certificates()
    {
        return $this->hasMany(EmployeeCertificate::class);
    }

    public function departmentRel()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function designationRel()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function branchRel()
    {
        return $this->belongsTo(CompanyBranch::class, 'branch_id');
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function assetAssignments()
    {
        return $this->hasMany(AssetAssignment::class);
    }
}
