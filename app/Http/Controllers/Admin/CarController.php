<?php

namespace App\Http\Controllers\Admin;

class CarController extends BaseController {
	
	const MODULE = 'car';
	
	/**
	 * 客户车辆列表
	 * @author 李小同
	 * @date   2018-7-26 23:11:13
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function carList() {
		
		$filter = [
			'filter_id'      => \Request::input('filter_id'),
			'filter_user_id' => \Request::input('filter_user_id'),
			'filter_name'    => \Request::input('filter_name'),
			'perPage'        => $this->getPerPage(),
		];
		$list   = $this->service->getCarList($filter);
		
		foreach ($list['list'] as &$item) {
			$item['plate'] = $item['province'].substr($item['plate_number'], 0, 1).'·'.substr($item['plate_number'], 1);
		}
		unset($item);
		
		$this->data['list']       = $list['list'];
		$this->data['pagination'] = $list['listPage'];
		$this->data['total']      = $list['total'];
		$this->data['filter']     = $filter;
		
		return view('admin/car/car/list', $this->data);
	}
	
	/**
	 * 车牌省份简称
	 * @author 李小同
	 * @date   2018-7-26 16:21:29
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function provinceList() {
		
		$this->data['list'] = $this->service->getProvinceList();
		
		return view('admin/car/province/list', $this->data);
	}
	
	# region 品牌brand
	/**
	 * 品牌列表
	 * @author 李小同
	 * @date   2018-7-24 15:23:41
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function brandList() {
		
		$filter = [
			'filter_id'           => \Request::input('filter_id'),
			'filter_first_letter' => \Request::input('filter_first_letter'),
			'filter_name'         => \Request::input('filter_name'),
			'filter_hot'          => \Request::input('filter_hot'),
			'perPage'             => $this->getPerPage(),
		];
		$list   = $this->service->getBrandList($filter);
		
		$this->data['list']       = $list['list'];
		$this->data['pagination'] = $list['listPage'];
		$this->data['total']      = $list['total'];
		$this->data['filter']     = $filter;
		
		return view('admin/car/brand/list', $this->data);
	}
	
	/**
	 * 增改品牌
	 * @param int $id
	 * @author 李小同
	 * @date   2018-7-24 16:04:35
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function brandForm($id = 0) {
		
		if (\Request::getMethod() == 'POST') {
			
			$brandId = $this->service->handleBrandForm();
			if ($brandId) redisDel(config('cache.CAR.BRAND'));
			$this->render($brandId);
			
		} else {
			
			$structure = [
				[
					'name_text' => '',
					'type'      => 'hidden',
					'name'      => 'id',
					'value'     => '',
				],
				[
					'name_text' => trans('common.name'),
					'type'      => 'input',
					'name'      => 'name',
					'value'     => '',
				],
				[
					'name_text' => trans('common.name_en'),
					'type'      => 'input',
					'name'      => 'name_en',
					'value'     => '',
				],
				[
					'name_text' => trans('common.logo'),
					'type'      => 'image',
					'name'      => 'logo',
					'value'     => '',
				],
				[
					'name_text' => trans('common.first_letter'),
					'type'      => 'select',
					'name'      => 'first_letter',
					'value'     => config('project.FIRST_LETTER'),
				],
				[
					'name_text' => trans('common.hot_value'),
					'type'      => 'number',
					'name'      => 'hot',
					'value'     => trans('common.int'),
				],
				[
					'name_text' => trans('common.status'),
					'type'      => 'radio',
					'name'      => 'status',
					'value'     => '1,0',
				],
			];
			$detail    = $this->service->getBrandDetailById($id);
			
			$this->data['html'] = $this->getFormHtml($detail, $structure);
			
			return view('admin/car/brand/form', $this->data);
		}
	}
	
	/**
	 * 修改品牌状态
	 * @author 李小同
	 * @date   2018-7-24 15:30:22
	 */
	public function brandChangeStatus() {
		
		$brandId = \Request::input('id');
		$status  = \Request::input('status');
		$res     = $this->service->easyChangeStatus('car_brand', $brandId, $status);
		if ($res) redisDel(config('cache.CAR.BRAND'));
		$this->render($res);
	}
	# endregion
	
