<?php

namespace App\Http\Controllers\Admin;

class OrderController extends BaseController {
	
	const MODULE = 'order';
	
	/**
	 * 洗车订单列表
	 * @author 李小同
	 * @date
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function washOrderList() {
		
		$filter                   = [
			'filter_user_id'   => \Request::input('filter_user_id', ''),
			'filter_date_from' => \Request::input('filter_date_from', ''),
			'filter_date_to'   => \Request::input('filter_date_to', ''),
			'filter_account'   => \Request::input('filter_account', ''),
			'perPage'          => $this->getPerPage(),
		];
		$list                     = $this->service->getOrderList($filter);
		$this->data['list']       = $list['list'];
		$this->data['pagination'] = $list['listPage'];
		$this->data['total']      = $list['total'];
		$this->data['filter']     = $filter;
		
		return view('admin/order/list', $this->data);
	}
	
}
