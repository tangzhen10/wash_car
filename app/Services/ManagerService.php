<?php

/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-07-03 8:25
 */

namespace App\Services;

/**
 * 管理员服务
 * Class ManagerService
 * @package App\Services
 */
class ManagerService {
	
	public $managerId = 0;
	
	public function __construct() {
		
		$this->managerId = $this->checkLogin();
		
	}
	
	/**
	 * 验证密码
	 * @param array $post
	 * @author 李小同
	 * @date   2018-1-12 00:04:34
	 * @return string|int 验证出错返回错误提示json，验证成功返回账户id
	 */
	public function checkPwd(array $post) {
		
		$manager = $this->getManagerInfoByManagerName($post['manager_name']);
		if ($manager) {
			if ($manager['status'] == 0) json_msg('该账户已被禁用', 40002);
			if (sha1(md5($post['password']).$manager['salt']) == $manager['password']) {
				return $manager['manager_id'];
			} else {
				json_msg('密码不正确', 40002);
			}
		} else {
			json_msg('不存在的账户', 40002);
		}
	}
	
	/**
	 * 根据用户名获取用户信息
	 * @param $managerName string 管理员账户名
	 * @author 李小同
	 * @date   2018-1-12 21:35:34
	 * @return mixed null | array
	 */
	public function getManagerInfoByManagerName($managerName) {
		
		$fields  = ['manager_id', 'manager_name', 'password', 'salt', 'status'];
		$manager = \DB::table('manager')->where('manager_name', $managerName)->first($fields);
		
		return $manager;
	}
	
	/**
	 * 保存登录信息
	 * @param $managerId int 后台账户id
	 * @author 李小同
	 * @date   2018-1-13 22:29:36
	 * @return array 账户信息
	 */
	public function saveLoginInfo($managerId) {
		
		$managerInfo = $this->getManagerInfoByManagerId($managerId);
		$logId       = create_token();
		$key         = sprintf(config('cache.ADMIN_LOG_INFO'), $logId);
		redisSet($key, $managerInfo, 'admin'); # 存储在服务器端
		setcookie(config('project.ADMIN_LOGIN_COOKIE'), $logId, time() + 86400, '/'); # 存在客户端，24h
		
		# 获取到上次登录信息存起来之后再更新
		$this->updateLoginInfo($managerId);
		
		return $managerInfo;
	}
	
	/**
	 * 根据用户ID获取用户信息
	 * @param $managerId
	 * @author 李小同
	 * @date   2018-1-12 21:56:22
	 * @return mixed null | array
	 */
	public function getManagerInfoByManagerId($managerId = 0) {
		
		if ($managerId == 0) $managerId = $this->managerId;
		
		$fields  = ['manager_id', 'manager_name', 'status', 'last_login_ip', 'last_login_time'];
		$manager = \DB::table('manager')->where('manager_id', $managerId)->first($fields);
		
		return $manager;
	}
	
	/**
	 * 更新最后一次登录信息
	 * @param $managerId
	 * @author 李小同
	 * @date   2018-1-14 19:56:40
	 * @return mixed
	 */
	public function updateLoginInfo($managerId) {
		
		$where      = ['manager_id' => $managerId];
		$updateData = [
			'last_login_time' => date('Y-m-d H:i:s'),
			'last_login_ip'   => getClientIp(),
		];
		$res        = \DB::table('manager')->where($where)->update($updateData);
		return $res;
	}
	
	/**
	 * 创建后台账户
	 * @author 李小同
	 * @date   2018-1-12 21:20:29
	 * @return mixed 创建的账户ID
	 */
	public function creteManager() {
		
		$post = \Request::all();
		
		$data['manager_name'] = $post['manager_name'];
		$data['salt']         = create_salt();
		$data['password']     = sha1(md5($post['password']).$data['salt']);
		$data['date_add']     = date('Y-m-d H:i:s');
		
		$managerId = \DB::table('manager')->insertGetId($data);
		
		return $managerId;
	}
	
	/**
	 * 检测登录
	 * @author 李小同
	 * @date   2018-1-14 15:42:07
	 * @return int 登录返回manager_id,未登录返回0
	 */
	public function checkLogin() {
		
		$adminLogKey = config('project.ADMIN_LOGIN_COOKIE');
		
		if (!empty($_COOKIE[$adminLogKey])) {
			
			$logId       = $_COOKIE[$adminLogKey];
			$cacheKey    = sprintf(config('cache.ADMIN_LOG_INFO'), $logId);
			$managerInfo = redisGet($cacheKey, 'admin');
			if (!empty($managerInfo['manager_id'])) return $managerInfo['manager_id'];
		}
		
		return 0;
	}
	
	/**
	 * 获取后台账户ID
	 * @author 李小同
	 * @date   2018-1-18 22:42:46
	 * @return int
	 */
	public function getManagerId() {
		
		return $this->managerId;
	}
	
	/**
	 * 清除登录信息
	 * @author 李小同
	 * @date   2018-7-3 14:27:03
	 * @return bool|mixed
	 */
	public function deleteLoginInfo() {
		
		$adminLogKey = config('project.ADMIN_LOGIN_COOKIE');
		$res         = false;
		if (!empty($_COOKIE[$adminLogKey])) {
			
			$logId = $_COOKIE[$adminLogKey];
			
			# 清除服务器端
			$cacheKey = sprintf(config('cache.ADMIN_LOG_INFO'), $logId);
			$res      = redisDel($cacheKey, 'admin');
		}
		
		# 清除客户端
		setcookie($adminLogKey, null, time() - 1, '/');
		if (isset($_COOKIE[$adminLogKey])) unset($_COOKIE[$adminLogKey]);
		
		return $res;
	}
}