	# region 车型model
	/**
	 * 车型列表
	 * @param int $brandId 品牌id
	 * @author 李小同
	 * @date   2018-7-25 17:52:58
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function modelList($brandId = 0) {
		
		$filter = [
			'brand_id' => $brandId,
			'perPage'  => $this->getPerPage(),
		];
		$list   = $this->service->getModelList($filter);
		
		$this->data['list']       = $list['list'];
		$this->data['pagination'] = $list['listPage'];
		$this->data['total']      = $list['total'];
		$this->data['filter']     = $filter;
		
		return view('admin/car/model/list', $this->data);
	}
	
	/**
	 * 增改车型
	 * @param int $id
	 * @author 李小同
	 * @date   2018-7-25 17:59:27
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function modelForm($id = 0) {
		
		if (\Request::getMethod() == 'POST') {
			
			$modelId = $this->service->handleModelForm();
			$this->render($modelId);
			
		} else {
			
			$brandId = \Request::input('brand_id');
			$brand   = $this->service->getBrandDetailById($brandId);
			if (empty($brand)) json_msg(trans('error.illegal_param'), 40001);
			
			$structure          = [
				[
					'name_text' => '',
					'type'      => 'hidden',
					'name'      => 'id',
					'value'     => '',
				],
				[
					'name_text' => trans('common.name'),
					'type'      => 'input',
					'name'      => 'name',
					'value'     => '',
				],
				[
					'name_text' => '',
					'type'      => 'hidden',
					'name'      => 'brand_id',
					'value'     => '',
				],
				[
					'name_text' => trans('common.status'),
					'type'      => 'radio',
					'name'      => 'status',
					'value'     => '1,0',
				],
			];
			$detail             = $this->service->getModelDetailById($id, $brandId);
			$detail['brand_id'] = $brandId; # 新增型号，品牌读取当前页面的品牌值
			
			$this->data['html']  = $this->getFormHtml($detail, $structure);
			$this->data['brand'] = $brand;
			
			return view('admin/car/model/form', $this->data);
		}
	}
	
	/**
	 * 修改车型状态
	 * @author 李小同
	 * @date   2018-7-25 18:03:06
	 */
	public function modelChangeStatus() {
		
		$modelId = \Request::input('id');
		$status  = \Request::input('status');
		$res     = $this->service->easyChangeStatus('car_model', $modelId, $status);
		$this->render($res);
	}
	
	# endregion
	
	# region 颜色color
	/**
	 * 获取车辆颜色列表
	 * @author 李小同
	 * @date   2018-7-26 16:37:15
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function colorList() {
		
		$this->data['list'] = $this->service->getColorList();
		
		return view('admin/car/color/list', $this->data);
	}
	
	/**
	 * 增改颜色
	 * @param int $id
	 * @author 李小同
	 * @date   2018-7-26 17:14:33
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function colorForm($id = 0) {
		
		if (\Request::getMethod() == 'POST') {
			
			$colorId = $this->service->handleColorForm();
			if ($colorId) redisDel(config('cache.CAR.COLOR'));
			$this->render($colorId);
			
		} else {
			
			$structure = [
				[
					'name_text' => '',
					'type'      => 'hidden',
					'name'      => 'id',
					'value'     => '',
				],
				[
					'name_text' => trans('common.name'),
					'type'      => 'input',
					'name'      => 'name',
					'value'     => '',
				],
				[
					'name_text' => trans('common.color_code'),
					'type'      => 'input',
					'name'      => 'code',
					'value'     => '',
				],
				[
					'name_text' => trans('common.status'),
					'type'      => 'radio',
					'name'      => 'status',
					'value'     => '1,0',
				],
			];
			$detail    = $this->service->getColorDetailById($id);
			
			$this->data['html']   = $this->getFormHtml($detail, $structure);
			$this->data['detail'] = $detail;
			
			return view('admin/car/color/form', $this->data);
		}
	}
	
	/**
	 * 修改颜色状态
	 * @author 李小同
	 * @date   2018-7-26 17:13:47
	 */
	public function colorChangeStatus() {
		
		$colorId = \Request::input('id');
		$status  = \Request::input('status');
		$res     = $this->service->easyChangeStatus('car_color', $colorId, $status);
		if ($res) redisDel(config('cache.CAR.COLOR'));
		$this->render($res);
	}
	# endregion
}
