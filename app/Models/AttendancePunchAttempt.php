<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendancePunchAttempt extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'time',
        'latitude',
        'longitude',
        'distance',
        'accuracy',
        'reason',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
