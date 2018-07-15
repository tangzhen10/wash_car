<?php

namespace App\Http\Controllers\Admin;

class ArticleController extends BaseController {
	
	const MODULE = 'article';
	
	/**
	 * 文章列表
	 * @author 李小同
	 * @date   2018-7-14 09:03:12
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function articleList() {
		
		$filter                   = [
			'filter_content_type' => \Request::input('filter_content_type', ''),
			'filter_article_name' => \Request::input('filter_article_name', ''),
		];
		$list                     = $this->service->getList($filter);
		$this->data['list']       = $list['list'];
		$this->data['pagination'] = $list['listPage'];
		$this->data['total']      = $list['total'];
		$this->data['typeList']   = \ContentTypeService::getList();
		$this->data += $filter;
		
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
	
	public function uploadFile() {
		
		$data = request_all();
		print_r($data);
	}
}
