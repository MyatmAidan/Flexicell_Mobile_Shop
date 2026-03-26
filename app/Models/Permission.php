<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $guarded = ['id'];

    /**
     * Get all roles that have this permission.
     */
    public function roles()
    {
        return $this->hasMany(RolePermission::class);
    }
}
