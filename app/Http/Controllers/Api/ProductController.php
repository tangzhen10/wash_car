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
		
		$filter = ['content_type' => self::CONTENT_TYPE];
		$list   = \ArticleService::getArticleList($filter);
		foreach ($list as &$item) {
			$item['price']     = $item['detail']['price'];
			$item['price_ori'] = $item['detail']['price_ori'];
			$item['discount']  = round(10 * $item['price']['value'] / $item['price_ori']['value'], 1).'折';
			unset($item['sub_name'], $item['detail']);
		}
		unset($item);
		
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
				
				$detail                                 = $rows[0];
				$detail['detail']['price']['value']     = currencyFormat($detail['detail']['price']['value']);
				$detail['detail']['price_ori']['value'] = currencyFormat($detail['detail']['price_ori']['value']);
				unset($detail['sub_name']);
				
				# 已售多少单，读数据库
				$saleCountCacheKey = sprintf(config('cache.ORDER.PRODUCT_SALE_COUNT'), $id);
				$saleCount         = redisGet($saleCountCacheKey);
				if (false === $saleCount) {
					$saleCount = \OrderService::getSaleCount($id);
					redisSet($saleCountCacheKey, $saleCount);
				}
				$detail['detail']['sale_count'] = [
					'text'  => sprintf(trans('common.sale_count'), $saleCount),
					'value' => $saleCount,
				];
				
			} else {
				json_msg(trans('error.illegal_param'), 40001);
			}
		}
		
		json_msg($detail);
	}
}
