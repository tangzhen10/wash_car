<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class WechatServiceProvider extends ServiceProvider {
	
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
		
		$this->app->singleton('WechatService', function () {
			
			return new \App\Services\WechatService();
		});
	}
}
