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
	 * @param array $filter
	 * @author 李小同
	 * @date   2018-7-23 21:16:15
	 * @return mixed
	 */
	public function getBrandList(array $filter = []) {
		
		$fields   = ['id', 'name', 'logo', 'hot', 'first_letter', 'name_en', 'status'];
		$listPage = \DB::table('car_brand')->where('status', '!=', '-1');
		
		# 按名称
		if (!empty($filter['filter_name'])) {
			$listPage = $listPage->where(function ($query) use ($filter) {
				
				$query->where('name', 'LIKE', '%'.$filter['filter_name'].'%')
				      ->orWhere('name_en', 'LIKE', '%'.$filter['filter_name'].'%');
			});
		}
		
		# 筛选热门
		if (!empty($filter['filter_hot'])) $listPage = $listPage->where('hot', '>', '0');
		
		# 精确筛选
		$where = [];
		if (!empty($filter['filter_id'])) $where['id'] = $filter['filter_id'];
		if (!empty($filter['filter_first_letter'])) $where['first_letter'] = $filter['filter_first_letter'];
		
		$listPage = $listPage->where($where)
		                     ->select($fields)
		                     ->orderBy('first_letter', 'asc')
		                     ->orderByRaw('CONVERT(name USING gb2312) ASC')
		                     ->orderBy('hot', 'desc')
		                     ->paginate($filter['perPage']);
		$listArr  = json_decode(json_encode($listPage), 1);
		$total    = $listArr['total'];
		$list     = $listArr['data'];
		$this->addStatusText($list);
		
		return compact('list', 'listPage', 'total');
	}
	
	/**
	 * 获取品牌详情
	 * @param $id
	 * @author 李小同
	 * @date   2018-7-24 16:11:27
	 * @return array
	 */
	public function getBrandDetailById($id) {
		
		if ($id > 0) {
			
			$detail = \DB::table('car_brand')->where('id', $id)->first();
		} else {
			$detail = [
				'id'           => '0',
				'name'         => '',
				'logo'         => '',
				'hot'          => '0',
				'first_letter' => '',
				'status'       => '1',
				'name_en'      => '',
			];
		}
		
		return $detail;
	}
	
	/**
	 * 增改品牌数据
	 * @author 李小同
	 * @date   2018-7-25 17:15:49
	 * @return int 增改的品牌id
	 */
	public function handleBrandForm() {
		
		$post = request_all();
		
		# validation
		if (empty($post['name'])) {
			json_msg(trans('validation.required', ['attr' => trans('common.name')]), 40001);
		}
		if (empty($post['first_letter'])) {
			json_msg(trans('validation.required', ['attr' => trans('common.first_letter')]), 40001);
		}
		
		$brandId = $post['id'];
		$query   = \DB::table('car_brand');
		
		# 图片上传，无图保持不变
		if (empty($post['logo'])) {
			$post['logo'] = $post['uploadfile_logo'];
		} else {
			$post['logo'] = ToolService::uploadFiles(\Request::file('logo'));
		}
		unset($post['uploadfile_logo']);
		
		if (!$brandId) {
			
			# 无id新增
			$brandId = $query->insertGetId($post);
			
		} else {
			
			# 有id修改
			$where = ['id' => $brandId];
			unset($post['id']);
			$query->where($where)->update($post);
		}
		
		return $brandId;
	}
	
	/**
	 * 获取指定品牌下的车型列表
	 * @param array $filter
	 * @author 李小同
	 * @date   2018-7-24 22:53:46
	 * @return array
	 */
	public function getModelList(array $filter = []) {
		
		$fields   = [
			'a.id',
			'a.name',
			'a.status',
			'b.name AS brand_name',
			'b.status AS brand_status',
		];
		$listPage = \DB::table('car_model AS a')->join('car_brand AS b', 'b.id', '=', 'a.brand_id');
		
		# 过滤品牌
		if (!empty($filter['brand_id'])) $listPage = $listPage->where('a.brand_id', $filter['brand_id']);
		
		$listPage = $listPage->where('a.status', '!=', '-1')->select($fields)->paginate($filter['perPage']);
		$listArr  = json_decode(json_encode($listPage), 1);
		$total    = $listArr['total'];
		$list     = $listArr['data'];
		$this->addStatusText($list);
		
		return compact('list', 'listPage', 'total');
	}
	
	/**
	 * 获取车型详情
	 * @param $id
	 * @author 李小同
	 * @date   2018-7-25 17:58:28
	 * @return array
	 */
	public function getModelDetailById($id) {
		
		if ($id > 0) {
			
			$detail = \DB::table('car_model')->where('id', $id)->first();
		} else {
			$detail = [
				'id'       => '0',
				'name'     => '',
				'brand_id' => '0',
				'status'   => '1',
			];
		}
		
		return $detail;
	}
	
	/**
	 * 增改品牌数据
	 * @author 李小同
	 * @date   2018-7-25 17:15:49
	 * @return int 增改的品牌id
	 */
	public function handleModelForm() {
		
		$post = request_all();
		
		# validation
		if (empty($post['name'])) {
			json_msg(trans('validation.required', ['attr' => trans('common.name')]), 40001);
		}
		if (empty($post['brand_id'])) json_msg(trans('error.illegal_param'), 40001);
		
		$modelId = $post['id'];
		$query   = \DB::table('car_model');
		
		if (!$modelId) {
			
			# 无id新增
			$modelId = $query->insertGetId($post);
			
		} else {
			
			# 有id修改
			$where = ['id' => $modelId];
			unset($post['id']);
			$query->where($where)->update($post);
		}
		
		return $modelId;
	}
	
	/**
	 * 获取车牌省份列表
	 * @author 李小同
	 * @date   2018-7-26 16:19:00
	 * @return array
	 */
	public function getProvinceList() {
		
		$list = \DB::table('car_province')->get(['id', 'name', 'status'])->toArray();
		$this->addStatusText($list);
		
		return $list;
	}
	
	/**
	 * 获取颜色详情
	 * @param $id
	 * @author 李小同
	 * @date   2018-7-26 17:30:48
	 * @return array
	 */
	public function getColorDetailById($id) {
		
		if ($id > 0) {
			
			$detail = \DB::table('car_color')->where('id', $id)->first();
		} else {
			$detail = [
				'id'         => '0',
				'name'       => '',
				'color_code' => '',
				'status'     => '1',
			];
		}
		
		return $detail;
	}
	
	/**
	 * 获取车辆颜色列表
	 * @author 李小同
	 * @date   2018-7-26 16:36:32
	 * @return array
	 */
	public function getColorList() {
		
		$list = \DB::table('car_color')->get(['id', 'name', 'color_code', 'status'])->toArray();
		$this->addStatusText($list);
		
		return $list;
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
	
	/**
	 * 获取指定品牌下的车型
	 * @param $brandId
	 * @author 李小同
	 * @date   2018-7-25 14:55:35
	 * @return array
	 */
	public function getModelsByBrandId($brandId) {
		
		if (empty($brandId) || !is_numeric($brandId)) json_msg(trans('error.illegal_param'), 40001);
		
		$list = \DB::table('car_model')
		           ->where('brand_id', intval($brandId))
		           ->where('status', '1')
		           ->get(['id', 'name'])
		           ->toArray();
		return $list;
	}
	
	/**
	 * 获取车牌省份简称
	 * @author 李小同
	 * @date   2018-7-26 16:31:45
	 * @return array
	 */
	public function getProvince() {
		
		$list = \DB::table('car_province')->get(['id', 'name'])->toArray();
		
		return $list;
	}
	
	public function colorList() {
		
		$list = \DB::table('car_color')->get(['id', 'name'])->toArray();
		
		return $list;
	}
	
	# endregion
}