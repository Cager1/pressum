<?php

namespace App\Http\Controllers;

use App\Models\Role;

class RoleController
{

    // get all user roles
    public function userRoles()
    {
        return Role::all();
    }

    // get all roles with user count
    public function rolesWithUserCount()
    {
        return Role::withCount('users')->get();
    }
}
