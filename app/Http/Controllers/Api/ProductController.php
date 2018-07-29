<?php

namespace App\Http\Controllers\Api;

class ProductController extends BaseController {
	
	/**
	 * 清洗服务产品列表
	 * @author 李小同
	 * @date   2018-7-29 09:41:05
	 */
	public function washList() {
		
		$filter = ['content_type' => 24];
		$list   = \ArticleService::getArticleBaseInfo($filter);
		
		json_msg(['list' => $list]);
	}
	
	/**
	 * 清理服务详情
	 * @author 李小同
	 * @date   2018-7-29 12:00:15
	 */
	public function washDetail() {
		
		$id = \Request::input('id');
		$id = intval($id);
		
		$filter = [
			'content_type'   => 24,
			'article_id_arr' => [$id],
		];
		$rows   = \ArticleService::getArticleList($filter);
		if (count($rows)) {
			
			$rows[0]['detail']['price']['value'] .= '元/次';
			unset($rows[0]['sub_name']);
			# todo lxt 已售多少单，读数据库
			
			json_msg(['detail' => $rows[0]]);
		} else {
			json_msg(trans('error.illegal_param'), 40001);
		}
		
	}
}
