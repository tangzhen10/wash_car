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
		$price    = $product['detail']['price'];
		$priceOri = $product['detail']['price_ori'];
		unset($product['sub_name'], $product['detail']);
		
		# 联系人电话
		$contact = [
			'user'  => $this->user->userInfo['nickname'],
			'phone' => $this->user->userInfo['phone'],
		];
		
		# 默认车辆
		$car = \CarService::myLastWashCar();
		
		json_msg(compact('banners', 'product', 'contact', 'car', 'price', 'priceOri'));
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
		
		$this->render($orderId);
	}
}
