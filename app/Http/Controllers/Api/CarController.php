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
}
