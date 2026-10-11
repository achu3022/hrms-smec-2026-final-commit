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
        'is_teamleader' => 'boolean',
        'is_profile_completed' => 'boolean',
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

    public function employeeLogin()
    {
        return $this->hasOne(EmployeeLogin::class);
    }

    /**
     * Parse shift string into start and end time details.
     */
    public function getShiftDetails(): array
    {
        $shiftStr = trim($this->shift ?? '9:00 AM - 6:00 PM');
        $parts = explode('-', $shiftStr);

        if (count($parts) === 2) {
            try {
                $start = \Carbon\Carbon::parse(trim($parts[0]));
                $end = \Carbon\Carbon::parse(trim($parts[1]));

                return [
                    'start_time' => $start->format('H:i'),
                    'end_time' => $end->format('H:i'),
                    'start_carbon' => $start,
                    'end_carbon' => $end,
                    'label' => $shiftStr,
                ];
            } catch (\Throwable $e) {
                // Fallback below
            }
        }

        return [
            'start_time' => '09:00',
            'end_time' => '18:00',
            'start_carbon' => \Carbon\Carbon::parse('09:00'),
            'end_carbon' => \Carbon\Carbon::parse('18:00'),
            'label' => '9:00 AM - 6:00 PM',
        ];
    }

    /**
     * Check if mandatory profile information is complete.
     * Mandatory fields: Qualification, Blood Group, Emergency Contact, Joining Date, Aadhaar Number.
     */
    public function checkIsProfileComplete(): bool
    {
        return count($this->getMissingProfileFields()) === 0;
    }

    /**
     * Get list of missing mandatory profile fields.
     */
    public function getMissingProfileFields(): array
    {
        $missing = [];

        // 1. Qualification (check education relation)
        $hasQual = $this->education()->whereNotNull('qualification')->where('qualification', '!=', '')->exists();
        if (!$hasQual) {
            $missing[] = 'Qualification';
        }

        // 2. Blood Group
        if (empty(trim((string)($this->blood_group ?? '')))) {
            $missing[] = 'Blood Group';
        }

        // 3. Emergency Contact
        $hasEmergPhone = !empty(trim((string)($this->emergency_phone ?? '')));
        $hasEmergName = !empty(trim((string)($this->emergency_name ?? '')));
        if (!$hasEmergPhone && !$hasEmergName) {
            $missing[] = 'Emergency Contact';
        }

        // 4. Joining Date
        $jDate = is_string($this->joining_date) ? trim($this->joining_date) : $this->joining_date;
        if (empty($jDate) || $jDate === '0000-00-00' || $jDate === '1970-01-01') {
            $missing[] = 'Joining Date';
        }

        // 5. Aadhaar Number
        if (empty(trim((string)($this->aadhaar ?? '')))) {
            $missing[] = 'Aadhaar Number';
        }

        return $missing;
    }
}
