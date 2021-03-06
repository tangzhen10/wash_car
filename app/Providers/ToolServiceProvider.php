<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ToolServiceProvider extends ServiceProvider {
	
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
		
		$this->app->singleton('ToolService', function ($app) {
			
			return new \App\Services\ToolService();
		});
	}
}
