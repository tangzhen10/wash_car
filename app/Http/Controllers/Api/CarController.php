<?php

namespace App\Http\Controllers\Api;

class CarController extends BaseController {
	
	/**
	 * 车辆品牌
	 * 包含热门品牌和所有品牌
	 * @author 李小同
	 * @date   2018-7-23 16:02:53
	 */
	public function brand() {
		
		$res = \CarService::getBrandGroup();
		
		json_msg($res);
	}
	
	/**
	 * 指定品牌下的车型列表
	 * @param $brandId
	 * @author 李小同
	 * @date   2018-7-25 14:56:14
	 */
	public function model($brandId) {
		
		$res = \CarService::getModelsByBrandId($brandId);
		
		if (empty($res)) $res = [
			[
				'id'   => 0,
				'name' => trans('common.no_data'),
			],
		];
		
		json_msg(['list' => $res]);
	}
}
