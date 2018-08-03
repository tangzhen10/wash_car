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
		
		$filter                   = [
			'filter_user_id'   => \Request::input('filter_user_id', ''),
			'filter_date_from' => \Request::input('filter_date_from', ''),
			'filter_date_to'   => \Request::input('filter_date_to', ''),
			'filter_account'   => \Request::input('filter_account', ''),
			'perPage'          => $this->getPerPage(),
		];
		$list                     = $this->service->getMemberList($filter);
		var_dump($list                     );die;
		$this->data['list']       = $list['list'];
		$this->data['pagination'] = $list['listPage'];
		$this->data['total']      = $list['total'];
		$this->data['filter']     = $filter;
		
		return view('admin/member/list', $this->data);
	}
	
	/**
	 * 增改表单所需相关数据
	 * @param array $detail
	 * @author 李小同
	 * @date   2018-7-6 21:12:08
	 * @return array
	 */
	public function assocDataForForm($detail = []) {
		
		$check  = [];
		$userId = $detail['user_id'];
		
		if ($userId == 0) json_msg(trans('error.can_not_create_member'), 40003);
		
		$authList = $this->service->getUserAuthList($userId);
		
		foreach ($authList as $auth) {
			
			switch ($auth['identity_type']) {
				case 'email':
					if ($detail['email'] == $auth['identity']) {
						$check['email'] = true;
					}
					break;
				case 'phone':
					if ($detail['phone'] == $auth['identity']) {
						$check['phone'] = true;
					}
					break;
			}
		}
		
		return compact('check');
	}
}
