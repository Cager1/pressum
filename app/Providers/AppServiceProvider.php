<?php

namespace App\Providers;

use App\Socialite\EduIDProvider;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Socialite::extend('eduid', function ($app) {
            $config = $app['config']['services.eduid'];
            return new EduIDProvider(
                $app['request'],
                $config['client_id'],
                $config['client_secret'],
                \URL::to($config['redirect'])
            );
        });
    }
}
