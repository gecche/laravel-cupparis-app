<?php

namespace App\Providers;

use Gecche\Cupparis\App\Foorm\FoormManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Schema::defaultStringLength(191);
        //
        View::composer(['*'],function ($view) {
            $view->with('authRole',Auth::id() ? Auth::user()->mainrole : null);
            $view->with('layoutGradientColor','bg-gradient-info overlay-dark overlay-opacity-4');
        });

    }
}
