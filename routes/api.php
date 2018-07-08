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
	Route::group(['prefix' => 'user', 'middleware' => 'checkLogin'], function () {
		
		# 注册
		Route::post('register', ['uses' => 'UserController@register', 'as' => 'register']);
		# 登录
		Route::post('login', ['uses' => 'UserController@login', 'as' => 'login']);
		# 修改密码
		Route::post('changePassword', ['uses' => 'UserController@changePassword', 'as' => 'changePassword']);
		
	});
	
	# 工具
	Route::group(['prefix' => 'tool'], function () {
		
		# 发送短信验证码
		Route::post('sendSMSCode', ['uses' => 'ToolController@sendSMSCode', 'as' => 'sendSMSCode']);
		
	});
	
	# 微信
	Route::group(['prefix' => 'wechat'], function () {
		
		# 获取access_token
		Route::get('baseAccessToken', ['uses' => 'WechatController@baseAccessToken', 'as' => 'wechatBaseAccessToken']);
		
		# 获取openid
		Route::get('openid', ['uses' => 'WechatController@getAccessTokenAndOpenId', 'as' => 'wechatOpenid']);
		
		# 获取用户信息
		Route::get('userinfo', ['uses' => 'WechatController@getUserInfo', 'as' => 'wechatUserInfo']);
	});
	
});

