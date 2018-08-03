<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-07-30 20:31
 */

namespace App\Services;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class OrderService extends BaseService {
	
	const STATUS_1 = ['value' => 1, 'text' => '未付款'];
	const STATUS_2 = ['value' => 2, 'text' => '等待接单中'];
	const STATUS_3 = ['value' => 3, 'text' => '已接单'];
	const STATUS_4 = ['value' => 4, 'text' => '服务中'];
	const STATUS_5 = ['value' => 5, 'text' => '已完成'];
	const STATUS_6 = ['value' => 6, 'text' => '已退款'];
	const STATUS_7 = ['value' => 7, 'text' => '已关闭'];
	
	# region 后台
	/**
	 * 洗车订单列表
	 * @param array $filter
	 * @author 李小同
	 * @date   2018-8-3 18:09:33
	 * @return array
	 */
	public function getOrderList(array $filter = []) {
		
		$fields   = [
			'a.order_id',
			'a.wash_product_id',
			'a.address',
			'a.wash_time',
			'a.create_at',
			'b.plate_number',
			'c.name AS brand',
			'd.name AS model',
			'e.name AS color',
		];
		$listPage = \DB::table('wash_order AS a')
		               ->leftJoin('car AS b', 'b.id', '=', 'a.car_id')
		               ->leftJoin('car_brand AS c', 'c.id', '=', 'b.brand_id')
		               ->leftJoin('car_model AS d', 'd.id', '=', 'b.model_id')
		               ->leftJoin('car_color AS e', 'e.id', '=', 'b.color_id')
		               ->leftJoin('user AS f', 'f.user_id', '=', 'a.user_id');
		
		if (!empty($filter['filter_user_id'])) $listPage = $listPage->where('a.user_id', '=', $filter['filter_user_id']);
		if (!empty($filter['filter_date_from'])) $listPage = $listPage->where('a.create_at', '>=', strtotime($filter['filter_date_from']));
		if (!empty($filter['filter_date_to'])) $listPage = $listPage->where('a.create_at', '<=', strtotime($filter['filter_date_to']));
		if (!empty($filter['filter_account'])) {
			$listPage = $listPage->where(function ($query) use ($filter) {
				
				$query->where('f.nickname', 'LIKE', '%'.$filter['filter_account'].'%')
				      ->orWhere('f.phone', 'LIKE', '%'.$filter['filter_account'].'%')
				      ->orWhere('f.email', 'LIKE', '%'.$filter['filter_account'].'%');
			});
		}
		
		$listPage = $listPage->select($fields)->orderBy('a.id', 'desc')->paginate($filter['perPage']);
		$listArr  = json_decode(json_encode($listPage), 1);
		
		$total = $listArr['total'];
		$list  = $listArr['data'];
		
		# format
		foreach ($list as &$item) {
			$item['create_at'] = date('Y-m-d H:i:s', $item['create_at']);
		}
		unset($item);
		
		return compact('list', 'listPage', 'total');
	}
	# endregion
	
	# region 前台
	/**
	 * 订单列表
	 * @author 李小同
	 * @date   2018-8-3 18:01:24
	 * @return array
	 */
	public function getMyWashOrderList() {
		
		$fields = [
			'a.order_id',
			'a.wash_product_id',
			'a.address',
			'a.wash_time',
			'a.create_at',
			'b.plate_number',
			'c.name AS brand',
			'd.name AS model',
			'e.name AS color',
		];
		$rows   = \DB::table('wash_order AS a')
		             ->leftJoin('car AS b', 'b.id', '=', 'a.car_id')
		             ->leftJoin('car_brand AS c', 'c.id', '=', 'b.brand_id')
		             ->leftJoin('car_model AS d', 'd.id', '=', 'b.model_id')
		             ->leftJoin('car_color AS e', 'e.id', '=', 'b.color_id')
		             ->where('a.user_id', $this->userId)
		             ->orderBy('a.id', 'desc')
		             ->get($fields)
		             ->toArray();
		$list   = [];
		foreach ($rows as $row) {
			$list[] = [
				'order_id'     => [
					'text'  => trans('common.order_id'),
					'value' => $row['order_id'],
				],
				'create_at'    => [
					'text'  => trans('common.create_at'),
					'value' => intToTime($row['create_at']),
				],
				'wash_product' => [
					'text'  => trans('common.wash_product'),
					'value' => $row['wash_product_id'],
				],
				'wash_time'    => [
					'text'  => trans('common.wash_time'),
					'value' => $row['wash_time'],
				],
				'car'          => [
					'text'  => trans('common.car_info'),
					'value' => [
						'plate_number' => $row['plate_number'],
						'brand'        => $row['brand'],
						'model'        => $row['model'],
						'color'        => $row['color'],
					],
				],
				'address'      => [
					'text'  => trans('common.address'),
					'value' => $row['address'],
				],
			];
		}
		
		return $list;
	}
	
