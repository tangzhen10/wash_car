<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) { return $request->user(); });

Route::group(['namespace' => 'Main'], function () {
	
	# 账户系统
	Route::group(['prefix' => 'User'], function () {
		
		# 注册
		Route::post('register', ['uses' => 'UserController@register', 'as' => 'register']);
		# 登录
		Route::post('login', ['uses' => 'UserController@login', 'as' => 'login']);
		
	});
});

