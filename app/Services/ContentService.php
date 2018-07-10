<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-07-10 23:57
 */

namespace App\Services;

class ContentService extends BaseService {
	
	public $module = 'content';
	
	public function getStructureDetailById($id = 0) {
		
		if ($id) {
			
		} else {
			$detail = [
				'id'        => '0',
				'name'      => '',
				'type'      => '',
				'form_type' => '',
				'status'    => '',
			];
		}
		
		return $detail;
	}
	
	/**
	 * 获取表单元素类型
	 * @author 李小同
	 * @date   2018-7-11 00:18:53
	 * @return array
	 */
	public function getFormElements() {
		
		$res = \DB::table('form_element')->where('status', '1')->get()->toArray();
		
		return $res;
	}
}