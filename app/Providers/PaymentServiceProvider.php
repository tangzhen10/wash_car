<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider {
	
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
		
		$this->app->singleton('PaymentService', function () {
			
			return new \App\Services\PaymentService();
		});
	}
}
