<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-07-05 9:47
 */

namespace App\Services;

class BaseService {
	
	public $module = '';
	public $userId = 0;
	
	public function __construct() { $this->userId = \UserService::getUserId(); }
	
	/**
	 * 获取详情
	 * @param $id
	 * @author 李小同
	 * @date   2018-7-4 17:40:46
	 * @return array
	 */
	public function getDetailById($id) {
		
		if ($id) {
			$detail = \DB::table($this->module)->where('id', $id)->first();
		} else {
			$detail = static::initDetail();
		}
		
		return $detail;
	}
	
	/**
	 * 创建
	 * @author 李小同
	 * @date   2018-7-4 13:59:36
	 * @return mixed
	 */
	public function create() {
		
		$data = request_all();
		if (method_exists(static::class, 'handleFormData')) static::handleFormData($data);
		unset($data['id']);
		
		$id = \DB::table($this->module)->insertGetId($data);
		
		return $id;
	}
	
	/**
	 * 修改
	 * @author 李小同
	 * @date   2018-7-4 13:59:36
	 * @return mixed
	 */
	public function update() {
		
		$data = request_all();
		
		if (method_exists(static::class, 'handleFormData')) static::handleFormData($data);
		
		\DB::table($this->module)->where('id', $data['id'])->update($data);
		
		if (method_exists(static::class, 'handleAfterUpdate')) static::handleAfterUpdate($data);
		
		return $data['id'];
	}
	
	/**
	 * 修改状态
	 * 启用、停用、删除
	 * @param $id      int
	 * @param $status  int 新状态 1启用 0停用 -1删除
	 * @param $module  string 模块名
	 * @author 李小同
	 * @date   2018-7-4 09:14:47
	 * @return bool
	 */
	public function changeStatus($id, $status, $module) {
		
		$this->_checkStatusValue($status);
		if (method_exists(static::class, 'checkChangeStatus')) static::checkChangeStatus($id, $status, $module);
		
		$source = \DB::table($module)->where('id', $id)->count();
		if (!empty($source)) {
			
			\DB::table($module)->where('id', $id)->update(['status' => $status]);
			
			if (method_exists(static::class, 'handleAfterChangeStatus')) static::handleAfterChangeStatus($id);
			
			return true;
			
		} else {
			
			json_msg(trans('error.illegal_param'), 40003);
		}
	}
	
	/**
	 * 简单修改状态
	 * 不做额外检测，适用于无关联属性的数据状态修改
	 * @param $table
	 * @param $id
	 * @param $status
	 * @author 李小同
	 * @date   2018-7-26 17:09:19
	 * @return mixed
	 */
	public function easyChangeStatus($table, $id, $status) {
		
		$id = intval($id);
		$this->_checkStatusValue($status);
		
		$res = \DB::table($table)->where('id', $id)->update(['status' => $status]);
		
		return $res;
	}
	
	/**
	 * 批量删除
	 * @param array  $ids
	 * @param string $module
	 * @author 李小同
	 * @date   2018-7-21 00:02:37
	 * @return bool
	 */
	public function batchDelete(array $ids = [], $module) {
		
		foreach ($ids as $id) {
			static::changeStatus($id, '-1', $module);
		}
		
		return true;
	}
	
	/**
	 * 为数组里的每一行追加状态文本字段
	 * @param array $list
	 * @author 李小同
	 * @date   2018-7-11 15:54:45
	 */
	public function addStatusText(array &$list = []) {
		
		foreach ($list as &$item) {
			$item['status_text'] = trans('common.'.($item['status'] ? 'enable' : 'disable'));
		}
		unset($item);
	}
	
	/**
	 * 获取表的相关信息
	 * @param string $table    表名
	 * @param array  $fields   要查询的表结构
	 * @param string $database 数据库
	 * @author 李小同
	 * @date   2018-7-13 15:53:02
	 * @return array
	 */
	public function getTableColumns($table, $fields = ['column_name'], $database = '') {
		
		if (empty($database)) $database = env('DB_DATABASE');
		$fields = implode(',', $fields);
		
		$sql     = 'SELECT %s FROM information_schema.COLUMNS WHERE table_schema = \'%s\' AND table_name = \'%s\'';
		$sql     = sprintf($sql, $fields, $database, $table);
		$columns = \DB::select($sql);
		$columns = array_column($columns, 'column_name');
		
		return $columns;
	}
	
	/**
	 * 检测新状态值是否合法
	 * @param $status
	 * @author 李小同
	 * @date   2018-7-25 22:08:44
	 */
	private function _checkStatusValue($status) {
		
		if (!in_array($status, ['1', '0', '-1'])) json_msg(trans('error.illegal_param'), 40001);
	}
	
}