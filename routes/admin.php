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
		# 登出
		Route::match(['get', 'post'], 'logout', ['uses' => 'ManagerController@logout', 'as' => 'managerLogout']);
		# 修改密码
		Route::post('changePassword', ['uses' => 'ManagerController@changePassword', 'as' => 'managerChangePassword']);
		# 管理员列表
		Route::get('list', ['uses' => 'ManagerController@managerList', 'as' => 'managerList']);
		# 修改状态
		Route::post('changeStatus', ['uses' => 'ManagerController@changeStatus', 'as' => 'managerChangeStatus']);
		
	});
	
	# 角色
	Route::group(['prefix' => 'role'], function () {
		
		# 新增角色
		Route::match(['get', 'post'], 'add', ['uses' => 'RoleController@add', 'as' => 'addRole']);
		# 修改状态
		Route::post('changeStatus', ['uses' => 'RoleController@changeStatus', 'as' => 'roleChangeStatus']);
		
	});
	
	# 权限
	Route::group(['prefix' => 'permission'], function () {
		
		# 权限列表
		Route::get('list', ['uses' => 'PermissionController@permissionList', 'as' => 'permissionList']);
		# 增修权限
		Route::match(['get', 'post'], 'form/{id?}', ['uses' => 'PermissionController@form', 'as' => 'permissionForm']);
		# 修改状态
		Route::post('changeStatus', ['uses' => 'PermissionController@changeStatus', 'as' => 'permissionChangeStatus']);
		
	});
	
});
