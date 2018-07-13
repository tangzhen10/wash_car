<?php

namespace App\Http\Controllers\Admin;

class ArticleController extends BaseController {
	
	const MODULE = 'article';
	
	public function articleList() {
		
		$list                     = $this->service->getList();
		$this->data['list']       = $list['list'];
		$this->data['pagination'] = $list['listPage'];
		$this->data['typeList']   = \ContentTypeService::getList();
		
		return view('admin/article/list', $this->data);
	}
	
	/**
	 * 增改表单所需相关数据
	 * @param array $detail
	 * @author 李小同
	 * @date   2018-7-5 14:55:15
	 * @return array
	 */
	public function assocDataForForm($detail = []) {
		
		$typeList = \ContentTypeService::getList();
		
		return compact('typeList');
	}
	
}
