<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends ResourceModel
{
    use HasFactory;

    protected $fillable = [
        'name', 'super_access', 'dashboard_access', 'permissions',
    ];

    // has many users
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
