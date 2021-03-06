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
	 * @param int  $id
	 * @param bool $flag 是否只返回字段结构
	 * @author 李小同
	 * @date   2018-7-11 15:43:54
	 * @return array
	 */
	public function getDetailById($id = 0, $flag = false) {
		
		if ($id) {
			
			$cacheKey = sprintf(config('cache.CONTENT_TYPE'), $id);
			$detail   = redisGet($cacheKey);
			if (false === $detail) {
				$detail = \DB::table($this->module)->where('id', $id)->first(['id', 'name', 'type']);
				
				$detail['structure'] = \DB::table('content_type_structure')
				                          ->where('content_type_id', $id)
				                          ->get()
				                          ->toArray();
				redisSet($cacheKey, $detail);
			}
		} else {
			$detail = [
				'id'        => '0',
				'name'      => '',
				'type'      => '2', # 1产品分类，2产品
				'structure' => [],
			];
		}
		
		return $flag ? $detail['structure'] : $detail;
	}
	
	/**
	 * 获取表单元素类型
	 * @author 李小同
	 * @date   2018-7-11 00:18:53
	 * @return array
	 */
	public function getFormElements() {
		
		$cacheKey = config('cache.FORM_ELEMENT');
		$res      = redisGet($cacheKey, 'admin');
		if ($res === false) {
			$res = \DB::table('form_element')->where('status', '1')->get(['name', 'type'])->toArray();
			redisSet($cacheKey, $res, 'admin');
		}
		
		return $res;
	}
	
	/**
	 * 新增
	 * @author 李小同
	 * @date   2018-7-11 15:37:29
	 * @return bool
	 */
	public function create() {
		
		$data = request_all();
		if (empty($data['name'])) json_msg(trans('validation.required', ['attr' => trans('common.name')]), 40001);
		
		# 检测重名
		$hasName = \DB::table($this->module)->where('name', $data['name'])->count('id');
		if ($hasName) json_msg(trans('validation.has_exist', ['attr' => trans('common.name')]), 40002);
		
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
			
			$typeData = ['name' => $data['name']];
			
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
			if (!empty($data['field_name_text'])) {
				
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
			}
			
			\DB::commit();
			
			# 清缓存
			$this->delCache($typeId);
			
			return $typeId;
			
		} catch (\Exception $e) {
			\DB::rollback();
			json_msg($e->getMessage(), $e->getCode());
			return false;
		}
	}
	
	/**
	 * 获取文档类型列表
	 * @param array $where
	 * @author 李小同
	 * @date   2018-7-11 15:46:58
	 * @return array
	 */
	public function getList(array $where = []) {
		
		$fields = ['id', 'name', 'status'];
		$list   = \DB::table($this->module)->where('status', '!=', '-1')->where($where)->get($fields)->toArray();
		$this->addStatusText($list);
		
		return $list;
	}
	
	/**
	 * 获取文章基本属性表字段
	 * @author 李小同
	 * @date   2018-7-13 21:16:55
	 * @return array
	 */
	public function getArticlePublicFields() {
		
		$cacheKey = sprintf(config('cache.TABLE_COLUMN'), 't_article');
		$fields   = redisGet($cacheKey);
		if (false === $fields) {
			$fields = $this->getTableColumns('t_article');
			redisSet($cacheKey, $fields);
		}
		
		return $fields;
	}
	
	/**
	 * 改变状态后的回调函数
	 * @param int $id
	 * @author 李小同
	 * @date   2018-7-14 10:03:59
	 */
	public function handleAfterChangeStatus($id) {
		
		$this->delCache($id);
	}
	
	/**
	 * 清缓存
	 * @param int $id 文档类型id
	 * @author 李小同
	 * @date   2018-7-14 10:03:11
	 * @return bool
	 */
	public function delCache($id) {
		
		$cacheKey = sprintf(config('cache.CONTENT_TYPE'), $id);
		return redisDel($cacheKey);
	}
	
	# region formElement
	/**
	 * 获取自定义的表单元素
	 * @param $contentTypeId
	 * @param $articleId
	 * @author 李小同
	 * @date   2018-7-14 15:24:29
	 * @return string
	 */
	public function getFormHtml($contentTypeId, $articleId) {
		
		$html        = '';
		$contentType = $this->getDetailById($contentTypeId);
		$article     = \ArticleService::getDetailById($articleId);
		if (!empty($contentType['structure'])) {
			foreach ($contentType['structure'] as $field) {
				
				$value = in_array($field['type'], ['checkbox', 'images']) ? [] : '';
				if (isset($article['detail'][$field['name']])) $value = $article['detail'][$field['name']];
				
				$funcName = $field['type'].'FormElement';
				if (method_exists($this, $funcName)) $html .= $this->$funcName($field, $value);
			}
		}
		
		return $html;
	}
	
	/**
	 * 隐藏域表单
	 * @param array  $field
	 * @param string $value
	 * @author 李小同
	 * @date   2018-7-24 17:03:07
	 * @return string
	 */
	public function hiddenFormElement(array $field, $value = '') {
		
		$html = '<input type="hidden" name="'.$field['name'].'" value="'.$value.'" />';
		
		return $html;
	}
	
	/**
	 * 文本框表单
	 * @param array  $field
	 * @param string $value
	 * @author 李小同
	 * @date   2018-7-12 22:02:46
	 * @return string
	 */
	public function inputFormElement(array $field, $value = '') {
		
		$html = '<p>
					<span class="form_filed_row">'.$field['name_text'].'：</span>
					<input type="text" class="input-text radius form_value_row" placeholder="'.$field['value'].'" 
							name="'.$field['name'].'" value="'.$value.'" />
				</p>';
		
		return $html;
	}
	
	/**
	 * 数字表单
	 * @param array  $field
	 * @param string $value
	 * @author 李小同
	 * @date   2018-7-24 17:00:46
	 * @return string
	 */
	public function numberFormElement(array $field, $value = '') {
		
		$html = '<p>
					<span class="form_filed_row">'.$field['name_text'].'：</span>
					<input type="number" class="input-text radius form_value_row" placeholder="'.$field['value'].'" 
							name="'.$field['name'].'" value="'.$value.'" style="width: 200px;" />
				</p>';
		
		return $html;
	}
	
	/**
	 * 单选框表单
	 * @param array  $field
	 * @param string $value
	 * @author 李小同
	 * @date   2018-7-12 22:02:46
	 * @return string
	 */
	public function radioFormElement(array $field, $value = '') {
		
		$html   = '<p><span class="form_filed_row">'.$field['name_text'].'：</span>
		<span class="skin-minimal form_value_row">';
		$groups = explode(',', $field['value']);
		
		if ($field['name'] == 'status') {
			foreach ($groups as $group) {
				$text    = $group == '1' ? trans('common.enable') : trans('common.disable');
				$checked = $group == $value ? 'checked' : '';
				$html .= '<label>
							<span class="radio-box">
								<input type="radio" name="'.$field['name'].'" value="'.$group.'" '.$checked.' >'.$text.'
							</span>
						</label>';
			}
		} elseif (substr($field['name'], -7) == '_status') {
			foreach ($groups as $group) {
				$text    = $group == '1' ? trans('common.yes') : trans('common.no');
				$checked = $group == $value ? 'checked' : '';
				$html .= '<label>
							<span class="radio-box">
								<input type="radio" name="'.$field['name'].'" value="'.$group.'" '.$checked.' >'.$text.'
							</span>
						</label>';
			}
		} else {
			foreach ($groups as $group) {
				$checked = $group == $value ? 'checked' : '';
				$html .= '<label>
							<span class="radio-box">
								<input type="radio" name="'.$field['name'].'" value="'.$group.'" '.$checked.' >'.$group.'
							</span>
						</label>';
			}
		}
		$html .= '</span></p>
					<script>
						$(\'.skin-minimal input\').iCheck({
							checkboxClass : \'icheckbox-blue\',
							radioClass    : \'iradio-blue\',
							increaseArea  : \'20%\'
						});
					</script>';
		
		return $html;
	}
	
	/**
	 * 复选框表单
	 * @param array $field
	 * @param array $value
	 * @author 李小同
	 * @date   2018-7-12 22:33:34
	 * @return string
	 */
	public function checkboxFormElement(array $field, array $value = []) {
		
		$html   = '<p><span class="form_filed_row">'.$field['name_text'].'：</span>
		<span class="skin-minimal form_value_row">';
		$groups = explode(',', $field['value']);
		foreach ($groups as $group) {
			$checked = in_array($group, $value) ? 'checked' : '';
			$html .= '<span class="check-box">
						<label>
							<input type="checkbox" name="'.$field['name'].'[]" value="'.$group.'" '.$checked.' >'.$group.'
						</label>
					</span>';
		}
		$html .= '</span></p>
					<script>
						$(\'.skin-minimal input\').iCheck({
							checkboxClass : \'icheckbox-blue\',
							radioClass    : \'iradio-blue\',
							increaseArea  : \'20%\'
						});
					</script>';
		
		return $html;
	}
	
	/**
	 * 下拉框表单
	 * @param array  $field
	 * @param string $value
	 * @author 李小同
	 * @date   2018-7-12 22:39:55
	 * @return string
	 */
	public function selectFormElement(array $field, $value = '') {
		
		$html   = '<p><span class="form_filed_row">'.$field['name_text'].'：</span>
		<select name="'.$field['name'].'" class="select-box radius form_value_row"><option></option>';
		if (is_array($field['value'])) {
			$groups = $field['value'];
			$flagArr = true;
		} else {
			$groups = explode(',', $field['value']);
			$flagArr = false;
		}
		foreach ($groups as $key => $text) {
			$realValue = $flagArr ? $key : $text;
			$selected = $realValue == $value ? 'selected' : '';
			$html .= '<option value="'.$realValue.'" '.$selected.' >'.$text.'</option>';
		}
		$html .= '</select></p>';
		
		return $html;
	}
	
	/**
	 * 文本域表单
	 * @param array  $field
	 * @param string $value
	 * @author 李小同
	 * @date   2018-7-12 22:02:46
	 * @return string
	 */
	public function textareaFormElement(array $field, $value = '') {
		
		$html = '<p style="display: flex;">
					<span class="form_filed_row">'.$field['name_text'].'：</span>
					<textarea class="textarea radius form_value_row" name="'.$field['name'].'" 
							  placeholder="'.$field['value'].'">'.$value.'</textarea>
				</p>';
		
		return $html;
	}
	
	/**
	 * 富文本表单
	 * @param array  $field
	 * @param string $value
	 * @author 李小同
	 * @date   2018-7-14 09:49:24
	 * @return string
	 */
	public function richtextFormElement(array $field, $value = '') {
		
		$html = '<p style="display: flex;">
					<span class="form_filed_row">'.$field['name_text'].'：</span>
					<span class="form_value_row">
						<textarea class="richtext" id="richtext_'.$field['name'].'" name="'.$field['name'].'">'.$value.'</textarea>
					</span>
				</p>
				<script>UE.getEditor(\'richtext_'.$field['name'].'\')</script>';
		
		return $html;
	}
	
	/**
	 * 时间表单
	 * @param array  $field
	 * @param string $value
	 * @author 李小同
	 * @date   2018-7-13 15:24:32
	 * @return string
	 */
	public function datetimeFormElement(array $field, $value = '') {
		
		if (empty($field['value'])) $field['value'] = 'yyyy-MM-dd HH:mm:ss';
		$html = '<p>
					<span class="form_filed_row">'.$field['name_text'].'：</span>
					<input type="text" class="input-text radius form_value_row Wdate" name="'.$field['name'].'" value="'.$value.'"
					       placeholder="'.$field['value'].'" onfocus="WdatePicker({dateFmt:\''.$field['value'].'\',skin:\'whyGreen\'})" />
				</p>';
		
		return $html;
	}
	
	/**
	 * 单图
	 * @param array  $field
	 * @param string $value
	 * @author 李小同
	 * @date   2018-7-13 15:24:32
	 * @return string
	 */
	public function imageFormElement(array $field, $value = '') {
		
		$html = '<p class="J_image">
					<span class="form_filed_row">'.$field['name_text'].'：</span>
					<span class="btn-upload form-group">
						<input class="input-text upload-url radius" type="text" style="width: 500px" value="'.$value.'"   
							   name="uploadfile_'.$field['name'].'" readonly placeholder="'.$field['value'].'">
						<a href="javascript:void();" class="btn btn-primary radius">
							<i class="Hui-iconfont">&#xe642;</i> 浏览文件
						</a>
						<input type="file" name="'.$field['name'].'" class="input-file">
					</span>
					<span class="J_image_preview">';
		if ($value) $html .= '<img src="'.\URL::asset($value).'" />';
		
		$html .= '</span></p>';
		$html .= '<script>
					$(\'input[name="'.$field['name'].'"]\').change(function () {
				
						var files = this.files   // 获取input上传的图片数据;						
						$(this).parents(\'.J_image\').find(\'.J_image_preview\').html(\'\');
						for(var i = 0; i < files.length; i++) {
							var img = new Image(),						
								url = window.URL.createObjectURL(files[i]);
							img.src = url;
							$(this).parents(\'.J_image\').find(\'.J_image_preview\').html(img);
						}
					});
				</script>';
		
		return $html;
	}
	
	/**
	 * 多图
	 * @param array $field
	 * @param array $value
	 * @author 李小同
	 * @date   2018-7-15 08:22:53
	 * @return string
	 */
	public function imagesFormElement(array $field, $value = []) {
		
		$html = '<p class="J_image">
					<span class="form_filed_row">'.$field['name_text'].'：</span>
					<span class="btn-upload form-group">
						<input class="input-text upload-url radius" type="text" style="width: 500px" value="'.implode(',', $value).'"   
							   name="uploadfile_'.$field['name'].'" readonly placeholder="'.$field['value'].'">
						<a href="javascript:void();" class="btn btn-primary radius">
							<i class="Hui-iconfont">&#xe642;</i> 浏览文件
						</a>
						<input type="file" multiple name="'.$field['name'].'[]" class="input-file">
					</span>
					<span class="J_image_preview">';
		if ($value) {
			foreach ($value as $img) {
				$src = $img = \URL::asset($img);
				if (strpos($img, config('project.THUMB_PREFIX')) !== false) {
					$src = str_replace(config('project.THUMB_PREFIX'), '', $img);
				}
				$html .= '<img src="'.$img.'" onclick="javascript:window.open(\''.$src.'\')" />';
			}
		}
		
		$html .= '</span></p>';
		$html .= '<script>
					$(\'input[name="'.$field['name'].'[]"]\').change(function () {
				
						var files = this.files   // 获取input上传的图片数据;						
						$(this).parents(\'.J_image\').find(\'.J_image_preview\').html(\'\');
						for(var i = 0; i < files.length; i++) {
							var img = new Image(),						
								url = window.URL.createObjectURL(files[i]);
							img.src = url;
							$(\'.J_image_preview\').append(img);
						}
					});
				</script>';
		
		return $html;
	}
	
	/**
	 * 文章池
	 * @param array  $field
	 * @param string $value
	 * @author 李小同
	 * @date   2018-7-21 15:40:36
	 * @return string
	 */
	public function articlepondFormElement(array $field, $value = '') {
		
		$articleList = \ArticleService::getListForArticlePond(\SettingService::getValue('product_content_type'));
		
		$html = '<div>
					<div class="f-l" style="width: 50%;">
						<span class="form_filed_row" style="position: relative;top: -45px;">'.$field['name_text'].'：</span>
						<textarea class="textarea radius form_value_row" name="'.$field['name'].'" style="height: 200px;"
								  placeholder="'.$field['value'].'">'.$value.'</textarea>
					</div>';
		$html .= '<ul style="border: 1px solid #ccc;width: 40%;height: 200px; overflow-y: scroll;">';
		$html .= '<li style="border-bottom: 1px solid #ccc;background: #e2fcee;">
						<dl>
			                <strong class="text-c" style="display: inline-block;width: 15%;">ID</strong>
			                <strong class="text-c" style="display: inline-block;width: 60%;">'.trans('common.article_name').'</strong>
			                <strong class="text-c" style="display: inline-block;width: 20%;">'.trans('common.status').'/'.trans('common.time_status').'</strong>
		                </dl>
	                </li>';
		foreach ($articleList as $key => $item) {
			$html .= '<li style="'.($key % 2 ? '' : 'background: #eee;').'">
						<dl>
			                <span class="text-c" style="display: inline-block;width: 15%;">'.$item['id'].'</span>
			                <span class="text-c" style="display: inline-block;width: 60%;">'.$item['name'].'</span>
			                <span class="text-c" style="display: inline-block;width: 20%;">'.$item['status_icon'].' '.$item['time_status'].'</span>
		                </dl>
	                </li>';
		}
		if (count($articleList) >= \SettingService::getValue('article_pond_product_per_page')) {
			$html .= '<p style="background: #f1f8ff;color: #333;cursor: pointer;" class="J_show_more text-c" data_page="2">'.trans('common.show_more').'</p>
					</ul><p style="clear:both"></p></div>';
		}
		$html .= '<script>
					$(\'.J_show_more\').click(function() {
						var page = $(this).attr(\'data_page\'),
							_this = this;
						$.ajax({
							url: \''.route('showMoreArticle').'\',
							type: \'post\',
							data: {page:page},
							beforeSend: function () {layer.load(1)},
							success: function(data) {
								layer.close(layer.load());
								if (data) {
								    $(\'.J_show_more\').before(data);
								    $(_this).attr(\'data_page\', (parseInt(page) + 1))
								} else {
									layer.msg(\''.trans('common.no_more').'\', {time: 1000});
									$(_this).remove();
								}
							}
						});
					});
					</script>';
		
		return $html;
	}
	# endregion
	
}