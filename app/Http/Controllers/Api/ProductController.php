<?php

namespace App\Http\Controllers\Api;

/**
 * 定制业务 - 产品类
 * Class ProductController
 * @package App\Http\Controllers\Api
 */
class ProductController extends BaseController {
	
	const CONTENT_TYPE = 24;
	
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
		
		$id = intval(\Request::input('id'));
		
		$cacheKey = sprintf(config('cache.ARTICLE.DETAIL'), $id);
		$detail   = redisGet($cacheKey);
		if (false === $detail) {
			$filter = [
				'content_type'   => self::CONTENT_TYPE,
				'article_id_arr' => [$id],
			];
			$rows   = \ArticleService::getArticleList($filter);
			if (count($rows)) {
				
				$rows[0]['detail']['price']['value']     = currencyFormat($rows[0]['detail']['price']['value']);
				$rows[0]['detail']['price_ori']['value'] = currencyFormat($rows[0]['detail']['price_ori']['value']);
				unset($rows[0]['sub_name']);
				$detail = $rows[0];
				
				# todo lxt 已售多少单，读数据库
				
				
			} else {
				json_msg(trans('error.illegal_param'), 40001);
			}
		}
		
		json_msg($detail);
	}
}
