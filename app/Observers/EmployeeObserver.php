<?php

namespace App\Observers;

use App\Models\Employee;

class EmployeeObserver
{
    /**
     * Handle the Employee "created" event.
     */
    public function created(Employee $employee): void
    {
        // Auto-generate employee login account
        if ($employee->employee_id && $employee->dob) {
            $password = \Carbon\Carbon::parse($employee->dob)->format('dmY');
            \App\Models\EmployeeLogin::create([
                'employee_id' => $employee->id,
                'username' => $employee->employee_id,
                'password' => \Illuminate\Support\Facades\Hash::make($password),
                'is_first_login' => true,
            ]);
        }

    }

    /**
     * Handle the Employee "updated" event.
     */
    public function updated(Employee $employee): void
    {
        //
    }

    /**
     * Handle the Employee "deleting" event.
     */
    public function deleting(Employee $employee): void
    {
        // Delete physical files associated with the employee
        if ($employee->photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($employee->photo);
        }
        if ($employee->aadhaar_file) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($employee->aadhaar_file);
        }

        foreach ($employee->education as $edu) {
            if ($edu->certificate) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($edu->certificate);
            }
        }

        foreach ($employee->experience as $exp) {
            if ($exp->certificate) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($exp->certificate);
            }
        }

        foreach ($employee->certificates as $cert) {
            if ($cert->file) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($cert->file);
            }
        }

        // Release any assets assigned to this employee
        $assetAssignments = \App\Models\AssetAssignment::where('employee_id', $employee->id)->get();
        foreach ($assetAssignments as $assignment) {
            if ($assignment->asset) {
                $assignment->asset->update(['status' => 'Available']);
            }
            $assignment->delete();
        }

        // Delete all related database records explicitly
        \App\Models\EmployeeLogin::where('employee_id', $employee->id)->delete();
        \App\Models\LeaveRequest::where('employee_id', $employee->id)->delete();
        \App\Models\LeaveBalance::where('employee_id', $employee->id)->delete();
        \App\Models\Attendance::where('employee_id', $employee->id)->delete();
        \App\Models\DailyReport::where('employee_id', $employee->id)->delete();
        \App\Models\EarlyGoingRequest::where('employee_id', $employee->id)->delete();
        $employee->education()->delete();
        $employee->experience()->delete();
        $employee->certificates()->delete();
    }

    /**
     * Handle the Employee "deleted" event.
     */
    public function deleted(Employee $employee): void
    {
        //
    }

    /**
     * Handle the Employee "restored" event.
     */
    public function restored(Employee $employee): void
    {
        //
    }

    /**
     * Handle the Employee "force deleted" event.
     */
    public function forceDeleted(Employee $employee): void
    {
        //
    }
}
