<?php

namespace App\Http\Controllers\Admin;

class CarController extends BaseController {
	
	const MODULE = 'car';
	
	/**
	 * 品牌列表
	 * @author 李小同
	 * @date   2018-7-24 15:23:41
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function brandList() {
		
		$this->data['list'] = $this->service->getBrandList();
		
		return view('admin/car/brand/list', $this->data);
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
}
