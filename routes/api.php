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

Route::group(['namespace' => 'Api', 'middleware' => 'checkLogin'], function () {
	
	# 账户系统
	Route::group(['prefix' => 'user', 'middleware' => 'checkLogin'], function () {
		
		# 手机号登录（未注册则自动注册）
		Route::post('loginByPhone', ['uses' => 'UserController@loginByPhone', 'as' => 'apiLoginByPhone']);
		# 注册
		Route::post('register', ['uses' => 'UserController@register', 'as' => 'apiRegister']);
		# 登录
		Route::post('loginOri', ['uses' => 'UserController@login', 'as' => 'apiLogin']);
		# 修改密码
		Route::post('changePassword', ['uses' => 'UserController@changePassword', 'as' => 'apiChangePassword']);
		# 获取openid
		Route::post('openid', ['uses' => 'UserController@getOpenid', 'as' => 'apiGetOpenid']);
	});
	
	# 工具
	Route::group(['prefix' => 'tool'], function () {
		
		# 发送短信验证码
		Route::post('sendSMSCode', ['uses' => 'ToolController@sendSMSCode', 'as' => 'apiSendSMSCode']);
		
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
		Route::post('model', ['uses' => 'CarController@model', 'as' => 'apiCarModel']);
		# 车牌省份
		Route::post('province', ['uses' => 'CarController@province', 'as' => 'apiCarProvince']);
		# 颜色
		Route::post('color', ['uses' => 'CarController@color', 'as' => 'apiCarColor']);
		
	});
	
	# 订单
	Route::group(['prefix' => 'order'], function () {
		
		# 首页
		Route::post('index', ['uses' => 'OrderController@appIndex', 'as' => 'apiAppIndex']);
		# 洗车服务项目列表
		Route::post('washList', ['uses' => 'ProductController@washList', 'as' => 'apiWashList']);
		# 洗车服务项目详情
		Route::post('washDetail', ['uses' => 'ProductController@washDetail', 'as' => 'apiWashDetail']);
		# 联系人
		Route::post('contact', ['uses' => 'UserController@washContact', 'as' => 'apiContactUser']);
		# 清洗时间
		Route::post('washTime', ['uses' => 'OrderController@washTime', 'as' => 'apiWashTime']);
		# 下单
		Route::post('placeOrder', ['uses' => 'OrderController@placeOrder', 'as' => 'apiPlaceOrder']);
		# 订单列表
		Route::post('list', ['uses' => 'OrderController@washOrderList', 'as' => 'apiWashOrderList']);
		# 订单详情
		Route::post('detail', ['uses' => 'OrderController@washOrderDetail', 'as' => 'apiWashOrderDetail']);
		# 修改订单状态
		Route::post('changeStatus', ['uses' => 'OrderController@changeStatus', 'as' => 'apiWashOrderChangeStatus']);
		
	});
	
});

