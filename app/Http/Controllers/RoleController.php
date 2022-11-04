<?php

namespace App\Http\Controllers;

use App\Models\Role;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class RoleController extends Controller
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

    // get one role
    public function getRole($id)
    {
        return Role::find($id);
    }

    // Create new role
    public function create(Request $request)
    {
        $userrr = $user_socialite = Socialite::driver('eduid')->stateless()->user();
        if ($request->user()->can('create', Role::class)) {
            $role = Role::create($request->all());
            return response()->json($role);
        } else {
            return response()->json([
                'message' => "Nemate ovlasti za kreiranje uloge"
            ]);
        }
    }
}
