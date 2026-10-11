<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'type',
        'target_type',
        'department_id',
        'is_active',
        'created_by',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'announcement_employee');
    }
}
