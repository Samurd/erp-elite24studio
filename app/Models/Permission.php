<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{

    protected $fillable = ['name', 'action', 'guard_name', 'area_id'];
    public function area()
    {
        return $this->belongsTo(Area::class);
    }


    public function getFullNameAttribute()
    {
        if ($this->area) {
            if ($this->area->parent) {
                return $this->area->parent->slug . '.' . $this->area->slug . '.' . $this->name;
            }
            return $this->area->slug . '.' . $this->name;
        }
        return $this->name;
    }
}
