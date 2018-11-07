<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-6-29 16:43:08
 */
namespace App\Http\Controllers\Api;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * 工具类
 * Class ToolController
 * @package App\Http\Controllers\Main
 */
class ToolController extends BaseController {
	
	/**
	 * 发送短信验证码
	 * @author 李小同
	 * @date   2018-7-28 17:39:22
	 */
	public function sendSMSCode() {
		
		$res = \ToolService::sendSMSCode();
		if ($res['result']) {
			json_msg($res['errmsg'].$res['ext'], $res['result']);
		} else {
			json_msg($res['errmsg'].$res['ext']);
		}
	}
	
	/**
	 * 充值
	 * @author 李小同
	 * @date   2018-8-11 11:13:29
	 */
	public function reCharge() {
		
		$amount = \Request::input('amount');
		$res    = \ToolService::recharge($amount, $this->user->userId);
		$this->render($res);
	}
	
	/**
	 * 微信支付回调
	 * @author 李小同
	 * @date   2018-11-05 21:21:07
	 */
	public function wechatNotify() {
		
		$log = new Logger('pay');
		$log->pushHandler(new StreamHandler(config('project.PATH_TO_PAY_LOG')));
		
		$post = file_get_contents("php://input");
		$post = xml_to_array($post);
		$log->addInfo('wechat_pay', $post);
		
		/*
		 * 微信官方提醒：
		 * 商户系统对于支付结果通知的内容一定要做【签名验证】,
		 * 并校验返回的【订单金额是否与商户侧的订单金额】一致，
		 * 防止数据泄漏导致出现“假通知”，造成资金损失。
		 */
		$sign = \WechatService::getSign($post);
		if ($sign !== $post['sign']) {
			$log->addError('wrong sign', $_SERVER);
			json_msg(trans('error.illegal_action'), 40006);
		}
		
		$orderId   = $post['out_trade_no'];
		$orderInfo = \OrderService::getWashOrder($orderId);
		
		# 验证微信支付金额是否正确
		if (in_array($orderInfo['payment_method'], ['wechat,balance', 'balance,wechat'])) {
			# 组合支付
			$balance = \UserService::getBalance($orderInfo['user_id']);
			$needPay = $orderInfo['total'] * 100 - $balance * 100;
		} else {
			$needPay = $orderInfo['total'] * 100;
		}
		if ($needPay != $post['total_fee']) {
			$log->addError('wrong total_fee', $_SERVER);
			$log->addError('$needPay='.$needPay);
			json_msg(trans('error.insufficient_payment'), 40003);
		}
		
		if ($post['return_code'] == 'SUCCESS' && !empty($post['sign'])) {
			
			/**
			 * 首先判断，订单是否已经更新为ok，因为微信会总共发送8次回调确认
			 * 其次，订单已经为ok的，直接返回SUCCESS
			 * 最后，订单没有为ok的，更新状态为ok，返回SUCCESS
			 */
			$successMsg = '<xml>
								<return_code><![CDATA[SUCCESS]]></return_code>
								<return_msg><![CDATA[OK]]></return_msg>
							</xml>';
			# 订单状态在变为2之后可能还会有其他状态，故只能用 != 1来判断
			if ($orderInfo['status'] != 1 && $orderInfo['payment_status'] == '1') die($successMsg);
			
			# 支付记录
			$paymentData = [
				'order_id'       => $orderId,
				'payment_method' => 'wechat',
				'amount'         => $post['total_fee'] / 100, # 回调的total_fee是乘以100的
				'create_by'      => $orderInfo['user_id'],
			];
			\PaymentService::addPaymentLog($paymentData);
			
			\OrderService::realPayOrder($orderInfo);
			$log->addInfo('wechat paid success');
			echo $successMsg;
			
		} else {
			$log->addInfo('wechat paid failed');
		}
	}
}
