<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckIfUserIsAuthor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user_uid) {
            $user = User::where('uid', $request->user_uid)->first();
            if ($user->role->name !== 'Super Admin' && $user->role->name !== 'Admin' && $user->role->name !== 'Autor') {
                response()->json([ 'message' => 'Korisnik nije autor.' ], 422);
            }
            return $next($request);
        }
        return $next($request);
    }
}
