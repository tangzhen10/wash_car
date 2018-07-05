<?php

namespace App\Http\Controllers\Admin;

/**
 * 会员（前台用户）控制器
 * Class MemberController
 * @package App\Http\Controllers\Admin
 */
class MemberController extends BaseController {
	
	const MODULE = 'member';
	
	/**
	 * 获取用户列表
	 * @author 李小同
	 * @date   2018-7-5 21:07:12
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function memberList() {
		
		$this->data['pagination'] = $this->service->getList();
		$this->data['users']      = json_decode(json_encode($this->data['pagination']), 1)['data'];
		
		return view('admin/member/list', $this->data);
	}
}
