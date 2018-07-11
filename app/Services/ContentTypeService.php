<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-07-10 23:57
 */

namespace App\Services;

class ContentTypeService extends BaseService {
	
	public $module = 'content_type';
	
	/**
	 * 获取文档类型详情
	 * @param int $id
	 * @author 李小同
	 * @date   2018-7-11 15:43:54
	 * @return array
	 */
	public function getDetailById($id = 0) {
		
		if ($id) {
			$detail = \DB::table($this->module)->where('id', $id)->first(['id', 'name']);
			
			$detail['structure'] = \DB::table('content_type_structure')
			                          ->where('content_type_id', $id)
			                          ->get()
			                          ->toArray();
		} else {
			$detail = [
				'id'        => '0',
				'name'      => '',
				'structure' => [],
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
		
		$res = \DB::table('form_element')->where('status', '1')->get(['name', 'type'])->toArray();
		
		return $res;
	}
	
	/**
	 * 新增
	 * @author 李小同
	 * @date   2018-7-11 15:37:29
	 * @return bool
	 */
	public function create() {
		
		return $this->update();
	}
	
	/**
	 * 修改
	 * @author 李小同
	 * @date   2018-7-11 17:16:15
	 * @return bool
	 */
	public function update() {
		
		$data = request_all();
		\DB::beginTransaction();
		try {
			
			$typeData = ['name' => $data['type_name']];
			
			if ($data['id'] == 0) {
				
				unset($data['id']);
				$typeId = \DB::table($this->module)->insertGetId($typeData);
				
			} else {
				
				$typeId = $data['id'];
				\DB::table($this->module)->where('id', $typeId)->update($typeData);
				\DB::table('content_type_structure')->delete('content_type_id', $typeId);
			}
			
			$sql    = 'INSERT INTO 
							`t_content_type_structure` 
						(
							`content_type_id`,
							`name_text`,
							`type`,
							`name`,
							`value`
						) VALUES ';
			$valStr = '';
			foreach ($data['field_name_text'] as $key => $value) {
				
				if (empty($data['field_name_text'][$key]) || empty($data['field_type'][$key]) || empty($data['field_name'][$key])) {
					continue;
				} else { # 单选框和复选框必须有值
					if (in_array($data['field_type'], ['radio', 'checkbox']) && empty($data['field_value'])) {
						json_msg(trans('validation.required', ['attr' => $data['field_name_text'][$key]].'备选值'), 40001);
					}
				}
				
				$valStr .= sprintf('(\'%s\',\'%s\',\'%s\',\'%s\',\'%s\'),', $typeId, $data['field_name_text'][$key], $data['field_type'][$key], $data['field_name'][$key], $data['field_value'][$key]);
			}
			if ($valStr) {
				$sql .= substr($valStr, 0, -1);
				\DB::insert($sql);
			}
			\DB::commit();
			return $typeId;
			
		} catch (\Exception $e) {
			\DB::rollback();
			//echo $e->getMessage();
			return false;
		}
	}
	
	/**
	 * 获取文档类型列表
	 * @author 李小同
	 * @date   2018-7-11 15:46:58
	 * @return array
	 */
	public function getTypeList() {
		
		$list = \DB::table($this->module)->where('status', '!=', '-1')->get(['id', 'name', 'status'])->toArray();
		
		$this->addStatusText($list);
		
		return $list;
	}
	
	
}