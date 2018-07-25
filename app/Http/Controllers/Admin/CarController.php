<?php

namespace App\Http\Controllers\Admin;

use App\Services\ContentTypeService;
use App\Services\ToolService;

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
		
		if (\Request::getMethod() == 'POST') $this->handleBrandForm();
		
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
		$article   = ['detail' => $this->getBrandDetailById($id)];
		
		$html               = '';
		$contentTypeService = new ContentTypeService();
		foreach ($structure as $field) {
			
			$value    = isset($article['detail'][$field['name']]) ? $article['detail'][$field['name']] : ($field['type'] == 'checkbox' ? [] : '');
			$funcName = $field['type'].'FormElement';
			if (method_exists($contentTypeService, $funcName)) $html .= $contentTypeService->$funcName($field, $value);
		}
		$this->data['html'] = $html;
		
		return view('admin/car/brand/form', $this->data);
	}
	
	/**
	 * 处理品牌表单请求
	 * @author 李小同
	 * @date   2018-7-24 17:52:31
	 * @return mixed
	 */
	public function handleBrandForm() {
		
		$post = request_all();
		
		if (empty($post['name'])) json_msg(trans('validation.required', ['attr' => trans('common.name')]), 40001);
		if (empty($post['first_letter'])) json_msg(trans('validation.required', ['attr' => trans('common.first_letter')]), 40001);
		
		$brandId = $post['id'];
		$query   = \DB::table('car_brand');
		
		if (empty($post['logo'])) {
			$post['logo'] = $post['uploadfile_logo'];
		} else {
			$files        = \Request::file('logo');
			$post['logo'] = ToolService::uploadFiles($files);
		}
		unset($post['uploadfile_logo']);
		
		if (!$brandId) {
			$brandId = $query->insertGetId($post);
		} else {
			
			$where = ['id' => $brandId];
			unset($post['id']);
			$query->where($where)->update($post);
		}
		$this->render($brandId);
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
	public function modelList() {
		
		$branId = \Request::input('brand_id', 0);
		$filter = [
			'brand_id' => $branId,
		];
		$list   = $this->service->getModelList($filter);
		
		$this->data['list']       = $list['list'];
		$this->data['pagination'] = $list['listPage'];
		$this->data['total']      = $list['total'];
		
		return view('admin/car/model/list', $this->data);
	}
	
	# endregion
}
