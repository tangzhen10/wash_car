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
		# 增改管理员
		Route::match(['get', 'post'], 'form/{id?}', ['uses' => 'ManagerController@form', 'as' => 'managerForm']);
		# 修改状态
		Route::post('changeStatus', ['uses' => 'ManagerController@changeStatus', 'as' => 'managerChangeStatus']);
		
	});
	
	# 角色
	Route::group(['prefix' => 'role'], function () {
		
		# 角色列表
		Route::get('list', ['uses' => 'RoleController@roleList', 'as' => 'roleList']);
		# 增改角色
		Route::match(['get', 'post'], 'form/{id?}', ['uses' => 'RoleController@form', 'as' => 'roleForm']);
		# 修改状态
		Route::post('changeStatus', ['uses' => 'RoleController@changeStatus', 'as' => 'roleChangeStatus']);
		
	});
	
	# 权限
	Route::group(['prefix' => 'permission'], function () {
		
		# 权限列表
		Route::get('list', ['uses' => 'PermissionController@permissionList', 'as' => 'permissionList']);
		# 增改权限
		Route::match(['get', 'post'], 'form/{id?}', ['uses' => 'PermissionController@form', 'as' => 'permissionForm']);
		# 修改状态
		Route::post('changeStatus', ['uses' => 'PermissionController@changeStatus', 'as' => 'permissionChangeStatus']);
		
	});
	
	# 用户（会员）
	Route::group(['prefix' => 'member'], function () {
		
		# 会员列表
		Route::get('list', ['uses' => 'MemberController@memberList', 'as' => 'memberList']);
		# 修改状态
		Route::post('changeStatus', ['uses' => 'MemberController@changeStatus', 'as' => 'memberChangeStatus']);
		
	});
});
