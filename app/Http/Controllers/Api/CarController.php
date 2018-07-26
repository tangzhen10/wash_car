<?php

namespace App\Http\Controllers\Api;

/**
 * 车辆功能接口类
 * Class CarController
 * @package App\Http\Controllers\Api
 */
class CarController extends BaseController {
	
	public function saveCar() {
		
		$post = request_all();
		
		$post['user_id'] = $this->user->userId;
		
		$carId = \CarService::saveCar($post);
		
		json_msg($carId);
	}
	
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
	
	/**
	 * 车牌省份简称
	 * @author 李小同
	 * @date   2018-7-26 17:04:11
	 */
	public function province() {
		
		$res = \CarService::getProvince();
		
		json_msg($res);
	}
	
	/**
	 * 车辆品牌
	 * 包含热门品牌和所有品牌
	 * @author 李小同
	 * @date   2018-7-23 16:02:53
	 */
	public function color() {
		
		$res = \CarService::colorList();
		
		json_msg($res);
	}
}
