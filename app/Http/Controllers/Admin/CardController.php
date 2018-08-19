<?php

namespace App\Http\Controllers\Admin;

/**
 * 卡券
 * Class CardController
 * @package App\Http\Controllers\Admin
 */
class CardController extends BaseController {
	
	const MODULE = 'card';
	
	/**
	 * 卡券列表
	 * @author 李小同
	 * @date   2018-08-19 14:52:55
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function cardList() {
		
		$cardList = $this->service->getList();
		
		$this->data['list'] = $cardList;
		
		return view('admin/card/list', $this->data);
	}
	
	/**
	 * 增改表单所需相关数据
	 * @param array $detail
	 * @author 李小同
	 * @date   2018-08-19 14:53:06
	 * @return array
	 */
	public function assocDataForForm($detail = []) {
		
		# 服务项目列表
		$filter             = [
			'content_type' => \SettingService::getValue('product_content_type'),
			'status'       => '1',
		];
		$washProductListArr = \ArticleService::getArticleBaseInfo($filter);
		$washProductList    = [];
		foreach ($washProductListArr as $item) {
			$washProductList[$item['id']] = $item['name'];
		}
		
		# 表单
		$structure = [
			[
				'name_text' => '',
				'type'      => 'hidden',
				'name'      => 'id',
				'value'     => '',
			],
			[
				'name_text' => trans('common.name'),
				'type'      => 'input',
				'name'      => 'name',
				'value'     => '',
			],
			[
				'name_text' => trans('common.wash_product'),
				'type'      => 'select',
				'name'      => 'wash_product_id',
				'value'     => $washProductList,
			],
			[
				'name_text' => trans('common.price'),
				'type'      => 'number',
				'name'      => 'price',
				'value'     => '0.00',
			],
			[
				'name_text' => trans('common.price_ori'),
				'type'      => 'number',
				'name'      => 'price_ori',
				'value'     => '0.00',
			],
			[
				'name_text' => trans('common.expire_date'),
				'type'      => 'number',
				'name'      => 'expire_date',
				'value'     => '',
			],
			[
				'name_text' => trans('common.use_times'),
				'type'      => 'number',
				'name'      => 'use_times',
				'value'     => '',
			],
			[
				'name_text' => trans('common.hot_sale'),
				'type'      => 'radio',
				'name'      => 'hot_status',
				'value'     => '1,0',
			],
			[
				'name_text' => trans('common.introduction'),
				'type'      => 'textarea',
				'name'      => 'introduction',
				'value'     => '',
			],
			[
				'name_text' => trans('common.background'),
				'type'      => 'image',
				'name'      => 'background',
				'value'     => '',
			],
		];
		$html      = $this->service->getFormHtmlByStructure($structure, $detail);
		
		return compact('html', 'washProductList');
	}
}
