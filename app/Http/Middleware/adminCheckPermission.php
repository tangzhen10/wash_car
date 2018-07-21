<?php

namespace App\Http\Middleware;

use Closure;

class adminCheckPermission {
	
	protected $except = [
		'adminIndex', # 后台首页
		'managerLogin', # 登录
		'managerLogout', # 登出
		'checkManagerPwd', # 登出
	];
	
	/**
	 * Handle an incoming request.
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure                 $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {
		
		$currentURL   = \Request::getRequestUri();
		$currentRoute = substr($currentURL, strlen('/admin/'));
		$currentRoute = preg_replace('/\/\d+/', '', $currentRoute);
		$queryStrPos  = strpos($currentRoute, '?');
		if ($queryStrPos) $currentRoute = substr($currentRoute, 0, $queryStrPos);
		
		if (!in_array(\Route::currentRouteName(), $this->except)) {
			
			# 获取用户权限
			$roleIds     = \ManagerService::getRolesByManagerId();
			$permissions = \RoleService::getPermissionsByRoleId($roleIds);
			$list        = \DB::table('permission')
			                  ->where('status', '1')
			                  ->where('level', '>', '1')
			                  ->whereIn('id', $permissions)
			                  ->pluck('route')
			                  ->toArray();
			if (!in_array($currentRoute, $list)) {
				if (\Request::header('vfrom') == 'ajax') {
					json_msg(trans('error.access_denied'), 40006);
				} else {
					echo '<h2 style="color: red;">'.trans('error.access_denied'), '</h2>';
					echo '<script>setTimeout("window.history.back();",1000)</script>';
					die();
				}
				
			}
		};
		
		return $next($request);
	}
}
