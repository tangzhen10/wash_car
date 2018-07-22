<?php

namespace App\Http\Controllers\Admin;

class CarController extends BaseController {
	
	const MODULE = 'car';
	
	public function brandList() {
		
		$this->data['list'] = $this->service->getBrandList();
		
		return view('admin/car/brand/list', $this->data);
	}
}
