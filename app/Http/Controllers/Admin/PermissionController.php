<?php

namespace App\Http\Controllers\Admin;

class PermissionController extends BaseController {
	
	const MODULE = 'permission';
	
	/**
	 * 权限列表
	 * @author 李小同
	 * @date   2018-7-4 14:03:06
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function permissionList() {
		
		$this->data['permissions'] = $this->service->getList();
		
		return view('admin/permission/list', $this->data);
	}
	
	/**
	 * 增改表单所需相关数据
	 * @param $data
	 * @author 李小同
	 * @date   2018-7-5 14:56:35
	 * @return array
	 */
	public function assocDataForForm($data = null) {
		
		$permissions = $this->service->getEnableList();
		
		return compact('permissions');
	}
	
}
