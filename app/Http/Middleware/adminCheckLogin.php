<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;

class adminCheckLogin {
	
	protected $except = [
		'managerLogin', # 登录
		'managerLogout', # 登出
	];
	
	/**
	 * Handle an incoming request.
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure                 $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {
		
		$name = Route::currentRouteName();
		if (in_array($name, $this->except)) return $next($request);
		
		$managerId = \ManagerService::checkLogin();
		if ($managerId > 0) {
			return $next($request);
		} else {
			return redirect()->route('managerLogin');
		}
	}
}
