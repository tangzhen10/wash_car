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
	 * 获取车辆列表
	 * @param array $filter
	 * @author 李小同
	 * @date   2018-7-27 20:05:34
	 * @return array
	 */
	public function getCarList(array $filter = []) {
		
		$fields   = [
			'a.id',
			'a.user_id',
			'a.brand_id',
			'a.model_id',
			'b.nickname AS username',
			'c.name AS brand',
			'd.name AS model',
			'e.name AS province',
			'a.plate_number',
			'f.name AS color',
			'a.status',
		];
		$listPage = \DB::table('car AS a')
		               ->join('user AS b', 'b.user_id', '=', 'a.user_id')
		               ->leftJoin('car_brand AS c', 'c.id', '=', 'a.brand_id')
		               ->leftJoin('car_model AS d', 'd.id', '=', 'a.model_id')
		               ->leftJoin('car_province AS e', 'e.id', '=', 'a.province_id')
		               ->leftJoin('car_color AS f', 'f.id', '=', 'a.color_id')
		               ->where('a.status', '1')
		               ->select($fields);
		
		if (!empty($filter['filter_user_id'])) $listPage = $listPage->where('a.user_id', $filter['filter_user_id']);
		
		$listPage = $listPage->paginate($filter['perPage'])->appends($filter);
		$listArr  = json_decode(json_encode($listPage), 1);
		$total    = $listArr['total'];
		$list     = $listArr['data'];
		
		return compact('list', 'listPage', 'total');
	}
	
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
		                     ->paginate($filter['perPage'])
		                     ->appends($filter);
		$listArr  = json_decode(json_encode($listPage), 1);
		$total    = $listArr['total'];
		$list     = $listArr['data'];
		$this->addStatusText($list);
		
		return compact('list', 'listPage', 'total');
	}
	
	/**
	 * 获取所有的品牌
	 * @author 李小同
	 * @date   2018-7-29 22:39:29
	 * @return mixed
	 */
	public function getAllBrand() {
		
		$list = \DB::table('car_brand')
		           ->where('status', '1')
		           ->orderBy('first_letter', 'asc')
		           ->orderByRaw('CONVERT(name USING gb2312) ASC')
		           ->get(['id', 'name'])
		           ->toArray();
		return $list;
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
		
		$listPage = $listPage->where('a.status', '!=', '-1')
		                     ->select($fields)
		                     ->paginate($filter['perPage'])
		                     ->appends($filter);
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
			
			$detail = \DB::table('car_model AS a')
			             ->join('car_brand AS b', 'b.id', '=', 'a.brand_id')
			             ->where('a.id', $id)
			             ->first(['a.id', 'a.name', 'a.status', 'a.brand_id', 'b.name AS brand']);
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
		
		$list = \DB::table('car_province')->get(['id', 'name', 'status'])->where('status', '!=', '-1')->toArray();
		$this->addStatusText($list);
		
		return $list;
	}
	
	/**
	 * 获取车辆颜色列表
	 * @author 李小同
	 * @date   2018-7-26 16:36:32
	 * @return array
	 */
	public function getColorList() {
		
		$list = \DB::table('car_color')->get(['id', 'name', 'code', 'status'])->where('status', '!=', '-1')->toArray();
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
				'id'     => '0',
				'name'   => '',
				'code'   => '',
				'status' => '1',
			];
		}
		
		return $detail;
	}
	
	public function handleColorForm() {
		
		$post = request_all();
		
		# validation
		if (empty($post['name'])) {
			json_msg(trans('validation.required', ['attr' => trans('common.name')]), 40001);
		}
		if (empty($post['code'])) {
			json_msg(trans('validation.required', ['attr' => trans('common.color_code')]), 40001);
		}
		
		$colorId      = $post['id'];
		$post['code'] = strtoupper($post['code']);
		$query        = \DB::table('car_color');
		
		if (!$colorId) {
			
			# 无id新增
			$colorId = $query->insertGetId($post);
			
		} else {
			
			# 有id修改
			$where = ['id' => $colorId];
			unset($post['id']);
			$query->where($where)->update($post);
		}
		
		return $colorId;
	}
	# endregion
	
	# region 前台
	
	/**
	 * 获取我的列表
	 * @author 李小同
	 * @date   2018-7-28 10:50:20
	 * @return array
	 */
	public function getMyCarList() {
		
		$fields = [
			'a.id AS car_id',
			'c.name AS brand',
			'd.name AS model',
			'e.name AS province',
			'a.plate_number',
			'f.name AS color',
		];
		$list   = \DB::table('car AS a')
		             ->join('user AS b', 'b.user_id', '=', 'a.user_id')
		             ->leftJoin('car_brand AS c', 'c.id', '=', 'a.brand_id')
		             ->leftJoin('car_model AS d', 'd.id', '=', 'a.model_id')
		             ->leftJoin('car_province AS e', 'e.id', '=', 'a.province_id')
		             ->leftJoin('car_color AS f', 'f.id', '=', 'a.color_id')
		             ->where('a.user_id', $this->userId)
		             ->where('a.status', '1')
		             ->get($fields)
		             ->toArray();
		foreach ($list as &$item) {
			$item['plate'] = $item['province'].$item['plate_number'];
			unset($item['province'], $item['plate_number']);
		}
		unset($item);
		
		return $list;
	}
	
	/**
	 * 保存客户车辆
	 * @param $post
	 * @author 李小同
	 * @date   2018-7-26 23:25:04
	 * @return int 车辆id
	 */
	public function saveCar($post) {
		
		if (!empty($post['car_id'])) $this->_checkCar($post['car_id']);
		
		if (empty($post['brand_id'])) json_msg(trans('validation.required', ['attr' => trans('common.brand')]), 40001);
		if (empty($post['model_id'])) json_msg(trans('validation.required', ['attr' => trans('common.car_model')]), 40001);
		if (empty($post['province_id'])) json_msg(trans('validation.required', ['attr' => trans('common.province')]), 40001);
		if (empty($post['plate_number'])) {
			json_msg(trans('validation.required', ['attr' => trans('common.plate_number')]), 40001);
		} else {
			if (!preg_match(config('project.PATTERN.PLATE'), $post['plate_number'])) {
				json_msg(trans('validation.invalid', ['attr' => trans('common.plate_number')]), 40003);
			}
		}
		if (empty($post['color_id'])) json_msg(trans('validation.required', ['attr' => trans('common.color')]), 40001);
		
		# 车牌大写
		$post['plate_number'] = strtoupper($post['plate_number']);
		$post['user_id']      = $this->userId;
		
		if (empty($post['car_id'])) {
			
			$carId = \DB::table('car')->insertGetId($post);
		} else {
			$where = ['id' => $post['car_id']];
			unset($post['car_id']);
			\DB::table('car')->where($where)->update($post);
			$carId = $where['id'];
		}
		
		return $carId;
	}
	
	/**
	 * 用户删除车辆
	 * @param $carId
	 * @author 李小同
	 * @date   2018-7-28 11:16:03
	 * @return mixed
	 */
	public function deleteCar($carId) {
		
		$this->_checkCar($carId);
		
		$where = [
			'user_id' => $this->userId,
			'id'      => intval($carId),
		];
		$res   = \DB::table('car')->where($where)->update(['status' => '-1']);
		
		return $res;
	}
	
	/**
	 * 获取品牌分组
	 * @author 李小同
	 * @date   2018-7-23 21:58:39
	 * @return array
	 */
	public function getBrandGroup() {
		
		$cacheKey = config('cache.CAR.BRAND');
		$res      = redisGet($cacheKey);
		if (false === $res) {
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
			redisSet($cacheKey, $res);
		}
		
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
		
		$cacheKey = config('cache.CAR.PROVINCE');
		$list     = redisGet($cacheKey);
		if (false === $list) {
			$list = \DB::table('car_province')->get(['id', 'name'])->toArray();
			redisSet($cacheKey, $list);
		}
		
		return $list;
	}
	
	/**
	 * 获取车辆颜色列表
	 * @author 李小同
	 * @date   2018-7-28 11:56:02
	 * @return bool|mixed
	 */
	public function colorList() {
		
		$cacheKey = config('cache.CAR.COLOR');
		$list     = redisGet($cacheKey);
		if (false === $list) {
			$fields = ['id', 'name', 'code'];
			$list   = \DB::table('car_color')
			             ->where('status', '1')
			             ->orderBy('id', 'desc')
			             ->limit(12)
			             ->get($fields)
			             ->toArray();
			foreach ($list as &$item) {
				if ($item['id']) $item['code'] = '#'.$item['code'];
			}
			unset($item);
			redisSet($cacheKey, $list);
		}
		
		return $list;
	}
	
	/**
	 * 检测车辆是否属于当前用户
	 * @param $carId
	 * @author 李小同
	 * @date   2018-7-28 11:27:42
	 */
	private function _checkCar($carId) {
		
		$where = [
			'user_id' => $this->userId,
			'id'      => intval($carId),
			'status'  => '1',
		];
		$count = \DB::table('car')->where($where)->count('id');
		
		if ($count == 0) json_msg(trans('error.not_your_car'), 40003);
	}
	
	# endregion
}