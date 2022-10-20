<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    // return all users that have no assigned author
    public function index()
    {
        $users = User::doesntHave('author')->get();
        return $users;
    }

    // get users from last 6 months
    public function usersLastSixMonths()
    {
        return User::selectRaw('COUNT(*) as count, YEAR(created_at) year, MONTH(created_at) month')
            ->groupBy('year', 'month')
            ->get();
    }

    // get last 10 added users
    public function usersLastTen()
    {
        return User::orderBy('created_at', 'desc')->limit(10)->get()->load('role');
    }

    // search for users
    public function search(Request $request, $search)
    {
        return User::where('name', 'like', '%' . $search . '%')
            ->orWhere('email', 'like', '%' . $search . '%')
            ->get()->load('role');
    }

    // update user role
    public function updateRole(Request $request){
        $uid = $request->uid;
        // find user by uid
        $user = User::where('uid', $uid)->firstOrFail();
        $user->role_id = $request->role_id;
        $user->save();
        // return json user
        return response()->json([
            'user' => $user->load('role'),
            'message' => "Uloga uspješno promijenjena"
        ]);
    }

    // ban user
    public function ban(Request $request){
        $uid = $request->uid;
        // find user by uid
        $user = User::where('uid', $uid)->firstOrFail();
        if ($user->banned === 1) {
            $user->banned = false;
            $user->save();
            // return json user
            return response()->json([
                'user' => $user->load('role'),
                'message' => "Korisnik je odbanovan"
            ]);
        } else {
            $user->banned = true;
            $user->save();
            // return json user
            return response()->json([
                'user' => $user->load('role'),
                'message' => "Korisnik je banovan"
            ]);
        }
    }

    // get all banned users
    public function bannedUsers()
    {
        return User::where('banned', true)->get()->load('role');
    }
}
