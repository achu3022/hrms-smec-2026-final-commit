<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $guarded = [];

    public function assignments()
    {
        return $this->hasMany(AssetAssignment::class);
    }
}
