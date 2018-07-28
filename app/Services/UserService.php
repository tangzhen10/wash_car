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
		
		$token = $this->getToken();
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
	 * 获取客户端会话token
	 * @author 李小同
	 * @date   2018-7-28 11:21:30
	 * @return string
	 */
	public function getToken() {
		
		return \Request::header('token', '');
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
		$identityType = $data['identityType'];
		$identity     = $data['account'];
		$password     = $data['password'];
		$verifyCode   = \Request::input('verify_code');
		if ($identityType == 'phone') {
			$phoneInfo = [
				'phone'   => $identity,
				'useType' => 'register',
			];
			$cacheKey  = \ToolService::getVerifyCodeCacheKey($phoneInfo);
			if ($verifyCode != redisGet($cacheKey)) {
				json_msg(trans('error.wrong_verify_code'), 40001);
			} else {
				redisDel($cacheKey); # 验证通过，清除验证码
			}
		}
		
		# 检查渠道是否允许注册
		if (!in_array($identityType, config('project.ALLOW_IDENTITY_TYPE'))) {
			$typeTextArr = [];
			foreach (config('project.ALLOW_IDENTITY_TYPE') as $type) $typeTextArr[] = trans('common.'.$type);
			json_msg(trans('error.support_register_identity_type', ['type' => implode(',', $typeTextArr)]), 40003);
		}
		
		# 检测是否已注册
		$hasRegister = $this->checkExistIdentity($identityType, $identity);
		if ($hasRegister) {
			$errorMsg = trans('validation.has_been_registered', ['attr' => trans('common.'.$identityType)]);
			json_msg($errorMsg, 40002);
		}
		
		# 对密码进行加密
		$salt       = create_salt();
		$credential = easy_encrypt($password, $salt);
		
		return compact('identityType', 'identity', 'credential', 'salt');
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
		
		$where = [
			'identity_type' => $identityType,
			'identity'      => $identity,
		];
		$row   = \DB::table('user_auth')->where($where)->pluck('user_id')->toArray();
		
		return count($row) ? $row[0] : false;
	}
	
	/**
	 * 创建用户
	 * @param array $authData
	 * @param array $userInfo
	 * @author 李小同
	 * @date   2018-6-28 15:13:22
	 * @return bool
	 */
	public function create(array $authData, $userInfo = []) {
		
		\DB::beginTransaction();
		try {
			
			$now = time();
			
			# 先保存用户信息，生成user_id
			$userInfo['create_at'] = $now;
			$userId                = \DB::table('user')->insertGetId($userInfo);
			
			# 保存登录凭证
			$userAuth = [
				'user_id'       => $userId,
				'identity_type' => $authData['identityType'],
				'identity'      => $authData['identity'],
				'credential'    => isset($authData['credential']) ? $authData['credential'] : '',
				'salt'          => isset($authData['salt']) ? $authData['salt'] : '',
				'create_at'     => $now,
				'create_ip'     => getClientIp(true),
			];
			$authId   = \DB::table('user_auth')->insertGetId($userAuth);
			
			if ($userId && $authId) \DB::commit();
			
			return $userId;
			
		} catch (\Exception $e) {
			
			\DB::rollback();
			return false;
		}
	}
	
	/**
	 * 密码登录
	 * @author 李小同
	 * @date   2018-6-29 10:31:51
	 */
	public function loginByPassword() {
		
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
			
			$userInfo = $this->handleLogin($identityInfo['user_id']);
			
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
	 * 处理登录
	 * 保存登录信息，获取token
	 * @param int   $userId    用户id
	 * @param array $extraData 额外保存的一些数据
	 * @author 李小同
	 * @date   2018-7-8 22:15:07
	 * @return array
	 */
	public function handleLogin($userId, $extraData = []) {
		
		# 保存最近登录信息
		$lastLoginInfo = [
			'last_login_at' => time(),
			'last_login_ip' => getClientIp(true),
		];
		$where         = ['user_id' => $userId];
		\DB::table('user')->where($where)->update($lastLoginInfo);
		
		$userInfo          = $this->getUserInfo($userId);
		$userInfo['token'] = create_token();
		$userInfo += $extraData;
		
		# 服务器端保存登录信息
		$cacheKey = sprintf(config('cache.USER_INFO'), $userInfo['token']);
		redisSet($cacheKey, $userInfo);
		
		return $userInfo;
	}
	
	/**
	 * 获取用户id
	 * @author 李小同
	 * @date   2018-7-28 10:56:17
	 * @return int|mixed
	 */
	public function getUserId() {
		
		return $this->userId;
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
			
			$userInfo['gender_text']   = trans('common.gender_'.$userInfo['gender']);
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
	
	public function handelOAuthLogin($authData, $userData) {
		
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
		
		# 判断渠道
		$identityType = 'username';
		if (preg_match(config('project.PATTERN.PHONE'), $account)) {
			$identityType = 'phone';
		} elseif (preg_match(config('project.PATTERN.EMAIL'), $account)) {
			$identityType = 'email';
		}
		
		return compact('account', 'password', 'identityType');
	}
	
}