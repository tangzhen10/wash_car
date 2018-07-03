<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-06-27 23:01
 */

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;

/**
 * 用户类
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
		$regInfo = $this->user->checkRegInfo();
		
		# 注册
		$res = $this->user->create($regInfo);
		if ($res) {
			json_msg(trans('common.action_success'));
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
		
		$loginInfo = $this->user->handleLogin();
		
		json_msg($loginInfo);
	}
	
	public function changePassword() {
		
		$this->user->updatePassword();
	}
	
}