	/**
	 * 创建洗车订单
	 * @author 李小同
	 * @date   2018-7-31 18:36:31
	 * @return int
	 */
	public function createOrder() {
		
		$post = request_all();
		$this->_validateOrderData($post);
		
		$logger = new Logger('wash_order');
		$logger->pushHandler(new StreamHandler(config('project.PATH_TO_WASH_ORDER_LOG')));
		
		\DB::beginTransaction();
		try {
			
			# 洗车订单表
			$orderData = $this->_addOrder($post);
			
			# 订单日志
			$logData = [
				'wash_order_id' => $orderData['order_id'],
				'type'          => 'add_order',
				'operator'      => 'user - '.$orderData['user_id'],
			];
			$this->addOrderLog($logData);
			
			\DB::commit();
			
			$logger->info('success', $orderData);
			
			return $orderData['order_id'];
			
		} catch (\Exception $e) {
			
			$logger->error($e->getMessage(), ['error_code' => $e->getCode()]);
			
			\DB::rollback();
			
			return false;
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
	
	/**
	 * 获取订单号
	 * @author 李小同
	 * @date   2018-7-31 18:30:26
	 * @return string
	 */
	public function getOrderId() {
		
		$cacheKey = sprintf(config('cache.ORDER.TODAY_ORDER_ID_LIST'), date('ymd'));
		$orderId  = \Redis::lpop($cacheKey);
		
		if (null === $orderId) {
			$this->makeOrderIdList();
			$orderId = \Redis::lpop($cacheKey);
		}
		
		return $orderId;
	}
	
	/**
	 * 创建订单号
	 * @author 李小同
	 * @date   2018-7-31 18:30:02
	 * @return bool
	 */
	public function makeOrderIdList() {
		
		$today = date('ymd');
		
		$orderIdList = [];
		for ($i = 1000; $i < 10000; ++$i) {
			$orderIdList[] = $today.$i;
		}
		shuffle($orderIdList);
		
		$cacheKey = sprintf(config('cache.ORDER.TODAY_ORDER_ID_LIST'), date('ymd'));
		foreach ($orderIdList as $item) {
			\Redis::lpush($cacheKey, $item);
		}
		
		return true;
	}
	
	/**
	 * 记录洗车订单日志
	 * @param array $logData
	 * @author 李小同
	 * @date   2018-8-2 10:58:33
	 * @return mixed
	 */
	public function addOrderLog(array $logData) {
		
		$data = [
			'wash_order_id' => $logData['wash_order_id'],
			'type'          => $logData['type'],
			'operator'      => $logData['operator'],
			'create_at'     => time(),
		];
		$id   = \DB::table('wash_order_log')->insertGetId($data);
		
		return $id;
	}
	
	/**
	 * 获取商品的销售次数
	 * @param $productId
	 * @author 李小同
	 * @date   2018-8-2 21:56:50
	 * @return mixed
	 */
	public function getSaleCount($productId) {
		
		$count = \DB::table('wash_order')->where('wash_product_id', intval($productId))->count('order_id');
		
		return $count;
	}
	
	/**
	 * 检测下单请求数据
	 * @param $data
	 * @author 李小同
	 * @date   2018-7-30 21:49:27
	 */
	private function _validateOrderData($data) {
		
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
		} else {
			if (!preg_match(config('project.PATTERN.PHONE'), $data['contact_phone'])) {
				json_msg(trans('validation.invalid', ['attr' => trans('common.contact_phone')]), 40003);
			}
		}
		
		# 地址
		if (empty($data['address'])) {
			json_msg(trans('validation.required', ['attr' => trans('common.address')]), 40001);
		}
		
		# 地址坐标
		if (empty($data['address_coordinate'])) {
			json_msg(trans('common.lost_coordinate'), 40001);
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
	 * 写入洗车订单表
	 * @param array $post
	 * @author 李小同
	 * @date   2018-8-2 10:47:53
	 * @return mixed
	 */
	private function _addOrder(array $post) {
		
		$price = $this->getProductPrice($post['wash_product_id']);
		if ($price <= 0) json_msg('error.wrong_wash_product_price', 40003);
		
		$orderData = [
			'order_id'           => $this->getOrderId(),
			'user_id'            => $this->userId,
			'wash_product_id'    => intval($post['wash_product_id']),
			'car_id'             => $post['car_id'],
			'address'            => $post['address'],
			'address_coordinate' => $post['address_coordinate'],
			'contact_user'       => $post['contact_user'],
			'contact_phone'      => $post['contact_phone'],
			'wash_time'          => $post['wash_time'],
			'total'              => $price,
			'create_at'          => time(),
			'status'             => self::STATUS_1['value'],
		];
		\DB::table('wash_order')->insert($orderData);
		
		return $orderData;
	}
	# endregion
}