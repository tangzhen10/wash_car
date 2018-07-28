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

Route::group(['namespace' => 'Api'], function () {
	
	# 账户系统
	Route::group(['prefix' => 'user', 'middleware' => 'checkLogin'], function () {
		
		# 注册
		Route::post('register', ['uses' => 'UserController@register', 'as' => 'apiRegister']);
		# 登录
		Route::post('login', ['uses' => 'UserController@login', 'as' => 'apiLogin']);
		# 修改密码
		Route::post('changePassword', ['uses' => 'UserController@changePassword', 'as' => 'apiChangePassword']);
		
	});
	
	# 工具
	Route::group(['prefix' => 'tool'], function () {
		
		# 发送短信验证码
		Route::post('sendSMSCode', ['uses' => 'ToolController@sendSMSCode', 'as' => 'apiSendSMSCode']);
		
	});
	
	# 微信
	Route::group(['prefix' => 'wechat'], function () {
		
		# 获取access_token
		Route::get('baseAccessToken', ['uses' => 'WechatController@baseAccessToken', 'as' => 'apiWechatBaseAccessToken']);
		# 获取openid
		Route::get('openid', ['uses' => 'WechatController@getAccessTokenAndOpenId', 'as' => 'apiWechatOpenid']);
		# 获取用户信息
		Route::get('userinfo', ['uses' => 'WechatController@getUserInfo', 'as' => 'apiWechatUserInfo']);
	});
	
	# 车辆
	Route::group(['prefix' => 'car'], function () {
		
		# 我的车辆
		Route::post('myCar', ['uses' => 'CarController@myCar', 'as' => 'apiMyCar']);
		# 保存车辆
		Route::post('save', ['uses' => 'CarController@saveCar', 'as' => 'apiSaveCar']);
		# 删除车辆
		Route::post('delete', ['uses' => 'CarController@deleteCar', 'as' => 'apiCarDelete']);
		# 品牌
		Route::post('brand', ['uses' => 'CarController@brand', 'as' => 'apiCarBrand']);
		# 车型
		Route::post('model/{brand_id}', ['uses' => 'CarController@model', 'as' => 'apiCarModel']);
		# 车牌省份
		Route::post('province', ['uses' => 'CarController@province', 'as' => 'apiCarProvince']);
		# 颜色
		Route::post('color', ['uses' => 'CarController@color', 'as' => 'apiCarColor']);
	
	});
	
});

