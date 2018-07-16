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
class ManagerService extends BaseService {
	
	public $managerId = 0;
	public $module    = 'manager';
	
	public function __construct() {
		
		$this->managerId = $this->checkLogin();
		
	}
	
	/**
	 * 初始化的数据，用于填充新增数据表单默认值
	 * @author 李小同
	 * @date   2018-7-5 11:17:55
	 * @return array
	 */
	public function initDetail() {
		
		$detail = [
			'id'       => '0',
			'name'     => '',
			'password' => '',
			'role'     => [],
			'status'   => '1',
		];
		
		return $detail;
	}
	
	/**
	 * 验证密码
	 * @param string $name
	 * @param string $password
	 * @author 李小同
	 * @date   2018-1-12 00:04:34
	 * @return string|int 验证出错返回错误提示json，验证成功返回账户id
	 */
	public function checkPwd($name, $password) {
		
		$manager = $this->getManagerInfoByManagerName($name);
		
		if ($manager) {
			
			if ($manager['status'] == 0) json_msg('该账户已被禁用', 50001);
			
			if (easy_encrypt($password, $manager['salt']) == $manager['password']) {
				return $manager['id'];
			} else {
				json_msg('密码不正确', 50001);
			}
		} else {
			json_msg('不存在的账户', 50001);
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
		
		$fields  = ['id', 'name', 'password', 'salt', 'status'];
		$manager = \DB::table('manager')->where('name', $managerName)->first($fields);
		
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
		
		$fields  = ['id', 'name', 'status', 'last_login_ip', 'last_login_at'];
		$manager = \DB::table('manager')->where('id', $managerId)->first($fields);
		
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
		
		$where      = ['id' => $managerId];
		$updateData = [
			'last_login_at' => date('Y-m-d H:i:s'),
			'last_login_ip' => getClientIp(),
		];
		$res        = \DB::table('manager')->where($where)->update($updateData);
		return $res;
	}
	
	/**
	 * 预处理请求数据
	 * @param array $data
	 * @author 李小同
	 * @date   2018-7-5 11:45:34
	 */
	public function handleFormData(array &$data) {
		
		$detail = \DB::table($this->module)->where('name', $data['name'])->orWhere('id', $data['id'])->first();
		if ($data['id']) { # 更新
			
			if (($data['password'] || $data['password_repeat']) && $data['password'] != $data['password_repeat']) {
				json_msg(trans('error.different_twice_pwd'), 50001);
			}
			
			# 无密码则密码不更新
			if (is_null($data['password'])) {
				$data['password'] = $detail['password'];
			} else {
				$data['password'] = easy_encrypt($data['password'], $detail['salt']);
			}
		} else { # 新增
			# 检查重名
			$error = trans('validation.has_been_registered', ['attr' => trans('common.manager_name')]);
			if (isset($detail['name'])) json_msg($error, 40002);
			
			$data['salt']      = create_salt();
			$data['password']  = easy_encrypt($data['password'], $data['salt']);
			$data['create_at'] = date('Y-m-d H:i:s');
		}
		# 权限不得为空
		if (empty($data['roles'])) {
			$error = trans('validation.required', ['attr' => trans('common.permission')]);
			json_msg($error, 40001);
		}
		
		unset($data['password_repeat']);
	}
	
	/**
	 * 检测登录
	 * @author 李小同
	 * @date   2018-1-14 15:42:07
	 * @return int 登录返回id,未登录返回0
	 */
	public function checkLogin() {
		
		$adminLogKey = config('project.ADMIN_LOGIN_COOKIE');
		
		if (!empty($_COOKIE[$adminLogKey])) {
			
			$logId       = $_COOKIE[$adminLogKey];
			$cacheKey    = sprintf(config('cache.ADMIN_LOG_INFO'), $logId);
			$managerInfo = redisGet($cacheKey, 'admin');
			if (!empty($managerInfo['id'])) {
				
				redisSet($cacheKey, $managerInfo, 'admin'); # 续签
				
				return $managerInfo['id'];
			}
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
	
	/**
	 * 获取管理员列表
	 * @author 李小同
	 * @date   2018-7-3 15:26:26
	 * @return array
	 */
	public function getList() {
		
		$fields   = ['a.id', 'a.name', 'a.create_at', 'a.last_login_at', 'a.last_login_ip', 'a.status'];
		$fields[] = \DB::raw('GROUP_CONCAT(t_c.name) AS role');
		$list     = \DB::table('manager AS a')
		               ->leftJoin('manager_role AS b', 'b.manager_id', '=', 'a.id')
		               ->leftJoin('role AS c', 'c.id', '=', 'b.role_id')
		               ->where('a.status', '!=', '-1')
		               ->groupBy('a.id')
		               ->get($fields)
		               ->toArray();
		$this->addStatusText($list);
		
		return $list;
	}
	
	/**
	 * 检查是否允许修改状态
	 * @param $id     int
	 * @param $status int 新状态 1启用 0停用 -1删除
	 * @author 李小同
	 * @date   2018-7-5 14:45:13
	 * @return bool
	 */
	public function checkChangeStatus($id, $status) {
		
		# 不能删除自己
		if ($status == '-1' && $id == $this->managerId) {
			json_msg(trans('error.can_not_delete').', '.trans('error.can_not_delete_self'), 40003);
		}
	}
	
	/**
	 * 获取管理员的角色
	 * @param int $managerId
	 * @author 李小同
	 * @date   2018-7-5 15:28:55
	 * @return mixed
	 */
	public function getRolesByManagerId($managerId = 0) {
		
		if (empty($managerId)) $managerId = $this->managerId;
		$managerRoles = \DB::table('manager_role')->where('manager_id', $managerId)->pluck('role_id')->toArray();
		
		return $managerRoles;
	}
	
	/**
	 * 创建
	 * @author 李小同
	 * @date   2018-7-4 13:59:36
	 * @return mixed
	 */
	public function create() {
		
		return $this->update();
		
	}
	
	/**
	 * 修改
	 * @author 李小同
	 * @date   2018-7-4 13:59:36
	 * @return mixed
	 */
	public function update() {
		
		$data = request_all();
		
		$this->handleFormData($data);
		
		$roles = $data['roles'];
		unset($data['roles']);
		
		\DB::beginTransaction();
		
		try {
			
			if ($data['id'] == 0) {
				
				unset($data['id']);
				$managerId = \DB::table($this->module)->insertGetId($data);
				
			} else {
				
				\DB::table($this->module)->where('id', $data['id'])->update($data);
				$managerId = $data['id'];
				
				\DB::table('manager_role')->where('manager_id', $managerId)->delete();
			}
			
			$sql = 'INSERT INTO `t_manager_role` (manager_id, role_id) VALUES ';
			if (count($roles)) {
				foreach ($roles as $roleId) $sql .= sprintf('(%s, %s),', $managerId, $roleId);
				$sql = substr($sql, 0, -1);
				\DB::insert($sql);
			}
			\DB::commit();
			
			return $managerId;
			
		} catch (\Exception $e) {
			\DB::rollback();
			return false;
		}
		
	}
	
}