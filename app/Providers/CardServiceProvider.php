<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CardServiceProvider extends ServiceProvider {
	
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
		
		$this->app->singleton('CardService', function () {
			
			return new \App\Services\CardService();
		});
	}
}
