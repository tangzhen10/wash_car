<?php

namespace App\Http\Controllers\Admin;

/**
 * 管理员类
 * Class ManagerController
 * @package App\Http\Controllers\Admin
 */
class ManagerController extends BaseController {
	
	const TABLE = 'manager';
	
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
	 * 登录
	 * @author 李小同
	 * @date
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
	 */
	public function login() {
		
		# 注册用户
//		$this->manager->creteManager();die;
		
		$post = \Request::all();
		
		if (!empty($post['name']) && !empty($post['password'])) {
			$managerId = $this->manager->checkPwd($post);
			if ($managerId > 0) {
				$managerInfo = $this->manager->saveLoginInfo($managerId);
				if ($managerInfo) {
					json_msg('ok');
				} else {
					json_msg('登录失败，请重试！', 40003);
				}
			}
		} else {
			# 登录状态下不允许进入登录页面
			$managerId = $this->manager->getManagerId();
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
		
		$this->manager->deleteLoginInfo();
		
		return redirect()->route('managerLogin');
	}
	
	/**
	 * 管理员列表
	 * @author 李小同
	 * @date   2018-7-3 15:33:45
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function managerList() {
		
		$managers = $this->manager->getManagerList();
		
		$this->data += compact('managers');
		
		return view('admin/manager/list', $this->data);
	}
}
