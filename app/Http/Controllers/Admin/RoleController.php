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
	 * @param array $detail
	 * @author 李小同
	 * @date   2018-7-5 14:55:33
	 * @return array
	 */
	public function assocDataForForm($detail = []) {
		
		# 获取指定角色的权限
		$rolePermissions = $this->service->getPermissionsByRoleId($detail['id']);
		
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
	
	/**
	 * 查看拥有指定角色的管理员
	 * @param $id int 角色id
	 * @author 李小同
	 * @date   2018-7-16 22:33:08
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function roleManager($id) {
		
		$this->data['detail']   = $this->service->getDetailById($id);
		$managerIds             = $this->service->getManagersById($id);
		$this->data['managers'] = \ManagerService::getListByIds($managerIds);
		
		return view('admin/role/manager', $this->data);
	}
	
	/**
	 * 移除管理员的角色
	 * @author 李小同
	 * @date   2018-7-23 23:19:43
	 */
	public function removeManager() {
		
		$roleId    = \Request::input('role_id');
		$managerId = \Request::input('manager_id');
		
		$res = $this->service->removeManagerRole($managerId, $roleId);
		json_msg($res ? 'ok' : 'failed');
	}
}
