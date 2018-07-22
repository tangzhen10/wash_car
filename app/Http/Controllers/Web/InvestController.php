<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InvestController extends Controller {
	
	public $data = [];
	
	/**
	 * 理财产品列表
	 * @param int $id 版块id，如我要理财，我要投资
	 * @author 李小同
	 * @date   2018-7-15 11:47:24
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function investList($id = 0) {
		
		# 检测$id是否是产品分类的content_type
		$where       = ['id' => $id, 'status' => '1'];
		$contentType = \DB::table('content_type')->where($where)->first(['name', 'type']);
		if ($contentType['type'] == '1') {
			
			$this->data['pageTitle'] = $contentType['name'];
			
			$filter  = ['content_type' => $id, 'status' => '1'];
			$navList = \ArticleService::getArticleList($filter);
			
			$groups = [];
			if (!empty($navList)) {
				
				$articleIds = [];
				foreach ($navList as $item) {
					$articleIdsGroup = explode(',', $item['detail']['article_list']['value']);
					$articleIds      = array_merge($articleIds, $articleIdsGroup);
				}
				$articleIds  = array_unique($articleIds);
				$filter      = [
					'article_id_arr' => $articleIds,
					'content_type'   => env('ARTICLE_PRODUCT_CONTENT_TYPE'),
					'status'         => '1',
				];
				$articleList = \ArticleService::getArticleList($filter);
				
				# 以article_id作为key
				$articleListWithKey = [];
				foreach ($articleList as $item) {
					unset($item['content_type'], $item['create_at'], $item['create_by'], $item['update_at'], $item['update_by'], $item['start_time'], $item['end_time']);
					$articleListWithKey[$item['id']] = $item;
				}
				
				foreach ($navList as $item) {
					
					$list            = ['title' => $item['name']];
					$articleIdsGroup = explode(',', $item['detail']['article_list']['value']);
					foreach ($articleIdsGroup as $articleId) {
						if (isset($articleListWithKey[$articleId])) $list['list'][] = $articleListWithKey[$articleId];
					}
					$groups[$item['id']] = $list;
				}
			}
			$this->data['groups'] = $groups;
			
			return view('web/invest/list', $this->data);
		} else {
			json_msg(trans('error.illegal_param'), 40003);
		}
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
