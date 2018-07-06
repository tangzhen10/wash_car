<?php

/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-06-28 0028 14:23
 */

namespace App\Services;

class UserService {
	
	public $userId = 0;
	public $nickname = 0;
	
	private $_passwordIdentityTypes = ['username', 'email', 'phone']; # 需要密码的登录渠道
	
	/**
	 * 初始化
	 * @author 李小同
	 * @date   2018-6-29 11:58:09
	 */
	public function __construct() {
		
		$token = \Request::header('token', '');
		if ($token) {
			$cacheKey = sprintf(config('cache.USER_INFO'), $token);
			$userInfo = redisGet($cacheKey);
			if (is_array($userInfo) && count($userInfo)) {
				
				# 登录状态续签
				redisSet($cacheKey, $userInfo);
				
				$this->userId   = $userInfo['user_id'];
				$this->nickname = $userInfo['nickname'];
			}
		}
	}
	
	/**
	 * 是否登录
	 * @author 李小同
	 * @date   2018-6-29 11:57:46
	 * @return bool
	 */
	public function isLogin() {
		
		return $this->userId ? true : false;
	}
	
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
		$this->checkExistIdentity($identityType, $account);
		
		return compact('account', 'password', 'identityType');
	}
	
	/**
	 * 检测是否存在指定的登录凭证
	 * @param string $identityType
	 * @param string $identity
	 * @author 李小同
	 * @date   2018-6-29 18:12:36
	 * @return bool
	 */
	public function checkExistIdentity($identityType, $identity) {
		
		$where       = [
			'identity_type' => $identityType,
			'identity'      => $identity,
		];
		$hasRegister = \DB::table('user_auth')->where($where)->count('id');
		if ($hasRegister) {
			$errorMsg = trans('validation.has_been_registered', ['attr' => trans('common.'.$identityType)]);
			json_msg($errorMsg, 40002);
		}
		
		return true;
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
			
			$now      = time();
			$userInfo = ['create_at' => $now];
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
				'create_at'     => $now,
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
	
	/**
	 * 处理登录操作
	 * @author 李小同
	 * @date   2018-6-29 10:31:51
	 */
	public function handleLogin() {
		
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
		
		# 登录成功
		if (easy_encrypt($password, $identityInfo['salt']) == $identityInfo['credential']) {
			
			# 保存最近登录信息
			$lastLoginInfo = [
				'last_login_at' => time(),
				'last_login_ip' => getClientIp(true),
			];
			$where         = ['user_id' => $identityInfo['user_id']];
			\DB::table('user')->where($where)->update($lastLoginInfo);
			
			$userInfo          = $this->getUserInfo($identityInfo['user_id']);
			$userInfo['token'] = create_token();
			
			# 服务器端保存登录信息
			$cacheKey = sprintf(config('cache.USER_INFO'), $userInfo['token']);
			redisSet($cacheKey, $userInfo);
			
			return $userInfo;
			
		} else {
			
			# 用户名或密码错误
			json_msg(trans('error.wrong_account_or_pwd'), 50001);
			
			# 记录登录出错信息
			if (env('LOGIN_FAILED_LOG', false)) {
				
				# todo lxt 登录错误日志
			}
		}
	}
	
	/**
	 * 获取用户信息
	 * @param $id int 用户id
	 * @author 李小同
	 * @date   2018-7-6 20:34:07
	 * @return array
	 */
	public function getUserInfo($id) {
		
		$userInfo = \DB::table('user')->where('user_id', $id)->first();
		
		if (!empty($userInfo)) {
			
			# 生日在1970-01-01的人，birthday字段为0
			$userInfo['birthday'] = $userInfo['birthday'] != -1 ? date('Y-m-d', $userInfo['birthday']) : '';
			
			$userInfo['create_at']     = date('Y-m-d H:i:s', $userInfo['create_at']);
			$userInfo['last_login_at'] = $userInfo['last_login_at'] > 0 ? date('Y-m-d H:i:s', $userInfo['last_login_at']) : '';
			$userInfo['last_login_ip'] = long2ip($userInfo['last_login_ip']);
		}
		
		return $userInfo;
	}
	
	/**
	 * 修改密码（登录状态下）
	 * @author 李小同
	 * @date   2018-6-29 16:01:27
	 * @return bool
	 */
	public function updatePassword() {
		
		$oldPwd       = \Request::input('password_old');
		$newPwd       = \Request::input('password_new');
		$newPwdRepeat = \Request::input('password_new_repeat');
		
		$pwdQuery     = \DB::table('user_auth')
		                   ->where(['user_id' => $this->userId])
		                   ->whereIn('identity_type', $this->_passwordIdentityTypes);
		$updateQuery  = clone $pwdQuery;
		$identityInfo = $pwdQuery->first(['credential', 'salt']);
		if (count($identityInfo)) {
			
			# 旧密码正确
			if (easy_encrypt($oldPwd, $identityInfo['salt']) == $identityInfo['credential']) {
				if ($newPwd == $newPwdRepeat) {
					if (!isset($newPwd{6})) { # 检测字符串长度，使用isset，效率比strlen高
						$newHashPwd = easy_encrypt($newPwd, $identityInfo['salt']);
						$updateQuery->update(['credential' => $newHashPwd]);
						
						return true;
					} else {
						json_msg(trans('error.wrong_repeat_password'), 40003);
					}
				} else {
					$errorMsg = trans('validation.min.string', ['attr' => trans('common.password'), 'min' => 6]);
					json_msg($errorMsg, 40003);
				}
			} else {
				json_msg(trans('error.wrong_old_pwd'), 40003);
			}
		}
	}
	
	/**
	 * 根据指定的登录渠道，检测该登录信息是否存在
	 * @param string $type     登录渠道
	 * @param string $identity 登录凭证
	 * @author 李小同
	 * @date   2018-6-29 18:04:49
	 * @return bool
	 */
	public function checkAuthByIdentityInfo($type, $identity) {
		
		$where = ['identity_type' => $type, 'identity' => $identity];
		$res   = \DB::table('user_auth')->where($where)->count('id');
		
		return $res > 0 ? true : false;
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
		if (!trim($account)) json_msg(trans('validation.required', ['attr' => trans('common.account')]), 40001);
		if (!$password) json_msg(trans('validation.required', ['attr' => trans('common.password')]), 40001);
		if (strlen($password) < 6) {
			$errorMsg = trans('validation.min.string', ['attr' => trans('common.password'), 'min' => 6]);
			json_msg($errorMsg, 40003);
		}
		
		# 检测渠道
		$identityType = 'username';
		if (preg_match(config('project.PATTERN.PHONE'), $account)) {
			$identityType = 'phone';
		} elseif (preg_match(config('project.PATTERN.EMAIL'), $account)) {
			$identityType = 'email';
		}
		
		return compact('account', 'password', 'identityType');
	}
	
}