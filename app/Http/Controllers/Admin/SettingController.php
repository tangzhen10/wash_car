<?php

namespace App\Http\Controllers\Admin;

class SettingController extends BaseController {
	
	const MODULE = 'setting';
	
	/**
	 * 前台设置
	 * @author 李小同
	 * @date   2018-7-22 15:16:11
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function main() {
		
		$this->data['list'] = $this->service->getMainList();
		
		return view('admin/setting/list', $this->data);
	}
	
	/**
	 * 后台设置
	 * @author 李小同
	 * @date   2018-7-22 13:03:01
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function admin() {
		
		$this->data['list'] = $this->service->getAdminList();
		
		return view('admin/setting/list', $this->data);
	}
	
}
