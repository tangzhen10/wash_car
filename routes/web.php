<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
	
	if (env('APP_ENV') == 'local') {
		
//		return redirect('/admin');
	}
	
	return view('welcome');
});

Route::group(['namespace' => 'Web'], function () {
	
	# 后台首页
//	Route::get('/', ['uses' => 'ManagerController@index', 'as' => 'adminIndex']);
	
	# 个人中心
	Route::group(['prefix' => 'user'], function () {
		
		# 个人信息
		Route::match(['get', 'post'], 'info', ['uses' => 'UserController@info', 'as' => 'webUserInfo']);
	});
	
});
