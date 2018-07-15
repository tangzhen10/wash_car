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
})->name('home');

Route::group(['namespace' => 'Web'], function () {
	
	# 个人中心
	Route::group(['prefix' => 'user'], function () {
		
		# 个人信息
		Route::match(['get', 'post'], 'info', ['uses' => 'UserController@info', 'as' => 'webUserInfo']);
	});
	
	# 理财
	Route::group(['prefix' => 'invest'], function () {
		
		# 理财列表
		Route::get('list', ['uses' => 'InvestController@investList', 'as' => 'webInvestList']);
		
		# 理财详情
		Route::get('detail/{id}', ['uses' => 'InvestController@detail', 'as' => 'webInvestDetail']);
	});
	
});
