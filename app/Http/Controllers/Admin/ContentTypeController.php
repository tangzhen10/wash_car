<?php

namespace App\Http\Controllers\Admin;

class ContentTypeController extends BaseController {
	
	const MODULE = 'content_type';
	
	/**
	 * 文档类型列表
	 * @author 李小同
	 * @date   2018-7-11 15:46:36
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function typeList() {
		
		$this->data['typeList'] = $this->service->getList();
		return view('admin/content_type/list', $this->data);
	}
	
	/**
	 * 增改表单所需相关数据
	 * @param array $detail
	 * @author 李小同
	 * @date   2018-7-11 17:13:02
	 * @return array
	 */
	public function assocDataForForm($detail = []) {
		
		# 自定义的表单类型
		$formElements = $this->service->getFormElements();
		$fields       = $this->service->getArticleBaseFields();
		$keyFields    = implode(',', $fields);
		
		return compact('formElements', 'keyFields');
	}
	
	/**
	 * 文档类型的表单html，用于编辑文章时的私有属性部分
	 * @author 李小同
	 * @date   2018-7-12 18:16:32
	 */
	public function formHtml() {
		
		$id        = \Request::input('content_type', 0);
		$articleId = \Request::input('article_id', 0);
		
		if (empty($id)) die;
		
		$formHtml = $this->service->getFormHtml($id, $articleId);
		
		echo $formHtml;
		die;
	}
	
}
