<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-6-29 16:43:08
 */
namespace App\Http\Controllers\Api;

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
		$msg = '本次验证码为：'.$res.'，验证码有效期为5分钟，请尽快输入。';
		json_msg($msg);
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
}
