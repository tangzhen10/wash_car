<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class userServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
	    $this->app->singleton('UserService', function ($app) {
		
		    return new \App\Services\UserService();
	    });
    }
}
