<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-07-22 22:11
 */

namespace App\Services;

class CarService extends BaseService {
	
	public function getBrandList() {
		
		$list = \DB::table('car_brand')->where('status', '!=', '-1')->get()->toArray();
		$this->addStatusText($list);
		
		return $list;
	}
}