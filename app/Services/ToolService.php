<?php

/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-06-28 0028 14:23
 */

namespace App\Services;

class ToolService {
	
	private $_validSMSCodeUseTypes = [
		'register', # 注册
		'bind_phone', # 绑定手机号码
	];
	
	public function sendSMSCode() {
		
		$this->_validation();
		
		# todo lxt 发送验证码 & 根据使用用途，在redis里生成有效期的验证码
	}
	
	/**
	 * 验证手机号是否可用
	 * @author 李小同
	 * @date   2018-6-29 18:13:37
	 * @return bool
	 */
	private function _validation() {
		
		$phone   = trim(\Request::input('phone'));
		$useType = \Request::input('use_type'); # 短信用途
		
		# 验证手机号码格式
		if (!preg_match(config('project.PATTERN.PHONE'), $phone)) {
			json_msg(trans('validation.invalid', ['attr' => trans('common.phone')]), 40003);
		}
		
		# 验证是否是合法的使用用途
		if (!in_array($useType, $this->_validSMSCodeUseTypes)) json_msg(trans('error.illegal_param'), 40003);
		
		switch ($useType) {
			case 'register' :
				# 验证手机号是否被注册
				\UserService::checkExistIdentity('phone', $phone);
				break;
			case 'bind_phone':
				# todo lxt
				break;
		}
		
		return true;
	}
}