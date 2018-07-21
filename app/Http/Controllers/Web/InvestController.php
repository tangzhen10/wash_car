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
		
		$contentTypeIds = [
			'nav'     => '20', # 首页栏目
			'product' => '21', # 理财产品
		];
		
		$filter     = [
			'content_type' => $contentTypeIds['nav'],
			'status'       => '1',
		];
		$navList    = \ArticleService::getArticleList($filter);
		$articleIds = [];
		foreach ($navList as $item) {
			$articleIdsGroup = explode(',', $item['detail']['article_list']['value']);
			$articleIds      = array_merge($articleIds, $articleIdsGroup);
		}
		$articleIds  = array_unique($articleIds);
		$filter      = [
			'article_id_arr' => $articleIds,
			'content_type'   => $contentTypeIds['product'],
			'status'         => '1',
		];
		$articleList = \ArticleService::getArticleList($filter);
		
		# 以article_id作为key
		$articleListWithKey = [];
		foreach ($articleList as $item) $articleListWithKey[$item['id']] = $item;
		
		$groups = [];
		foreach ($navList as $item) {
			
			$list            = ['title' => $item['name']];
			$articleIdsGroup = explode(',', $item['detail']['article_list']['value']);
			foreach ($articleIdsGroup as $articleId) {
				if (isset($articleListWithKey[$articleId])) $list['list'][] = $articleListWithKey[$articleId];
			}
			$groups[$item['id']] = $list;
		}
		sort($groups);
		$this->data['groups'] = $groups;
		
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
		
		$detail = \ArticleService::getDetailById($id);
		
		$detail['detail']['top_image'] = \URL::asset($detail['detail']['top_image']);
		
		$this->data['detail'] = $detail;
		
		return view('web/invest/detail', $this->data);
	}
}
