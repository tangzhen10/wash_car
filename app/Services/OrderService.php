<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-07-30 20:31
 */

namespace App\Services;

class OrderService extends BaseService {
	
	public function createOrder() {
		
		$post = request_all();
		$this->_validateOrderData($post);
		
		$price = $this->getProductPrice($post['wash_product_id']);
		if ($price <= 0) json_msg('error.wrong_wash_product_price', 40003);
		
		$orderData = [
			'order_id'           => $this->getOrderId(),
			'user_id'            => $this->userId,
			'wash_product_id'    => intval($post['wash_product_id']),
			'user_id'            => $post['user_id'],
			'wash_product_id'    => $post['wash_product_id'],
			'car_id'             => $post['car_id'],
			'address'            => $post['address'],
			'address_coordinate' => $post['address_coordinate'],
			'contact_user'       => $post['contact_user'],
			'contact_phone'      => $post['contact_phone'],
			'wash_time'          => $post['wash_time'],
			'total'              => $price,
			'create_at'          => time(),
		];
		\DB::table('wash_order')->insert($orderData);
	}
	
	/**
	 * 检测下单请求数据
	 * @param $data
	 * @author 李小同
	 * @date   2018-7-30 21:49:27
	 */
	public function _validateOrderData($data) {
		
		# 洗车服务产品
		if (empty($data['wash_product_id'])) {
			json_msg(trans('validation.required', ['attr' => trans('common.wash_product')]), 40001);
		} else {
			$filter  = [
				'id'           => $data['wash_product_id'],
				'content_type' => config('project.CONTENT_TYPE.WASH_PRODUCT'),
			];
			$product = \ArticleService::getArticlePublicInfo($filter)->count('id');
			if (!$product) json_msg(trans('validation.invalid', ['attr' => trans('common.wash_product')]), 40003);
		}
		
		# 联系电话
		if (empty($data['contact_phone'])) {
			json_msg(trans('validation.required', ['attr' => trans('common.contact_phone')]), 40001);
		}
		
		# 地址
		if (empty($data['address'])) {
			json_msg(trans('validation.required', ['attr' => trans('common.address')]), 40001);
		}
		# 车辆
		if (empty($data['car_id'])) {
			json_msg(trans('validation.required', ['attr' => trans('common.car')]), 40001);
		}
		# 清洗时间
		if (empty($data['wash_time'])) {
			json_msg(trans('validation.required', ['attr' => trans('common.wash_time')]), 40001);
		}
	}
	
	/**
	 * 获取洗车服务价格
	 * @param $id int 洗车服务项目id
	 * @author 李小同
	 * @date   2018-7-30 21:52:02
	 * @return int
	 */
	public function getProductPrice($id) {
		
		$result = \DB::table('article_detail')
		             ->where('article_id', $id)
		             ->where('name', 'price')
		             ->pluck('value')
		             ->toArray();
		return count($result) ? round($result[0], 2) : 0;
	}
	
	public function getOrderId() {
		
		$this->createOrderIdList();
	}
	
	public function createOrderIdList() {
		
		$today = today();
		echo $today;
		die;
	}
	
}