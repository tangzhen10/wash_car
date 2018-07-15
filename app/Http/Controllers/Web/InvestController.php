<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InvestController extends Controller {
	
	public $data = [];
	
	/**
	 * 理财产品列表
	 * @author 李小同
	 * @date   2018-7-15 11:47:24
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function investList() {
		
		$filter       = [
			'content_type' => '21',
			'status'       => 1,
		];
		$articleList  = \ArticleService::getArticleList($filter);
		$articleGroup = [];
		foreach ($articleList as $article) {
			$module = $article['detail']['module'];
			$articleGroup[$module['value']]['group_title'] = $module['value'];
			$articleGroup[$module['value']]['list'][]      = $article;
		}
		sort($articleGroup);
		$this->data['article_group'] = $articleGroup;
		
		return view('web/invest/list', $this->data);
	}
	
	/**
	 * 理财产品详情
	 * @param int $id 产品id
	 * @author 李小同
	 * @date   2018-7-15 12:55:06
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function detail($id = 0) {
		
		$detail                        = \ArticleService::getDetailById($id);
		$detail['detail']['top_image'] = \URL::asset($detail['detail']['top_image']);
		$this->data['detail']          = $detail;
		
		return view('web/invest/detail', $this->data);
	}
}
