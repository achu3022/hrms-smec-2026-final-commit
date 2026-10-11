<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveSegment extends Model
{
    protected $guarded = [];

    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class);
    }
}
