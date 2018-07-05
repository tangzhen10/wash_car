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
class PermissionService {
	
	/**
	 * 创建权限
	 * @author 李小同
	 * @date   2018-7-4 13:59:36
	 * @return mixed
	 */
	public function create() {
		
		$data = \Request::all();
		if ($data['pid'] > 0) {
			if (empty($data['route'])) json_msg(trans('validation.required', ['attr' => trans('common.route')]), 40001);
			
			$pLevel        = \DB::table('permission')->where('id', $data['pid'])->pluck('level');
			$data['level'] = $pLevel[0] + 1;
		} else {
			$data['level'] = 1;
		}
		$permissionId = \DB::table('permission')->insertGetId($data);
		
		return $permissionId;
	}
	
	/**
	 * 修改权限
	 * @author 李小同
	 * @date   2018-7-4 13:59:36
	 * @return mixed
	 */
	public function update() {
		
		$data = \Request::all();
		if ($data['pid'] > 0) {
			if (empty($data['route'])) json_msg(trans('validation.required', ['attr' => trans('common.route')]), 40001);
			
			$pLevel        = \DB::table('permission')->where('id', $data['pid'])->pluck('level');
			$data['level'] = $pLevel[0] + 1;
		} else {
			$data['level'] = 1;
		}
		\DB::table('permission')->where('id', $data['id'])->update($data);
		
		return $data['id'];
	}
	
	/**
	 * 获取权限列表，level <= 3
	 * @author 李小同
	 * @date   2018-7-4 14:02:21
	 * @return array
	 */
	public function getList() {
		
		$list = \DB::table('permission')
		           ->where('status', '!=', '-1')
		           ->orderBy('level', 'asc')
		           ->orderBy('sort', 'asc')
		           ->get()
		           ->toArray();
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
		
		$sortList = [];
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
	 * 获取启用的权限列表，用于权限表单页的父节点下拉选项
	 * @param $pid int 父节点
	 * @author 李小同
	 * @date   2018-7-4 16:52:30
	 * @return array
	 */
	public function getEnableList($pid = 0) {
		
		$list     = \DB::table('permission')
		               ->where('status', '1')
		               ->where('level', '<', '3')
		               ->orderBy('level', 'asc')
		               ->orderBy('sort', 'asc')
		               ->get()
		               ->toArray();
		$sortList = [];
		foreach ($list as $item) {
			if ($item['level'] == '1') {
				$item['selected']      = $pid == $item['id'] ? 'selected' : '';
				$sortList[$item['id']] = $item;
			}
		}
		
		foreach ($list as $item) {
			if ($item['level'] == '2') {
				$item['selected']                = $pid == $item['id'] ? 'selected' : '';
				$sortList[$item['pid']]['sub'][] = $item;
			}
		}
		
		return $sortList;
	}
	
	/**
	 * 获取详情
	 * @param $id
	 * @author 李小同
	 * @date   2018-7-4 17:40:46
	 * @return array
	 */
	public function getDetailById($id) {
		
		if ($id) {
			$detail = \DB::table('permission')->where('id', $id)->first();
		} else {
			$detail = [
				'id'     => 0,
				'name'   => '',
				'route'  => '',
				'pid'    => 0,
				'sort'   => 1,
				'status' => '1',
				'show'   => '1',
			];
		}
		
		return $detail;
	}
	
}