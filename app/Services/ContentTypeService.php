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
				\DB::table('content_type_structure')->where('content_type_id', $typeId)->delete();
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
	
	public function getFormHtml($id) {
		
		$html        = '';
		$contentType = $this->getDetailById($id);
		if (!empty($contentType['structure'])) {
			foreach ($contentType['structure'] as $field) {
				
				$funcName = $field['type'].'FormElement';
				if (method_exists($this, $funcName)) $html .= $this->$funcName($field);
			}
		}
		
		return $html;
	}
	
	/**
	 * input表单
	 * @param array $field
	 * @author 李小同
	 * @date   2018-7-12 22:02:46
	 * @return string
	 */
	public function inputFormElement(array $field) {
		
		$html = '<p>
					<span>'.$field['name_text'].'：</span>
					<input class="input-text radius" name="'.$field['name'].'" value="'.$field['value'].'" />
				</p>';
		
		return $html;
	}
	
	/**
	 * textarea表单
	 * @param array $field
	 * @author 李小同
	 * @date   2018-7-12 22:02:46
	 * @return string
	 */
	public function textareaFormElement(array $field) {
		
		$html = '<p>
					<span>'.$field['name_text'].'：</span>
					<textarea class="textarea radius" name="'.$field['name'].'">'.$field['value'].'</textarea>
				</p>';
		
		return $html;
	}
	
	/**
	 * radio表单
	 * @param array $field
	 * @author 李小同
	 * @date   2018-7-12 22:02:46
	 * @return string
	 */
	public function radioFormElement(array $field) {
		
		$html   = '<p><span>'.$field['name_text'].'：</span><span class="skin-minimal form_value">';
		$groups = explode('|', $field['value']);
		foreach ($groups as $group) {
			$pos   = strpos($group, ',');
			$text  = substr($group, 0, $pos);
			$value = substr($group, $pos);
			$html .= '<span class="radio-box">
						<label><input type="radio" name="'.$field['name'].'" value="'.$value.'" >'.$text.'</label>
					</span>';
		}
		$html .= '</span></p>';
		
		return $html;
	}
	
	/**
	 * checkbox表单
	 * @param array $field
	 * @author 李小同
	 * @date   2018-7-12 22:33:34
	 * @return string
	 */
	public function checkboxFormElement(array $field) {
		
		$html   = '<p><span>'.$field['name_text'].'：</span><span class="skin-minimal form_value">';
		$groups = explode('|', $field['value']);
		foreach ($groups as $group) {
			$pos   = strpos($group, ',');
			$text  = substr($group, 0, $pos);
			$value = substr($group, $pos);
			$html .= '<span class="check-box">
						<label><input type="checkbox" name="'.$field['name'].'" value="'.$value.'" >'.$text.'</label>
					</span>';
		}
		$html .= '</span></p>';
		
		return $html;
	}
	
	/**
	 * select表单
	 * @param array $field
	 * @author 李小同
	 * @date   2018-7-12 22:39:55
	 * @return string
	 */
	public function selectFormElement(array $field) {
		
		$html   = '<p><span>'.$field['name_text'].'：</span><select name="'.$field['name'].'" class="select-box">';
		$groups = explode('|', $field['value']);
		foreach ($groups as $group) {
			$pos   = strpos($group, ',');
			$text  = substr($group, 0, $pos);
			$value = substr($group, $pos);
			$html .= '<option value="'.$value.'" >'.$text.'</option>';
		}
		$html .= '</select></p>';
		
		return $html;
	}
	
}