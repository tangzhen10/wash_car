<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-06-27 23:01
 */

namespace App\Http\Controllers\Api;

/**
 * 用户接口类
 * Class UserController
 * @package App\Http\Controllers\Main
 */
class UserController extends BaseController {
	
	/**
	 * 用户注册
	 * @author 李小同
	 * @date   2018-6-28 15:33:53
	 */
	public function register() {
		
		# 验证
		$regData = $this->user->checkRegInfo();
		
		# 注册
		$res = $this->user->create($regData);
		if ($res) {
			json_msg(trans('common.register_success'));
		} else {
			json_msg(trans('common.action_failed'), 40004);
		}
	}
	
	/**
	 * 登录
	 * @author 李小同
	 * @date   2018-6-29 10:35:21
	 */
	public function login() {
		
		$loginInfo = $this->user->loginByPassword();
		
		json_msg($loginInfo);
	}
	
	/**
	 * 修改密码
	 * @author 李小同
	 * @date   2018-8-1 22:29:51
	 */
	public function changePassword() {
		
		$res = $this->user->updatePassword();
		$this->render($res);
	}
	
	/**
	 * 手机号登录（未注册则自动注册）
	 * @author 李小同
	 * @date   2018-8-2 11:10:42
	 */
	public function loginByPhone() {
		
		$phone            = trim(\Request::input('account'));
		$useType          = 'login_by_phone';
		$cacheKey         = \ToolService::getVerifyCodeCacheKey(compact('phone', 'useType'));
		$serverVerifyCode = redisGet($cacheKey);
		$clientVerifyCode = \Request::input('verify_code');
		
		# todo lxt 上正式后去掉 '||' 及其后面的内容 李小同 2018-8-2 14:18:21
		if ($serverVerifyCode == $clientVerifyCode || env('APP_ENV') == 'local') {
			
			redisDel($cacheKey);
			
			$userId = $this->user->checkExistIdentity('phone', $phone);
			
			# 未注册自动注册
			if (!$userId) {
				
				$regData  = [
					'identityType' => 'phone',
					'identity'     => $phone,
					'credential'   => '',
					'salt'         => '',
				];
				$userInfo = ['phone' => $phone]; # 手机号注册的，自动验证通过
				$userId   = $this->user->create($regData, $userInfo);
			}
			
			# 登录
			$loginInfo = $this->user->handleLogin($userId);
			json_msg($loginInfo);
			
		} else {
			json_msg(trans('validation.wrong', ['attr' => trans('common.verify_code')]), 50001);
		}
	}
	
	# region 业务
	/**
	 * 洗车联系人
	 * @author 李小同
	 * @date
	 */
	public function washContact() {
		
	}
	/**
	 * 获取openid
	 * @author 李小同
	 * @date   2018-8-7 15:27:52
	 */
	public function getOpenid() {
		
		$code = \Request::input('code');
		$res = \WechatService::getAccessTokenAndOpenId($code);
		json_msg($res);
	}
	# endregion
}