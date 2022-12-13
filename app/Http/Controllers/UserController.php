<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    // return all users that have no assigned author
    public function index()
    {
        // if user can viewAny user
        if (Auth::user()->can('viewAny', User::class)) {
            return User::doesntHave('author')->where('role_id', '!=', 4)->get();
        } else {
            return response()->json([
                'message' => "Nemate ovlasti za pregled korisnika"
            ], 403);
        }
    }

    // get users from last 6 months
    public function usersLastSixMonths()
    {
        // if user can viewAny
        if (Auth::user()->can('viewAny', User::class)) {
            return User::selectRaw('COUNT(*) as count, YEAR(created_at) year, MONTH(created_at) month')
                ->groupBy('year', 'month')
                ->get();
        } else {
            return response()->json([
                'message' => "Nemate ovlasti za pregled korisnika"
            ], 403);
        }
    }

    // get last 10 added users
    public function usersLastTen()
    {
        // if user can viewAny
        if (Auth::user()->can('viewAny', User::class)) {
            return User::orderBy('created_at', 'desc')->take(10)->get()->load('role');
        } else {
            return response()->json([
                'message' => "Nemate ovlasti za pregled korisnika"
            ],403);
        }
    }

    // search for users
    public function search(Request $request, $search)
    {
        // if user can viewAny
        if (Auth::user()->can('viewAny', User::class)) {
            return User::where('name', 'like', '%' . $search . '%')->orWhere('email', 'like', '%' . $search . '%')->get()->load('role');
        } else {
            return response()->json([
                'message' => "Nemate ovlasti za pregled korisnika"
            ], 403);
        }
    }

    // update user role
    public function updateRole(Request $request){
        $user = Auth::user();
        $changeUser = User::where('uid', $request->uid)->firstOrFail();
        if ($user->can('update', $changeUser)) {
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
        else {
            return response()->json([
                'message' => "Nemate ovlasti za promjenu uloge korisnika",
                'permission' => Auth::user()->role->permissions
            ], 403);
        }
    }

    // ban user
    public function ban(Request $request){
        if (Auth::user()->can('update', User::class)) {
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
        else {
            return response()->json([
                'message' => "Nemate ovlasti za banovanje korisnika"
            ], 403);
        }
    }

}
