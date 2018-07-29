<?php

namespace App\Http\Controllers\Api;

/**
 * 车辆功能接口类
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
	
	/**
	 * 预约时间段
	 * @author 李小同
	 * @date   2018-7-28 21:21:52
	 * @return array
	 */
	public function washTime() {
		
		$todayText    = trans('common.today');
		$tomorrowText = trans('common.tomorrow');
		$timeList     = [
			date('Y-m-d 00:00:00')                         => $todayText.' 00:00-01:00',
			date('Y-m-d 01:00:00')                         => $todayText.' 01:00-02:00',
			date('Y-m-d 21:00:00')                         => $todayText.' 21:00-22:00',
			date('Y-m-d 22:00:00')                         => $todayText.' 22:00-23:00',
			date('Y-m-d 23:00:00')                         => $todayText.' 23:00-24:00',
			date('Y-m-d', strtotime('+1 day')).' 00:00:00' => $tomorrowText.' 00:00-01:00',
			date('Y-m-d', strtotime('+1 day')).' 01:00:00' => $tomorrowText.' 01:00-02:00',
		];
		
		$now      = date('Y-m-d H:i:s');
		$am2clock = date('Y-m-d 02:00:00'); # 今天2点
		
		foreach ($timeList as $key => $item) {
			
			if ($now < $am2clock && $am2clock < $key) {
				unset($timeList[$key]);
			}
			if ($now > $key) {
				unset($timeList[$key]);
			}
		}
		
		json_msg(['list' => array_values($timeList)]);
	}
}
