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
		if (!empty($products[0])) {
			
			$product  = $products[0];
			$total    = $product['detail']['price'] * 1;
			$totalOri = $product['detail']['price_ori'] * 1;
			unset($product['sub_name'], $product['detail']);
		} else {
			$product = null;
		}
		
		# 默认车辆
		$car = \CarService::getMyLastWashCar();
		
		# 联系人及电话
		$contact = \OrderService::getContact();
		
		# 默认清洗时间
		$washTimeList = \OrderService::getWashTimeList();
		
		# 个人信息
		$userInfo = $this->user->getUserInfo();
		if (!empty($userInfo['phone'])) { # 未登录不做屏蔽
			$userInfo['phone'] = substr($userInfo['phone'], 0, 3).'****'.substr($userInfo['phone'], -4);
		}
		
		# 余额
		$userInfo['balance'] = $this->user->getBalance();
		
		# 支付方式
		$paymentMethod = ['wechat'];
		if ($userInfo['balance'] > 0) $paymentMethod[] = 'balance';
		
		json_msg(compact('banners', 'product', 'contact', 'car', 'washTimeList', 'paymentMethod', 'total', 'totalOri', 'userInfo'));
	}
	
	/**
	 * 下单
	 * @author 李小同
	 * @date   2018-8-1 22:46:09
	 */
	public function placeOrder() {
		
		$orderData = \OrderService::createOrder();
		
		if (!empty($orderData['order_id'])) {
			
			json_msg($orderData);
			
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
		
		$page      = \Request::input('page', '1');
		$list      = \OrderService::getMyWashOrderList($page);
		$totalPage = \OrderService::getMyWashOrderTotalPage();
		
		json_msg(compact('list', 'totalPage'));
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
		
		# 清理一些前端不需要的信息
		unset($detail['username'], $detail['phone'], $detail['order_status_msg']);
		
		$detail['balance'] = $this->user->getBalance();
		
		# 订单日志
		$logs = \OrderService::getOrderLogs($orderId);
		
		# 清理前后图片处理
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
	 * 用户修改订单状态
	 * 取消、退款、申请退款
	 * @author 李小同
	 * @date   2018-8-8 17:50:16
	 */
	public function changeStatus() {
		
		$post = request_all();
		$res  = \OrderService::userWashOrderChangeStatus($post);
		$this->render($res);
	}
	
	public function payOrder() {
		
		$post = request_all();
		$res  = \OrderService::payOrder($post);
		$this->render($res);
	}
	
}
