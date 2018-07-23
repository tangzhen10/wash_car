<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-07-03 15:11
 */

namespace App\Services;

/**
 * 角色服务类
 * Class RoleService
 * @package App\Services
 */
class RoleService extends BaseService {
	
	public $module = 'role';
	
	/**
	 * 初始化的数据，用于填充新增数据表单默认值
	 * @author 李小同
	 * @date   2018-7-5 09:51:00
	 * @return array
	 */
	public function initDetail() {
		
		$detail = [
			'id'          => 0,
			'name'        => '',
			'description' => '',
			'status'      => '1',
			'permissions' => [],
		];
		
		return $detail;
	}
	
	/**
	 * 创建
	 * @author 李小同
	 * @date   2018-7-5 13:08:34
	 * @return mixed
	 */
	public function create() {
		
		return $this->update();
		
	}
	
	/**
	 * 修改
	 * @author 李小同
	 * @date   2018-7-5 14:31:23
	 * @return mixed
	 */
	public function update() {
		
		$data = request_all();
		
		$permissions = empty($data['permissions']) ? [] : $data['permissions'];
		unset($data['permissions']);
		
		# 保存角色对应权限
		\DB::beginTransaction();
		
		try {
			
			if ($data['id'] == 0) {
				
				unset($data['id']);
				$roleId = \DB::table($this->module)->insertGetId($data);
				
			} else {
				
				\DB::table($this->module)->where('id', $data['id'])->update($data);
				$roleId = $data['id'];
			}
			
			\DB::table('role_permission')->where('role_id', $roleId)->delete();
			
			$sql = 'INSERT INTO `t_role_permission` (role_id, permission_id) VALUES ';
			if (count($permissions)) {
				foreach ($permissions as $permissionId) $sql .= sprintf('(%s, %s),', $roleId, $permissionId);
				$sql = substr($sql, 0, -1);
				\DB::insert($sql);
			}
			\DB::commit();
			
			return $roleId;
			
		} catch (\Exception $e) {
			\DB::rollback();
			return false;
		}
	}
	
	/**
	 * 通过角色id获取对应的权限
	 * @param $roleId
	 * @author 李小同
	 * @date   2018-7-5 14:12:21
	 * @return array
	 */
	public function getPermissionsByRoleId($roleId) {
		
		$res = \DB::table('role_permission AS a')
		          ->join('permission AS b', 'b.id', '=', 'a.permission_id')
		          ->where('b.status', '1');
		
		if (is_array($roleId)) {
			$res = $res->whereIn('role_id', $roleId);
		} else {
			$res = $res->where('role_id', $roleId);
		}
		$res = $res->pluck(\DB::raw('DISTINCT(permission_id) AS permission_id'))->toArray();
		
		return $res;
	}
	
	/**
	 * 获取列表
	 * @param $status
	 * @author 李小同
	 * @date   2018-7-5 14:24:36
	 * @return mixed
	 */
	public function getList($status = null) {
		
		$list = \DB::table($this->module);
		
		if ($status === null) {
			$list = $list->where('status', '!=', '-1');
		} elseif (is_numeric($status)) {
			$list = $list->where('status', $status);
		} elseif (is_array($status)) {
			$list = $list->whereIn('status', $status);
		} else {
			json_msg(trans('error.illegal_param'), 40003);
		}
		$list = $list->get()->toArray();
		
		return $list;
	}
	
	/**
	 * 获取指定角色下的管理员
	 * @param $id int 角色id
	 * @author 李小同
	 * @date   2018-7-16 18:13:03
	 * @return mixed
	 */
	public function getManagersById($id) {
		
		$managers = \DB::table('manager_role')->where('role_id', $id)->pluck('manager_id')->toArray();
		
		return $managers;
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
		
		# 该角色下还有管理员时，不可以删除
		if ($status == '-1') {
			
			$managers = $this->getManagersById($id);
			if (count($managers)) {
				json_msg(trans('error.can_not_delete', ['reason' => trans('error.role_have_mangers')]), 50001);
			}
		}
	}
	
	/**
	 * 移除管理员的角色
	 * @param $managerId
	 * @param $roleId
	 * @author 李小同
	 * @date   2018-7-23 23:18:46
	 * @return mixed
	 */
	public function removeManagerRole($managerId, $roleId) {
		
		if ($roleId && $managerId) {
			
			$where = ['manager_id' => $managerId, 'role_id' => $roleId];
			$res   = \DB::table('manager_role')->where($where)->delete();
			return $res;
			
		} else {
			json_msg(trans('error.illegal_param'), 40001);
		}
	}
}