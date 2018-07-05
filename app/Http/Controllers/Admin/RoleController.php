<?php

namespace App\Http\Controllers\Admin;

/**
 * 后台角色控制器
 * Class RoleController
 * @package App\Http\Controllers\Admin
 */
class RoleController extends BaseController {
	
	const MODULE = 'role';
	
	/**
	 * 角色列表
	 * @author 李小同
	 * @date   2018-7-5 11:09:09
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function roleList() {
		
		$this->data['roles'] = $this->service->getList();
		
		return view('admin/role/list', $this->data);
	}
	
	/**
	 * 增改表单所需相关数据
	 * @param $data
	 * @author 李小同
	 * @date   2018-7-5 14:55:33
	 * @return array
	 */
	public function assocDataForForm($data = null) {
		
		# 获取指定角色的权限
		$rolePermissions = $this->service->getPermissionsByRoleId($data['detail']['id']);
		
		# 所有启用的权限，树状结构
		$permissions = \PermissionService::getTreeList('1');
		
		# 给指定角色拥有的权限加上选中效果
		foreach ($permissions as &$permission1) {
			$permission1['checked'] = in_array($permission1['id'], $rolePermissions) ? 'checked' : '';
			if (!empty($permission1['sub'])) {
				foreach ($permission1['sub'] as &$permission2) {
					$permission2['checked'] = in_array($permission2['id'], $rolePermissions) ? 'checked' : '';
					if (!empty($permission2['sub'])) {
						foreach ($permission2['sub'] as &$permission3) {
							$permission3['checked'] = in_array($permission3['id'], $rolePermissions) ? 'checked' : '';
						}
						unset($permission3);
					}
				}
				unset($permission2);
			}
		}
		unset($permission1);
		
		return compact('permissions', 'rolePermissions');
	}
}
