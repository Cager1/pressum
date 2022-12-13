<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class PermissionController extends ResourceController
{
    public static $modelName = 'Permission';
    public static $middlewareCustom = ['auth:sanctum'];

    // create Permission
    public function store(Request $request)
    {
        // validate
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);
        $permission = Permission::create($request->all());

        // check if any role has "all" permission
        $roles = Role::permissions('all')->get();
        $permissions = Permission::all();
        // give this permission to all roles that have "all" permission
        foreach ($roles as $role) {
            $role->permissions()->sync($permissions);
        }

        return response()->json($permission);
    }
}
