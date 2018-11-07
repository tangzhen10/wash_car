<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-08-28 22:40
 */

namespace App\Services;

class PaymentService {
	
	/**
	 * 添加余额流水
	 * @param array $data
	 * @author 李小同
	 * @date   2018-08-21 15:26:38
	 */
	public function addBalanceDetail(array $data) {
		
		$useBalanceData = [
			'user_id'   => $data['user_id'],
			'amount'    => $data['amount'],
			'type'      => $data['type'],
			'order_id'  => $data['order_id'],
			'comment'   => $data['comment'],
			'create_at' => time(),
			'create_ip' => getClientIp(true),
		];
		$detailId       = \DB::table('balance_detail')->insertGetId($useBalanceData);
		
		return $detailId;
	}
	
	/**
	 * 添加支付记录
	 * @param array $data
	 * @author 李小同
	 * @date   2018-08-21 15:23:04
	 */
	public function addPaymentLog(array $data) {
		
		$paymentData = [
			'order_id'       => $data['order_id'],
			'payment_method' => $data['payment_method'],
			'amount'         => $data['amount'],
			'operate_type'   => 'user',
			'creator'        => empty($data['create_by']) ? \OrderService::getFormatUser() : \OrderService::getFormatUser($data['create_by']),
			'create_by'      => empty($data['create_by']) ? \UserService::getUserId() : $data['create_by'],
			'create_at'      => time(),
		];
		$logId       = \DB::table('payment_log')->insertGetId($paymentData);
		
		return $logId;
	}
	
	/**
	 * 获取支付记录
	 * @param $orderId
	 * @author 李小同
	 * @date   2018-08-25 11:06:45
	 * @return array
	 */
	public function getPaymentLogs($orderId) {
		
		# amount大于0为支付，小于0为退款
		$fields = ['payment_method', 'amount', 'create_by'];
		$logs   = \DB::table('payment_log')
		             ->where('order_id', $orderId)
		             ->where('amount', '>=', 0)
		             ->get($fields)
		             ->toArray();
		return $logs;
	}
	
	/**
	 * 余额退款
	 * @param array $data
	 * @author 李小同
	 * @date   2018-08-25 11:25:37
	 * @return bool
	 */
	public function balanceRefund(array $data) {
		
		# 余额使用记录
		$useBalanceData = [
			'amount'   => floatval($data['amount']),
			'type'     => 'refund_order',
			'order_id' => $data['order_id'],
			'comment'  => '【订单退款】'.$data['order_id'],
			'user_id'  => $data['user_id'],
		];
		$this->addBalanceDetail($useBalanceData);
		
		return true;
	}
	
	/**
	 * 微信退款
	 * @param array $data
	 * @author 李小同
	 * @date   2018-08-25 11:28:46
	 * @return bool
	 */
	public function wechatRefund(array $data) {
		
		# todo lxt 微信退款
		
		return true;
	}
	
	/**
	 * 获取支付方式名称
	 * @param $method
	 * @author 李小同
	 * @date   2018-09-29 14:50:16
	 * @return string
	 */
	public function getMethodName($method) {
		
		switch ($method) {
			case 'balance':
				$name = '余额支付';
				break;
			case 'balance,wechat':
			case 'wechat,balance':
				$name = '余额支付 + 微信支付';
				break;
			case 'wechat':
				$name = '微信支付';
				break;
			case 'card':
				$name = '卡券自动支付';
				break;
			default:
				$name = '线下支付';
		}
		
		return $name;
	}
}