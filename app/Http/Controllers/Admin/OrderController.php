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
			'filter_order_id'  => \Request::input('filter_order_id', ''),
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
	
	/**
	 * 洗车订单表单
	 * @param $orderId
	 * @author 李小同
	 * @date   2018-8-4 10:23:52
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function washOrderForm($orderId) {
		
		if (\Request::getMethod() == 'POST') {
			
			$brandId = $this->service->handleWashOrderForm();
			$this->render($brandId);
			
		} else {
			
			$structure                    = [
				[
					'name_text' => '',
					'type'      => 'hidden',
					'name'      => 'id',
					'value'     => '',
				],
				[
					'name_text' => trans('common.address'),
					'type'      => 'input',
					'name'      => 'address',
					'value'     => '',
				],
				[
					'name_text' => trans('common.contact_user'),
					'type'      => 'input',
					'name'      => 'contact_user',
					'value'     => '',
				],
				[
					'name_text' => trans('common.contact_phone'),
					'type'      => 'input',
					'name'      => 'contact_phone',
					'value'     => '',
				],
			];
			$detail                       = $this->service->getWashOrderDetail($orderId);
			$this->data['html']           = $this->getFormHtml($detail, $structure);
			$this->data['detail']         = $detail;
			$this->data['wash_time_list'] = array_column($this->service->getWashTimeList(), 'value');
			
			return view('admin/order/form', $this->data);
		}
	}
	
	/**
	 * 手动确认支付
	 * @author 李小同
	 * @date   2018-8-4 16:04:23
	 */
	public function confirmPay() {
		
		$orderId = \Request::input('order_id');
		$res     = $this->service->confirmPay($orderId);
		$this->render($res);
	}
	
	/**
	 * 修改订单状态
	 * @author 李小同
	 * @date   2018-8-4 16:04:23
	 */
	public function washOrderChangeStatus() {
		
		$orderId = \Request::input('order_id');
		$res     = $this->service->washOrderChangeStatus($orderId);
		$this->render($res);
	}
	
}
