<?php

/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-06-28 0028 14:23
 */

namespace App\Services;

use Illuminate\Support\Facades\Redis;

class UserService {
	
	/**
	 * 验证注册信息
	 * @author 李小同
	 * @date   2018-6-28 14:34:41
	 * @return array
	 */
	public function checkRegInfo() {
		
		$data         = $this->_validation();
		$account      = $data['account'];
		$password     = $data['password'];
		$identityType = $data['identityType'];
		
		# 检测是否已注册
		$where       = [
			'identity_type' => $identityType,
			'identity'      => $account,
		];
		$hasRegister = \DB::table('user_auth')->where($where)->count('id');
		if ($hasRegister) {
			$errorMsg = trans('validation.has_been_registered', ['attribute' => trans('common.'.$identityType)]);
			json_msg($errorMsg, 40002);
		}
		
		return compact('account', 'password', 'identityType');
	}
	
	/**
	 * 创建用户
	 * @param array $data
	 * @author 李小同
	 * @date   2018-6-28 15:13:22
	 * @return bool
	 */
	public function create(array $data) {
		
		$salt = create_salt();
		
		\DB::beginTransaction();
		try {
			
			$userInfo = ['nickname' => ''];
			switch ($data['identityType']) {
				case 'phone':
					$userInfo['phone'] = $data['account'];
					break;
				case 'email':
					$userInfo['email'] = $data['account'];
					break;
				case 'username' :
					$userInfo['nickname'] = $data['account'];
					break;
			}
			
			$userId = \DB::table('user')->insertGetId($userInfo);
			
			# 对密码进行加密
			$data['password'] = easy_encrypt($data['password'], $salt);
			
			$userAuth = [
				'user_id'       => $userId,
				'identity_type' => $data['identityType'],
				'identity'      => $data['account'],
				'credential'    => $data['password'],
				'salt'          => $salt,
				'create_at'     => time(),
				'create_ip'     => getClientIp(true),
			];
			$authId   = \DB::table('user_auth')->insertGetId($userAuth);
			
			if ($userId && $authId) \DB::commit();
			
			return true;
			
		} catch (\Exception $e) {
			
			\DB::rollback();
			return false;
		}
	}
	
	public function checkLoginInfo() {
		
		$data         = $this->_validation();
		$account      = $data['account'];
		$password     = $data['password'];
		$identityType = $data['identityType'];
		
		$where        = [
			'identity_type' => $identityType,
			'identity'      => $account,
			'status'        => '1',
		];
		$identityInfo = \DB::table('user_auth')->where($where)->first(['user_id', 'credential', 'salt']);
		if (easy_encrypt($password, $identityInfo['salt']) == $identityInfo['credential']) {
			
			# 登录成功
			$userInfo          = \DB::table('user')->where('user_id', $identityInfo['user_id'])->first();
			$userInfo['token'] = create_token();
			$cacheKey          = sprintf(config('cache.USER_INFO'), $userInfo['token']);
			redisSet($cacheKey, $userInfo);
			
			json_msg($userInfo);
			
		} else {
			
			# 用户名或密码错误
			json_msg(trans('error.error_account_or_pwd'), 50001);
			
			# 记录登录出错信息
			if (env('LOGIN_FAILED_LOG', false)) {
				
				# todo lxt 登录错误日志
			}
		}
	}
	
	/**
	 * 检测登录、注册信息的合法性
	 * @author 李小同
	 * @date   2018-6-28 16:41:55
	 * @return array
	 */
	private function _validation() {
		
		$account  = \Request::input('account', '');
		$password = \Request::input('password', '');
		
		# 检测数据合法性
		if (!trim($account)) json_msg(trans('validation.required', ['attribute' => trans('common.account')]), 40001);
		if (!$password) json_msg(trans('validation.required', ['attribute' => trans('common.password')]), 40001);
		if (strlen($password) < 6) {
			$errorMsg = trans('validation.min.string', ['attribute' => trans('common.password'), 'min' => 6]);
			json_msg($errorMsg, 40003);
		}
		$patten = [
			'phone' => '/^1[34578]{1}[\d]{9}$/',
			'email' => '/^[\w\.]+@([\w\.]+)+[\w]+$/',
		];
		
		# 检测渠道
		$identityType = 'username';
		if (preg_match($patten['phone'], $account)) {
			$identityType = 'phone';
		} elseif (preg_match($patten['email'], $account)) {
			$identityType = 'email';
		}
		
		return compact('account', 'password', 'identityType');
	}
	
}