<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class OrderServiceProvider extends ServiceProvider {
	
	/**
	 * Bootstrap the application services.
	 * @return void
	 */
	public function boot() {
		//
	}
	
	/**
	 * Register the application services.
	 * @return void
	 */
	public function register() {
		
		$this->app->singleton('OrderService', function () {
			
			return new \App\Services\OrderService();
		});
	}
}
