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
		$products = \ArticleService::getArticleList(['content_type' => \SettingService::getValue('product_content_type')], false);
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
		
		# 个人信息
		$userInfo = $this->user->getUserInfo();
		if (!empty($userInfo['phone'])) { # 未登录不做屏蔽
			$userInfo['phone'] = substr($userInfo['phone'], 0, 3).'****'.substr($userInfo['phone'], -4);
		}
		
		# 余额
		$userInfo['balance'] = $this->user->getBalance();
		
		# 支付方式
		$paymentMethod = ['wechat'];
		if ($userInfo['balance'] > 0) {
			$paymentMethod[] = 'balance';
		}
		
		json_msg(compact('banners', 'product', 'contact', 'car', 'paymentMethod', 'total', 'totalOri', 'userInfo'));
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
		
		$page = \Request::input('page', '1');
		$list = \OrderService::getMyWashOrderList($page);
		
		json_msg(['list' => $list]);
	}
	
	/**
	 * 订单详情
	 * @author 李小同
	 * @date   2018-8-7 20:44:13
	 */
	public function washOrderDetail() {
		
		$orderId = \Request::input('order_id');
		$detail  = \OrderService::getWashOrderDetail($orderId);
		
		# 不可以看别人的订单
		if ($detail['user_id'] != $this->user->userId) {
			json_msg(trans('error.access_denied'), 40003);
		}
		
		$logs      = \OrderService::getOrderLogs($orderId);
		$washImage = \OrderService::getWashImages($orderId, true);
		foreach ($logs as &$log) {
			switch ($log['action']) {
				case 'serve_start':
					$log['images'] = [
						'title'  => trans('common.image_before_wash'),
						'images' => $washImage['before'],
					];
					break;
				case 'serve_finish':
					$log['images'] = [
						'title'  => trans('common.image_after_wash'),
						'images' => $washImage['after'],
					];
					break;
			}
			unset($log['operator'], $log['action'], $log['order_status']);
		}
		unset($log);
		
		$result = [
			'detail' => [
				'title' => trans('common.order_info'),
				'data'  => $detail,
			],
			'log'    => [
				'title' => trans('common.order_log'),
				'data'  => $logs,
			],
		];
		json_msg($result);
	}
	
	/**
	 * 用户订单取消&退款
	 * @author 李小同
	 * @date   2018-8-8 17:50:16
	 */
	public function changeStatus() {
		
		$post = request_all();
		$res  = \OrderService::userWashOrderChangeStatus($post);
		$this->render($res);
	}
	
}
