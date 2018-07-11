<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ContentTypeServiceProvider extends ServiceProvider {
	
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
		
		$this->app->singleton('ContentTypeService', function () {
			
			return new \App\Services\ContentTypeService();
		});
	}
}
