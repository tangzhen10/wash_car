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
		
		$this->data['pagination'] = $this->service->getPaginationList();
		$this->data['total']      = json_decode(json_encode($this->data['pagination']), 1)['total'];
		$this->data['members']    = $this->service->getListByPage($this->data['pagination']);
		
		return view('admin/member/list', $this->data);
	}
	
	/**
	 * 增改表单所需相关数据
	 * @param $data
	 * @author 李小同
	 * @date   2018-7-6 21:12:08
	 * @return array
	 */
	public function assocDataForForm($data = null) {
		
		$check    = [];
		$userId   = $data['detail']['user_id'];
		$authList = $this->service->getUserAuthList($userId);
		
		foreach ($authList as $auth) {
			
			switch ($auth['identity_type']) {
				case 'email':
					if ($data['detail']['email'] == $auth['identity']) {
						$check['email'] = true;
					}
					break;
				case 'phone':
					if ($data['detail']['phone'] == $auth['identity']) {
						$check['phone'] = true;
					}
					break;
			}
		}
		
		return compact('check');
	}
}
