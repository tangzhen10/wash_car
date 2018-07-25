<?php

/**
 * 后台路由
 */
Route::group(['namespace' => 'Admin'], function () {
	
	# 后台首页
	Route::get('/', ['uses' => 'ManagerController@index', 'as' => 'adminIndex']);
	
	# 车辆
	Route::group(['prefix' => 'car'], function () {
		
		# 品牌列表
		Route::get('brandList', ['uses' => 'CarController@brandList', 'as' => 'brandList']);
		# 增改品牌
		Route::match(['get', 'post'], 'brandForm/{id?}', ['uses' => 'CarController@brandForm', 'as' => 'brandForm']);
		# 修改状态
		Route::post('brandChangeStatus', ['uses' => 'CarController@brandChangeStatus', 'as' => 'brandChangeStatus']);
		# 车型列表
		Route::get('modelList/{brand_id?}', ['uses' => 'CarController@modelList', 'as' => 'modelList']);
		# 增改车型
		Route::match(['get', 'post'], 'modelForm/{id?}', ['uses' => 'CarController@modelForm', 'as' => 'modelForm']);
		
	});
	
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
		# 验证密码
		Route::post('checkPassword', ['uses' => 'ManagerController@checkPassword', 'as' => 'checkManagerPwd']);
		# 批量删除
		Route::post('batchDelete', ['uses' => 'ManagerController@batchDelete', 'as' => 'batchDeleteManager']);
		
	});
	
	# 角色
	Route::group(['prefix' => 'role'], function () {
		
		# 角色列表
		Route::get('list', ['uses' => 'RoleController@roleList', 'as' => 'roleList']);
		# 增改角色
		Route::match(['get', 'post'], 'form/{id?}', ['uses' => 'RoleController@form', 'as' => 'roleForm']);
		# 修改状态
		Route::post('changeStatus', ['uses' => 'RoleController@changeStatus', 'as' => 'roleChangeStatus']);
		# 查看拥有该角色的管理员
		Route::get('manager/{id}', ['uses' => 'RoleController@roleManager', 'as' => 'roleManager']);
		# 从角色中移除管理员
		Route::post('removeManager', ['uses' => 'RoleController@removeManager', 'as' => 'removeManager']);
		# 批量删除
		Route::post('batchDelete', ['uses' => 'RoleController@batchDelete', 'as' => 'batchDeleteRole']);
		
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
		# 修改用户信息
		Route::match(['get', 'post'], 'form/{id}', ['uses' => 'MemberController@form', 'as' => 'memberForm']);
		
	});
	
	# 文档类型
	Route::group(['prefix' => 'content_type'], function () {
		
		# 列表
		Route::get('list/{type?}', ['uses' => 'ContentTypeController@typeList', 'as' => 'contentTypeList']);
		# 增改
		Route::match(['get', 'post'], 'form/{id?}', ['uses' => 'ContentTypeController@form', 'as' => 'contentTypeForm']);
		# 修改状态
		Route::match(['get', 'post'], 'changeStatus', ['uses' => 'ContentTypeController@changeStatus', 'as' => 'contentTypeChangeStatus']);
		# 文档类型的表单html
		Route::get('formHtml/{id?}', ['uses' => 'ContentTypeController@formHtml', 'as' => 'contentTypeFormHtml']);
	});
	
	# 文章
	Route::group(['prefix' => 'article'], function () {
		
		# 列表
		Route::get('list', ['uses' => 'ArticleController@articleList', 'as' => 'articleList']);
		# 增改
		Route::match(['get', 'post'], 'form/{id?}', ['uses' => 'ArticleController@form', 'as' => 'articleForm']);
		# 修改状态
		Route::match(['get', 'post'], 'changeStatus', ['uses' => 'ArticleController@changeStatus', 'as' => 'articleChangeStatus']);
		# 上传文件
		Route::match(['get', 'post'], 'upload', ['uses' => 'ArticleController@uploadFile', 'as' => 'articleUploadFile']);
		# 批量删除
		Route::post('batchDelete', ['uses' => 'ArticleController@batchDelete', 'as' => 'batchDeleteArticle']);
		
	});
	
	# 产品
	Route::group(['prefix' => 'product'], function () {
		
		# 列表
		Route::get('list', ['uses' => 'ArticleController@productList', 'as' => 'productList']);
		# 分类
		Route::get('category/{id?}', ['uses' => 'ArticleController@productCategory', 'as' => 'productCategory']);
		# 显示更多
		Route::post('showMore', ['uses' => 'ArticleController@showMore', 'as' => 'showMoreArticle']);
		
	});
	
	# 设置
	Route::group(['prefix' => 'setting'], function () {
		
		# 前台
		Route::get('main', ['uses' => 'SettingController@main', 'as' => 'settingMain']);
		# 后台
		Route::get('admin', ['uses' => 'SettingController@admin', 'as' => 'settingAdmin']);
		# 保存设置
		Route::post('save/{id?}', ['uses' => 'SettingController@form', 'as' => 'saveSetting']);
		
	});
	
	
	
});
