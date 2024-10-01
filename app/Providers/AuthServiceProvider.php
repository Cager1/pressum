<?php

namespace App\Providers;

use App\Models\Author;
use App\Models\Book;
use App\Models\ResourceFile;
use App\Policies\AuthorPolicy;
use App\Policies\FilePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        'App\Models\ResourceFile' => 'App\Policies\FilePolicy',
        'App\Models\Book' => 'App\Policies\BookPolicy',
        ResourceFile::class => FilePolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
