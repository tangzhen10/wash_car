<?php

namespace App\Http\Controllers\Admin;

class PermissionController extends BaseController {
	
	const TABLE = 'permission';
	
	/**
	 * 权限列表
	 * @author 李小同
	 * @date   2018-7-4 14:03:06
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function permissionList() {
		
		$this->data['permissions'] = \PermissionService::getList();
		
		return view('admin/permission/list', $this->data);
	}
	
	/**
	 * 增修权限
	 * @param $id int
	 * @author 李小同
	 * @date   2018-7-4 16:50:50
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function form($id = 0) {
		
		if (\Request::getMethod() == 'GET') {
			
			$this->data['detail']      = \PermissionService::getDetailById($id);
			$this->data['permissions'] = \PermissionService::getEnableList($this->data['detail']['pid']);
			
			return view('admin/permission/form', $this->data);
			
		} else {
			
			$id = \Request::input('id');
			if ($id) {
				$res = \PermissionService::update();
			} else {
				$res = \PermissionService::create();
			}
			$this->render($res);
		}
	}
	
}
