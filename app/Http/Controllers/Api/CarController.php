<?php

namespace App\Http\Controllers\Api;

/**
 * 定制业务 - 车辆功能接口类
 * Class CarController
 * @package App\Http\Controllers\Api
 */
class CarController extends BaseController {
	
	/**
	 * 我的车辆
	 * @author 李小同
	 * @date   2018-7-28 10:54:31
	 */
	public function myCar() {
		
		$carList = \CarService::getMyCarList();
		
		json_msg(['list' => $carList]);
	}
	
	/**
	 * 用户保存车辆
	 * @author 李小同
	 * @date   2018-7-27 17:40:46
	 */
	public function saveCar() {
		
		$post = request_all();
		
		$carId = \CarService::saveCar($post);
		
		$this->render($carId);
	}
	
	/**
	 * 移除车辆
	 * @author 李小同
	 * @date   2018-7-28 11:24:03
	 */
	public function deleteCar() {
		
		$carId = \Request::input('car_id');
		
		$res = \CarService::deleteCar($carId);
		
		$this->render($res);
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
	 * @author 李小同
	 * @date   2018-7-25 14:56:14
	 */
	public function model() {
		
		$brandId = \Request::input('brand_id');
		$res     = \CarService::getModelsByBrandId($brandId);
		
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
		
		json_msg(['list' => $res]);
	}
	
	/**
	 * 车辆品牌
	 * 包含热门品牌和所有品牌
	 * @author 李小同
	 * @date   2018-7-23 16:02:53
	 */
	public function color() {
		
		$res = \CarService::colorList();
		
		json_msg(['list' => $res]);
	}
}
