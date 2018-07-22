<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CarServiceProvider extends ServiceProvider
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
	    $this->app->singleton('CarService', function ($app) {
		
		    return new \App\Services\CarService();
	    });
    }
}
