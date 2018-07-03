<?php

/**
 * 后台路由
 */
Route::group(['namespace' => 'Admin', 'middleware' => 'adminCheckLogin'], function () {
	
	# 后台首页
	Route::get('/', ['uses' => 'ManagerController@index', 'as' => 'adminIndex']);
	
	# 管理员
	Route::group(['prefix' => 'manager'], function () {
		
		# 登录
		Route::match(['get', 'post'], 'login', ['uses' => 'ManagerController@login', 'as' => 'managerLogin']);
		# 登录
		Route::match(['get', 'post'], 'logout', ['uses' => 'ManagerController@logout', 'as' => 'managerLogout']);
		# 修改密码
		Route::post('changePassword', ['uses' => 'ManagerController@changePassword', 'as' => 'managerChangePassword']);
		
	});
	
});
