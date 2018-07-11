<?php

namespace App\Http\Controllers\Admin;

class ArticleController extends BaseController {
	
	const MODULE = 'article';
	
	public function articleList() {
		
		$this->data['typeList'] = \ContentTypeService::getTypeList();
		
		return view('admin/article/list', $this->data);
	}
	
	
}
