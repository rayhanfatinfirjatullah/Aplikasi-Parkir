<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('admin', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('petugas', function ($user) {
            return $user->role === 'petugas';
        });

        Gate::define('owner', function ($user) {
            return $user->role === 'owner';
        });
    }
}
