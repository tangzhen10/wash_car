<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-07-04 11:21
 */

namespace App\Services;

/**
 * 权限服务类
 * Class PermissionService
 * @package App\Services
 */
class PermissionService extends BaseService {
	
	public $module = 'permission';
	
	/**
	 * 初始化的数据，用于填充新增数据表单默认值
	 * @author 李小同
	 * @date   2018-7-5 09:53:55
	 * @return array
	 */
	public function initDetail() {
		
		$detail = [
			'id'     => 0,
			'name'   => '',
			'route'  => '',
			'pid'    => 0,
			'sort'   => 1,
			'status' => '1',
			'show'   => '1',
		];
		
		return $detail;
	}
	
	/**
	 * 预处理请求数据
	 * @param array $data
	 * @author 李小同
	 * @date   2018-7-5 10:01:08
	 */
	public function handleFormData(array &$data) {
		
		if ($data['pid'] > 0) {
			if (empty($data['route'])) json_msg(trans('validation.required', ['attr' => trans('common.route')]), 40001);
			
			$pLevel        = \DB::table($this->module)->where('id', $data['pid'])->pluck('level')->toArray();
			$data['level'] = $pLevel[0] + 1;
		} else {
			$data['level'] = 1;
		}
	}
	
	/**
	 * 获取权限树状列表，level <= 3
	 * @param $status null|string 状态
	 * @author 李小同
	 * @date   2018-7-4 14:02:21
	 * @return array
	 */
	public function getTreeList($status = null) {
		
		$list = \DB::table($this->module);
		if ($status === null) {
			$list = $list->where('status', '!=', '-1');
		} elseif (is_numeric($status)) {
			$list = $list->where('status', $status);
		} elseif (is_array($status)) {
			$list = $list->whereIn('status', $status);
		}
		
		$list = $list->orderBy('level', 'asc')->orderBy('sort', 'asc')->get()->toArray();
		
		foreach ($list as &$item) {
			$item['status_text'] = trans('common.'.($item['status'] ? 'enable' : 'disable'));
		}
		unset($item);
		
		$treeList = [];
		foreach ($list as $item) {
			if ($item['level'] == '1') {
				$treeList[$item['id']] = $item;
			}
		}
		
		$level3 = [];
		foreach ($list as $item) {
			if ($item['level'] == '3') {
				$level3[$item['pid']][] = $item;
			}
		}
		
		foreach ($list as $item) {
			if ($item['level'] == '2') {
				if (isset($level3[$item['id']])) $item['sub'] = $level3[$item['id']];
				$treeList[$item['pid']]['sub'][$item['id']] = $item;
			}
		}
		
		return $treeList;
	}
	
	/**
	 * 获取权限列表，二维数组，平行结构
	 * @author 李小同
	 * @date   2018-7-5 09:37:25
	 * @return array
	 */
	public function getList() {
		
		$sortList = [];
		$treeList = $this->getTreeList();
		foreach ($treeList as $item) {
			
			$subList2 = [];
			if (isset($item['sub'])) {
				$subList2 = $item['sub'];
				unset($item['sub']);
			}
			$sortList[] = $item;
			foreach ($subList2 as $item2) {
				$subList3 = [];
				if (isset($item2['sub'])) {
					$subList3 = $item2['sub'];
					unset($item2['sub']);
				}
				$sortList[] = $item2;
				foreach ($subList3 as $item3) {
					$sortList[] = $item3;
				}
			}
		}
		
		return $sortList;
	}
	
	/**
	 * 获取启用的权限列表
	 * 1. 权限表单页的父节点下拉选项
	 * 2. 后台页面左侧菜单列表
	 * @param $permissions array 允许的权限
	 * @author 李小同
	 * @date   2018-7-4 16:52:30
	 * @return array
	 */
	public function getEnableList($permissions = []) {
		
		$list = \DB::table($this->module)->where('status', '1')->where('show', '1')->where('level', '<=', '2');
		
		if ($permissions) $list = $list->whereIn('id', $permissions);
		
		$list = $list->orderBy('level', 'asc')->orderBy('sort', 'asc')->get()->toArray();
		
		$sortList = [];
		foreach ($list as $item) {
			if ($item['level'] == '1') $sortList[$item['id']] = $item;
		}
		
		foreach ($list as $item) {
			if ($item['level'] == '2') $sortList[$item['pid']]['sub'][] = $item;
		}
		
		return $sortList;
	}
	
	/**
	 * 获取页面菜单
	 * @author 李小同
	 * @date   2018-7-5 10:59:15
	 * @return array
	 */
	public function getMenuList() {
		
		# 获取用户权限
		$roleIds     = \ManagerService::getRolesByManagerId();
		$permissions = \RoleService::getPermissionsByRoleId($roleIds);
		
		$menus = $this->getEnableList($permissions);
		
		return $menus;
	}
	
	/**
	 * 面包屑
	 * @param array $menus 菜单数据
	 * @author 李小同
	 * @date   2018-7-7 16:32:46
	 * @return array
	 */
	public function getBreadCrumbs(array $menus = []) {
		
		$breadcrumb = [];
		$currentUrl = \Request::getRequestUri();
		foreach ($menus as $menu) {
			if (!empty($menu['sub'])) {
				foreach ($menu['sub'] as $item) {
					if ($item['level'] == '2' && '/admin/'.$item['route'] == $currentUrl) {
						$breadcrumb = [
							[
								'text' => trans('common.home_page'),
								'url'  => route('adminIndex'),
							],
							[
								'text' => '>',
								'url'  => '',
							],
							[
								'text' => $menu['name'],
								'url'  => route('adminIndex').'/'.$menu['route'],
							],
							[
								'text' => '>',
								'url'  => '',
							],
							[
								'text' => $item['name'],
								'url'  => route('adminIndex').'/'.$item['route'],
							],
						];
						return $breadcrumb;
					}
				}
			}
		}
		
		return $breadcrumb;
	}
}