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
		$total    = currencyFormat($product['detail']['price'] * 1);
		$totalOri = currencyFormat($product['detail']['price_ori'] * 1);
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
		
		$todayText    = trans('common.today');
		$tomorrowText = trans('common.tomorrow');
		$timeList     = [
			date('Y-m-d 00:00:00')                         => $todayText.' 00:00-01:00',
			date('Y-m-d 01:00:00')                         => $todayText.' 01:00-02:00',
			date('Y-m-d 21:00:00')                         => $todayText.' 21:00-22:00',
			date('Y-m-d 22:00:00')                         => $todayText.' 22:00-23:00',
			date('Y-m-d 23:00:00')                         => $todayText.' 23:00-24:00',
			date('Y-m-d', strtotime('+1 day')).' 00:00:00' => $tomorrowText.' 00:00-01:00',
			date('Y-m-d', strtotime('+1 day')).' 01:00:00' => $tomorrowText.' 01:00-02:00',
		];
		
		$now      = date('Y-m-d H:i:s');
		$am2clock = date('Y-m-d 02:00:00'); # 今天2点
		
		$list = [];
		foreach ($timeList as $key => $item) {
			
			if ($now < $am2clock && $am2clock < $key) continue;
			if ($now > $key) continue;
			
			$list[] = [
				'text'  => $item,
				'value' => $key,
			];
		}
		
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
		
		$list = \OrderService::getWashOrderList();
		
		json_msg(['list' => $list]);
	}
}
