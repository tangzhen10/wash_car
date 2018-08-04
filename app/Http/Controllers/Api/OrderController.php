<?php

namespace App\Http\Controllers\Api;

/**
 * 定制业务 - 订单系统
 * Class OrderController
 * @package App\Http\Controllers\Api
 */
class OrderController extends BaseController {
	
	/**
	 * 首页
	 * @author 李小同
	 * @date   2018-8-2 18:24:16
	 */
	public function appIndex() {
		
		# banner
		$banners = \ArticleService::getArticleList(['content_type' => config('project.CONTENT_TYPE.WASH_INDEX_BANNER')], false);
		
		# 默认服务项目
		$products = \ArticleService::getArticleList(['content_type' => config('project.CONTENT_TYPE.WASH_PRODUCT')], false);
		$product  = $products[0];
		$total    = $product['detail']['price'] * 1;
		$totalOri = $product['detail']['price_ori'] * 1;
		unset($product['sub_name'], $product['detail']);
		
		# 联系人电话
		$contact = [
			'user'  => $this->user->getUserInfo('nickname'),
			'phone' => $this->user->getUserInfo('phone'),
		];
		
		# 默认车辆
		$car = \CarService::getMyLastWashCar();
		
		json_msg(compact('banners', 'product', 'contact', 'car', 'total', 'totalOri'));
	}
	
	/**
	 * 清洗时间
	 * @author 李小同
	 * @date   2018-7-28 21:21:52
	 * @return array
	 */
	public function washTime() {
		
		$list = \OrderService::getWashTimeList();
		
		json_msg(['list' => $list]);
	}
	
	/**
	 * 下单
	 * @author 李小同
	 * @date   2018-8-1 22:46:09
	 */
	public function placeOrder() {
		
		$orderId = \OrderService::createOrder();
		
		if ($orderId) {
			$result = [
				'order_id'    => $orderId,
				'success_msg' => trans('common.place_order_success'),
			];
			json_msg($result);
		} else {
			json_msg(trans('common.place_order_failed'), 40004);
		}
	}
	
	/**
	 * 洗车订单列表
	 * @author 李小同
	 * @date   2018-8-2 22:46:33
	 */
	public function washOrderList() {
		
		$list = \OrderService::getMyWashOrderList();
		
		# todo lxt 无订单
		
		json_msg(['list' => $list]);
	}
}
