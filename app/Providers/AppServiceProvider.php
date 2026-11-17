<?php

namespace App\Providers;

use App\Services\UserRoleService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            View::share([
                'curr' => '₦',
                'currName' => 'NGN',
                'baseUrl' => url('').'/',
            ]);
        });
        Paginator::useBootstrap();
    }
}
