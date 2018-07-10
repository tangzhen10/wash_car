<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ContentServiceProvider extends ServiceProvider {
	
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
		
		$this->app->singleton('ContentService', function () {
			
			return new \App\Services\ContentService();
		});
	}
}
