<?php

namespace App\Http\Controllers\Admin;

/**
 * 管理员类
 * Class ManagerController
 * @package App\Http\Controllers\Admin
 */
class ManagerController extends BaseController {
	
	const MODULE = 'manager';
	
	/**
	 * 后台首页
	 * @author 李小同
	 * @date   2018-7-3 14:31:55
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index() {
		
		$server = $_SERVER;
		$this->data += compact('manager', 'server');
		
		return view('admin/index', $this->data);
	}
	
	/**
	 * 管理员列表
	 * @author 李小同
	 * @date   2018-7-3 15:33:45
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function managerList() {
		
		$this->data['managers'] = $this->service->getList();
		
		return view('admin/manager/list', $this->data);
	}
	
	/**
	 * 登录
	 * @author 李小同
	 * @date
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
	 */
	public function login() {
		
		$name     = \Request::input('name');
		$password = \Request::input('password');
		
		if (!empty($name) && !empty($password)) {
			$managerId = $this->service->checkPwd($name, $password);
			if ($managerId > 0) {
				$managerInfo = $this->service->saveLoginInfo($managerId);
				if ($managerInfo) {
					json_msg('ok');
				} else {
					json_msg('登录失败，请重试！', 40003);
				}
			}
		} else {
			# 登录状态下不允许进入登录页面
			$managerId = $this->service->getManagerId();
			if ($managerId > 0) return redirect()->route('adminIndex');
		}
		
		return view('admin/manager/login');
	}
	
	/**
	 * 后台登出
	 * @author 李小同
	 * @date   2018-7-3 14:30:57
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function logout() {
		
		$this->service->deleteLoginInfo();
		
		return redirect()->route('managerLogin');
	}
	
	/**
	 * 增改表单所需相关数据
	 * @param array $detail
	 * @author 李小同
	 * @date   2018-7-5 14:55:15
	 * @return array
	 */
	public function assocDataForForm($detail = []) {
		
		# 获取管理员拥有的角色
		$managerRoles = $this->service->getRolesByManagerId($detail['id']);
		
		# 所有启用的角色列表
		$roles = \RoleService::getList('1');
		
		# 给管理员拥有的角色加上选中效果
		foreach ($roles as &$role) {
			$role['checked'] = in_array($role['id'], $managerRoles) ? 'checked' : '';
		}
		unset($role);
		
		return compact('roles', 'managerRoles');
	}
}
