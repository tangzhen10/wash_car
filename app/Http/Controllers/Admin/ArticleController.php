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
	
	# region 理财产品定制内容
	/**
	 * 产品分类
	 * @param int $id 产品分类对应的content_type
	 * @author 李小同
	 * @date   2018-7-21 22:21:59
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function productCategory($id = 0) {
		
		$filter                 = ['filter_content_type' => $id];
		$this->data['typeList'] = \ContentTypeService::getList(['type' => '1']);
		if (empty($id)) {
			$filter['filter_content_type'] = $this->data['typeList'][0]['id'];
		}
		$list                     = $this->service->getList($filter);
		$this->data['list']       = $list['list'];
		$this->data['pagination'] = $list['listPage'];
		$this->data['total']      = $list['total'];
		$this->data += $filter;
		
		return view('admin/invest/category_list', $this->data);
	}
	
	/**
	 * 产品列表
	 * @author 李小同
	 * @date   2018-7-21 22:21:59
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function productList() {
		
		$filter                   = [
			'filter_content_type' => 21,
		];
		$list                     = $this->service->getList($filter);
		$this->data['list']       = $list['list'];
		$this->data['pagination'] = $list['listPage'];
		$this->data['total']      = $list['total'];
		$this->data['typeList']   = \ContentTypeService::getList(['type' => '1']);
		$this->data += $filter;
		
		return view('admin/invest/product_list', $this->data);
	}
	
	/**
	 * 文章池产品列表显示更多
	 * @author 李小同
	 * @date   2018-7-22 08:46:23
	 */
	public function showMore() {
		
		$page        = \Request::input('page');
		$contentType = \SettingService::getValue('product_content_type');
		$list        = $this->service->getListForArticlePond($contentType, $page);
		$html        = '';
		foreach ($list as $key => $item) {
			$icon = $item['status'] ? '<i class="Hui-iconfont c-success">&#xe6a8;</i>' : '<i class="Hui-iconfont c-danger">&#xe706;</i>';
			$html .= '<li style="'.($key % 2 ? '' : 'background: #eee;').'">
						<dl>
			                <span class="text-c" style="display: inline-block;width: 15%;">'.$item['id'].'</span>
			                <span class="text-c" style="display: inline-block;width: 60%;">'.$item['name'].'</span>
			                <span class="text-c" style="display: inline-block;width: 20%;">'.$icon.$item['time_status'].'</span>
		                </dl>
	                </li>';
		}
		echo $html;
	}
	
	# endregion
}
