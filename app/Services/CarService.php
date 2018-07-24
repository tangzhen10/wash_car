<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-07-22 22:11
 */

namespace App\Services;

class CarService extends BaseService {
	
	# region 后台
	/**
	 * 获取品牌列表
	 * @author 李小同
	 * @date   2018-7-23 21:16:15
	 * @return mixed
	 */
	public function getBrandList() {
		
		$list = \DB::table('car_brand')->where('status', '!=', '-1')->orderBy('first_letter', 'asc')->get()->toArray();
		$this->addStatusText($list);
		
		return $list;
	}
	
	/**
	 * 修改品牌状态
	 * @param $brandId
	 * @param $status
	 * @author 李小同
	 * @date   2018-7-24 15:28:56
	 * @return mixed
	 */
	public function brandChangeStatus($brandId, $status) {
		
		$brandId = intval($brandId);
		if (!in_array($status, ['1', '0', '-1'])) json_msg(trans('error.illegal_param'), 40001);
		
		$res = \DB::table('car_brand')->where('id', $brandId)->update(['status' => $status]);
		
		return $res;
	}
	# endregion
	
	# region 前台
	/**
	 * 获取品牌分组
	 * @author 李小同
	 * @date   2018-7-23 21:58:39
	 * @return array
	 */
	public function getBrandGroup() {
		
		$rows = \DB::table('car_brand')
		           ->where('status', '1')
		           ->orderBy('hot', 'desc')
		           ->orderByRaw('CONVERT(name USING gb2312) ASC')
		           ->get()
		           ->toArray();
		$hot  = [];
		$list = [];
		foreach ($rows as $row) {
			$row['logo'] = \URL::asset($row['logo']);
			unset($row['status'], $row['status_text'], $row['name_en']);
			if ($row['hot']) $hot[] = $row;
			if (empty($row['first_letter'])) $row['first_letter'] = '#';
			$list[$row['first_letter']][] = $row;
		}
		$hot = array_slice($hot, 0, 10);
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
		
		return $res;
	}
	
	# endregion
}