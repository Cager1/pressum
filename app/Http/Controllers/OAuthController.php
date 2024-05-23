<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Str;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Resources\User\UserResource;
use Symfony\Component\HttpFoundation\RedirectResponse;

class OAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['redirectToProvider', 'handleProviderCallback']);
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return RedirectResponse
     */
    public function redirectToProvider(Request $request): RedirectResponse
    {
        error_log("Redirecting to provider");
        Session::put('url.intended', $request->query('redirect_to'));

        return Socialite::driver('eduid')->redirect();
    }

    public function handleProviderCallback()
    {
        $user_socialite = Socialite::driver('eduid')->user();

        $laravelUser = User::withoutEvents(function () use ($user_socialite) {
            return DB::transaction(function () use ($user_socialite) {
                // check if user allready exists
                $laravelUser = User::where('uid', $user_socialite->user['data']['uid'])
                    ->whereBranch($user_socialite->user['data']['branch'])
                    ->first();
                // find Korisnik role
                $role = Role::where('name', 'Korisnik')->first();
                // role id is 1 if $user_socialite->user['data']['roles'] contains 'pauk_admin'
                if (in_array('pauk_admin', $user_socialite->user['data']['roles'])) {
                    $role = Role::where('name', 'Super Admin')->first();
                }
                if (!$laravelUser)
                    $laravelUser = User::create([
                        'uid' => $user_socialite->user['data']['uid'],
                        'branch' => $user_socialite->user['data']['branch'],
                        'first_name' => $user_socialite->user['data']['first_name'],
                        'last_name' => $user_socialite->user['data']['last_name'],
                        'email' => $user_socialite->user['data']['email'],
                        'name' => $user_socialite->user['data']['first_name'] . ' ' . $user_socialite->user['data']['last_name'],
                        'role_id' => $role->id,
                        'password' => "",
                    ]);
                else
                    $laravelUser->update([
                        'name' => $user_socialite->user['data']['first_name'] . ' ' . $user_socialite->user['data']['last_name'],
                        'first_name' => $user_socialite->user['data']['first_name'],
                        'last_name' => $user_socialite->user['data']['last_name'],
                        'email' => $user_socialite->user['data']['email'],
                        'role_id' => $role->id,
                    ]);
                return $laravelUser;
            });
        });

        Auth::login($laravelUser);

        error_log("User logged in: " . $laravelUser->name);
        Log::info('User logged: ' . $laravelUser->uid);
        $intended_url = Session::get('url.intended', null);

        if ($intended_url !== null) {
            return redirect($intended_url);
        } else {
            return redirect()->away(env('APP_FRONTEND_URL'));
        }

    }

    public function getUser(Request $request)
    {
        return auth()->user()->load('role', 'author.books');
    }

    public function logout(Request $request)
    {
        // get cookie from request
        $cookie = $request->cookie('pressum_session');
        error_log($cookie);
        Auth::logout();
        Session::save();

        $logoutUrl = 'https://api.eduid.sum.ba/sso/logout';

//        $intended_url = Session::get('url.intended', $request->redirect_to);
        $intended_url =  $request->redirect_to;

        if ($intended_url)
            $logoutUrl .= '?redirect_to=' . $intended_url;

        return redirect($logoutUrl);
    }
}
