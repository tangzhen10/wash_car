<?php

namespace App\Http\Controllers\Admin;

use App\Services\ContentTypeService;

class CarController extends BaseController {
	
	const MODULE = 'car';
	
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
			'perPage'             => \Request::input('perPage', \SettingService::getValue('per_page')),
		];
		$list   = $this->service->getBrandList($filter);
		
		$this->data['list']       = $list['list'];
		$this->data['pagination'] = $list['listPage'];
		$this->data['total']      = $list['total'];
		$this->data['filter']     = $filter;
		
		return view('admin/car/brand/list', $this->data);
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
	 * 增改品牌
	 * @param int $id
	 * @author 李小同
	 * @date   2018-7-24 16:04:35
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function brandForm($id = 0) {
		
		if (\Request::getMethod() == 'POST') {
			
			$brandId = $this->service->handleBrandForm();
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
			$detail    = $this->getBrandDetailById($id);
			
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
		$res     = $this->service->brandChangeStatus($brandId, $status);
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
			'perPage'  => \Request::input('perPage', \SettingService::getValue('per_page')),
		];
		$list   = $this->service->getModelList($filter);
		
		$this->data['list']       = $list['list'];
		$this->data['pagination'] = $list['listPage'];
		$this->data['total']      = $list['total'];
		$this->data['filter']     = $filter;
		
		return view('admin/car/model/list', $this->data);
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
			$brand   = $this->getBrandDetailById($brandId);
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
					'value'     => $brandId,
				],
				[
					'name_text' => trans('common.status'),
					'type'      => 'radio',
					'name'      => 'status',
					'value'     => '1,0',
				],
			];
			$detail             = $this->getModelDetailById($id, $brandId);
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
		$res     = $this->service->modelChangeStatus($modelId, $status);
		$this->render($res);
	}
	
	# endregion
}
