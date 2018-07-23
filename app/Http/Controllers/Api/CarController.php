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
		
		$rows = \CarService::getBrandList();
		
		$hot  = [];
		$list = [];
		foreach ($rows as $row) {
			$row['logo'] = \URL::asset($row['logo']);
			unset($row['status'], $row['status_text'], $row['name_en']);
			if ($row['hot']) $hot[] = $row;
			$list[$row['first_letter']][] = $row;
		}
		ksort($list);
		
		$groups = [];
		foreach ($list as $key => $item) {
			$groups[] = [
				'title' => $key,
				'list'  => $item,
			];
		}
		$res = [
			'hot' => $hot,
			'all' => $groups,
		];
		json_msg($res);
	}
}
