<?php

namespace App\Http\Middleware;

use Closure;

class CheckLogin {
	
	private $except = [
		'apiAppIndex',
		'apiRegister',
		'apiLogin',
		'apiLoginByPhone',
		'apiSendSMSCode',
		'apiGetOpenid',
		'apiPayWechatNotify',
	];
	
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure                 $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {
		
		$this->checkUserLogin();
		
		return $next($request);
	}
	
	/**
	 * 检测用户登录
	 * @author 李小同
	 * @date   2018-6-29 14:31:57
	 * @return bool
	 */
	public function checkUserLogin() {
		
		$routeName = \Route::currentRouteName();
		
		if (in_array($routeName, $this->except)) return true;
		
		$token = \Request::header('token', '');
		if ($token) {
			$cacheKey = sprintf(config('cache.USER_INFO'), $token);
			$userInfo = redisGet($cacheKey);
			if (is_array($userInfo) && !empty($userInfo['user_id'])) return true;
		}
		
		json_msg(trans('error.no_login'), 40005);
	}
}
