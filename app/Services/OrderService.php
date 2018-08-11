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
	
	# 订单状态
	const ORDER_STATUS = [
		1 => '未付款',
		2 => '等待接单中',
		3 => '已接单',
		4 => '服务中',
		5 => '已完成',
		6 => '已退款',
		7 => '已关闭',
		8 => '申请退款中',
	];
	
	# 动作名称
	const ORDER_ACTION = [
		'add_order'    => '提交订单',
		'order_pay'    => '订单支付',
		'confirm_pay'  => '确认支付',
		'take_order'   => '派单成功',
		'serve_start'  => '开始服务',
		'serve_finish' => '完成服务',
		'refund_order' => '订单退款',
		'cancel_order' => '取消订单',
		'apply_refund' => '申请退款',
	];
	
	# 订单操作对应操作后的状态
	const ACTION_TO_STATUS = [
		'add_order'    => 1,
		'order_pay'    => 2,
		'confirm_pay'  => 2,
		'take_order'   => 3,
		'serve_start'  => 4,
		'serve_finish' => 5,
		'refund_order' => 6,
		'cancel_order' => 7,
		'apply_refund' => 8,
	];
	
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
			'a.contact_user',
			'a.contact_phone',
			'g.name AS wash_product',
			'a.address',
			'a.wash_time',
			'a.create_at',
			'a.payment_status',
			'a.status',
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
		               ->leftJoin('user AS f', 'f.user_id', '=', 'a.user_id')
		               ->leftJoin('article AS g', 'g.id', '=', 'a.wash_product_id');
		
		if (!empty($filter['filter_order_id'])) $listPage = $listPage->where('a.order_id', '=', $filter['filter_order_id']);
		if (!empty($filter['filter_date_from'])) {
			$listPage = $listPage->where('a.create_at', '>=', strtotime($filter['filter_date_from'].' 00:00:00'));
		}
		if (!empty($filter['filter_date_to'])) {
			$listPage = $listPage->where('a.create_at', '<=', strtotime($filter['filter_date_to'].' 23:59:59'));
		}
		if (!empty($filter['filter_account'])) {
			$listPage = $listPage->where(function ($query) use ($filter) {
				
				$query->where('f.nickname', 'LIKE', '%'.$filter['filter_account'].'%')
				      ->orWhere('f.phone', 'LIKE', '%'.$filter['filter_account'].'%')
				      ->orWhere('f.email', 'LIKE', '%'.$filter['filter_account'].'%');
			});
		}
		
		$listPage = $listPage->select($fields)->orderBy('a.id', 'desc')->paginate($filter['perPage'])->appends($filter);
		$listArr  = json_decode(json_encode($listPage), 1);
		
		$total = $listArr['total'];
		$list  = $listArr['data'];
		
		# format
		foreach ($list as &$item) {
			$item['status_text'] = self::ORDER_STATUS[$item['status']];
			$item['create_at']   = date('Y-m-d H:i:s', $item['create_at']);
			if (empty($item['brand'])) $item['brand'] = trans('common.other');
			if (empty($item['model'])) $item['model'] = '';
		}
		unset($item);
		
		return compact('list', 'listPage', 'total');
	}
	
	/**
	 * 获取清洗前后照片的图片及表单
	 * @param int $orderId
	 * @param int $status 订单状态
	 * @author 李小同
	 * @date   2018-8-5 14:53:40
	 * @return array
	 */
	public function getWashImagesAndHtml($orderId, $status) {
		
		$types = ['before', 'after'];
		foreach ($types as $type) {
			$imagesHtml[$type] = '';
			$images[$type]     = [];
		}
		if (!in_array($status, [1, 2])) {
			$structure = [
				[
					'name_text' => '',
					'type'      => 'hidden',
					'name'      => 'wash_order_id',
					'value'     => '',
				],
				[
					'name_text' => '',
					'type'      => 'hidden',
					'name'      => 'type',
					'value'     => '',
				],
				[
					'name_text' => trans('common.picture'),
					'type'      => 'images',
					'name'      => 'images',
					'value'     => '上传3张图片',
				],
			];
			
			$images = $this->getWashImages($orderId);
			foreach ($types as $type) {
				$imagesInfo        = [
					'wash_order_id' => $orderId,
					'type'          => $type,
					'images'        => empty($images[$type]) ? [] : array_column($images[$type], 'thumb'),
				];
				$html              = $this->getFormHtmlByStructure($structure, $imagesInfo);
				$imagesHtml[$type] = $html;
			}
			if ($status == 3) { # 接单时不允许上传清洗后照片
				$imagesHtml['after'] = '';
			} elseif ($status == 4) { # 开始服务后，不允许修改清洗前照片
				$imagesHtml['before'] = '<span class="J_image_preview">';
				foreach ($images['before'] as $image) {
					$imagesHtml['before'] .= '<img src="'.\URL::asset($image['thumb']).'" onclick="javascript:window.open(\''.\URL::asset($image['src']).'\')" />';
				}
				$imagesHtml['before'] .= '</span>';
			} elseif (!in_array($status, [1, 2])) { # 其他状态下，不允许修改清洗照片
				foreach ($types as $type) {
					$imagesHtml[$type] = '<span class="J_image_preview">';
					foreach ($images[$type] as $image) {
						$imagesHtml[$type] .= '<img src="'.\URL::asset($image['thumb']).'" onclick="javascript:window.open(\''.\URL::asset($image['src']).'\')" />';
					}
					$imagesHtml[$type] .= '</span>';
				}
			}
		}
		
		return compact('imagesHtml', 'images');
	}
	
	/**
	 * 处理洗车订单表单
	 * @author 李小同
	 * @date   2018-8-4 11:51:12
	 */
	public function handleWashOrderForm() {
		
		$post = request_all();
		
		# validation
		if (empty($post['address'])) {
			json_msg(trans('validation.required', ['attr' => trans('common.address')]), 40001);
		}
		if (empty($post['contact_phone'])) {
			json_msg(trans('validation.required', ['attr' => trans('common.contact_phone')]), 40001);
		} elseif (!preg_match(config('project.PATTERN.PHONE'), $post['contact_phone'])) {
			json_msg(trans('validation.invalid', ['attr' => trans('common.contact_phone')]), 40003);
		}
		$this->validateWashTime($post['wash_time']);
		
		$where = ['id' => $post['id']];
		if ($post['payment_status'] == 1) {
			$post['status'] = 2;
		}
		\DB::table('wash_order')->where($where)->update($post);
		
		return true;
	}
	
	/**
	 * 确认支付
	 * @param $orderId
	 * @author 李小同
	 * @date   2018-8-5 00:23:35
	 * @return bool
	 */
	public function confirmPay($orderId) {
		
		$order = \DB::table('wash_order')->where('order_id', $orderId)->first(['status', 'payment_status']);
		\DB::beginTransaction();
		try {
			if ($order['payment_status'] == '0') {
				$newStatus  = 2;
				$updateData = ['payment_status' => '1', 'status' => $newStatus];
				\DB::table('wash_order')->where('order_id', $orderId)->update($updateData);
				
				$logData = [
					'wash_order_id' => $orderId,
					'action'        => 'confirm_pay',
					'order_status'  => $newStatus,
					'operator_type' => 'manager',
				];
				$this->addOrderLog($logData);
				
				\DB::commit();
				return true;
			}
			
		} catch (\Exception $e) {
			\DB::rollback();
			return false;
		}
		
	}
	
	/**
	 * 修改订单状态
	 * @param int $orderId
	 * @param int $status 新状态
	 * @author 李小同
	 * @date   2018-8-5 00:25:49
	 * @return bool
	 */
	public function adminWashOrderChangeStatus($orderId, $status) {
		
		$order  = \DB::table('wash_order')->where('order_id', $orderId)->first(['status']);
		$status = intval($status);
		
		\DB::beginTransaction();
		try {
			$flag   = false;
			$action = '';
			switch ($status) {
				case self::ACTION_TO_STATUS['take_order']:
					if ($order['status'] == self::ACTION_TO_STATUS['order_pay']) {
						$flag   = true;
						$action = 'take_order';
						$this->_setWasher($orderId);
					}
					break;
				case self::ACTION_TO_STATUS['serve_start']:
					if ($order['status'] == self::ACTION_TO_STATUS['take_order']) {
						$flag   = true;
						$action = 'serve_start';
					}
					break;
				case self::ACTION_TO_STATUS['serve_finish']:
					if ($order['status'] == self::ACTION_TO_STATUS['serve_start']) {
						$flag   = true;
						$action = 'serve_finish';
					}
					break;
			}
			if ($flag) {
				\DB::table('wash_order')->where('order_id', $orderId)->update(['status' => $status]);
				
				$logData = [
					'wash_order_id' => $orderId,
					'action'        => $action,
					'order_status'  => $status,
					'operator_type' => 'manager',
				];
				$this->addOrderLog($logData);
				
				\DB::commit();
				return true;
			} else {
				return false;
			}
			
		} catch (\Exception $e) {
			print_r($e->getMessage());
			print_r($e->getFile());
			print_r($e->getLine());
			\DB::rollback();
			return false;
		}
		
	}
	
	/**
	 * 上传洗车前后照片
	 * @param array $post
	 * @author 李小同
	 * @date   2018-8-5 14:29:33
	 * @return bool
	 */
	public function uploadImages(array $post = []) {
		
		if (!empty($post['wash_order_id'])) {
			if ($post['images'][0]) {
				$value = ToolService::uploadFiles($post['images']);
				$where = [
					'wash_order_id' => $post['wash_order_id'],
					'type'          => $post['type'],
				];
				\DB::table('wash_image')->where($where)->update(['status' => '-1']);
				$imageData = [
					'wash_order_id' => $post['wash_order_id'],
					'type'          => $post['type'],
					'images'        => $value,
					'create_at'     => time(),
					'create_by'     => $this->_getFormatManager(),
				];
				$id        = \DB::table('wash_image')->insertGetId($imageData);
				return $id;
			} else {
				return true;
			}
			
		} else {
			return false;
		}
	}
	
	/**
	 * 获取格式化的管理员名称
	 * @author 李小同
	 * @date   2018-8-5 14:08:29
	 * @return string
	 */
	private function _getFormatManager() {
		
		return '【'.trans('common.manager').'】'.\ManagerService::getManagerName();
	}
	
	/**
	 * 设置接单员
	 * @param $orderId
	 * @author 李小同
	 * @date   2018-8-8 18:01:17
	 * @return bool
	 */
	private function _setWasher($orderId) {
		
		$res = \DB::table('wash_order')
		          ->where('order_id', $orderId)
		          ->update(['washer_id' => \ManagerService::getManagerId()]);
		return $res;
	}
	# endregion
	
	# region 公共
	
	/**
	 * 获取当前可用的洗车时间你列表
	 * @author 李小同
	 * @date   2018-8-4 10:49:12
	 * @return array
	 */
	public function getWashTimeList() {
		
		$todayText    = trans('common.today');
		$tomorrowText = trans('common.tomorrow');
		$today        = date('Y-m-d');
		$tomorrow     = date('Y-m-d', strtotime('+1 day'));
		$timeList     = [
			$today.' 00:00:00'    => ['text' => $todayText.' 00:00-01:00', 'value' => $today.' 00:00-01:00'],
			$today.' 01:00:00'    => ['text' => $todayText.' 01:00-02:00', 'value' => $today.' 01:00-02:00'],
			$today.' 21:00:00'    => ['text' => $todayText.' 21:00-22:00', 'value' => $today.' 21:00-22:00'],
			$today.' 22:00:00'    => ['text' => $todayText.' 22:00-23:00', 'value' => $today.' 22:00-23:00'],
			$today.' 23:00:00'    => ['text' => $todayText.' 23:00-24:00', 'value' => $today.' 23:00-24:00'],
			$tomorrow.' 00:00:00' => ['text' => $tomorrowText.' 00:00-01:00', 'value' => $tomorrow.' 00:00-01:00'],
			$tomorrow.' 01:00:00' => ['text' => $tomorrowText.' 01:00-02:00', 'value' => $tomorrow.' 01:00-02:00'],
		];
		
		$now      = date('Y-m-d H:i:s');
		$am2clock = date('Y-m-d 02:00:00'); # 今天2点
		
		$list = [];
		foreach ($timeList as $key => $item) {
			
			if ($now < $am2clock && $am2clock < $key) continue;
			if ($now > $key) continue;
			
			$list[] = $item;
		}
		
		return $list;
	}
	
	/**
	 * 检测清洗时间
	 * @param $washTime
	 * @author 李小同
	 * @date   2018-8-4 11:55:12
	 */
	public function validateWashTime($washTime) {
		
		$allowedTimeList = array_column($this->getWashTimeList(), 'value');
		if (!in_array($washTime, $allowedTimeList)) {
			json_msg(trans('error.wrong_wash_time'), 40003);
		}
	}
	
	/**
	 * 获取洗车订单详情
	 * @param $orderId
	 * @author 李小同
	 * @date   2018-8-4 10:03:18
	 * @return array
	 */
	public function getWashOrderDetail($orderId) {
		
		$fields = [
			'a.id',
			'a.order_id',
			'a.user_id',
			'a.wash_product_id',
			'a.contact_user',
			'a.contact_phone',
			'a.address',
			'a.wash_time',
			'a.payment_status',
			'a.total',
			'a.status',
			'a.create_at',
			'b.plate_number',
			'c.name AS brand',
			'd.name AS model',
			'e.name AS color',
			'f.nickname AS username',
			'f.phone AS phone',
			'g.name AS wash_product',
			'a.washer_id',
		];
		$detail = \DB::table('wash_order AS a')
		             ->leftJoin('car AS b', 'b.id', '=', 'a.car_id')
		             ->leftJoin('car_brand AS c', 'c.id', '=', 'b.brand_id')
		             ->leftJoin('car_model AS d', 'd.id', '=', 'b.model_id')
		             ->leftJoin('car_color AS e', 'e.id', '=', 'b.color_id')
		             ->leftJoin('user AS f', 'f.user_id', '=', 'a.user_id')
		             ->leftJoin('article AS g', 'g.id', '=', 'a.wash_product_id')
		             ->select($fields)
		             ->where('order_id', $orderId)
		             ->where('a.status', '!=', '-1')
		             ->first();
		if (empty($detail)) json_msg(trans('common.not_exist_order'), 40003);
		
		# 取消 & 退款 & 申请售后
		switch ($detail['status']) {
			case self::ACTION_TO_STATUS['add_order']:
				
				# 未付款，1小时倒计时
				$cancelAt                   = $detail['create_at'] + 3600;
				$orderStatusMsg             = '* 若不支付，本单将于'.date('Y-m-d H:i:s', $cancelAt).'自动取消！';
				$detail['cancel_at']        = $cancelAt;
				$detail['order_status_msg'] = $orderStatusMsg;
				
				$detail['button'] = [
					'text'   => trans('common.cancel'),
					'action' => 'cancel_order',
				];
				break;
			case 2:
				$detail['button'] = [
					'text'   => trans('common.refund'),
					'action' => 'refund_order',
				];
				break;
			case 3:
				$detail['button'] = [
					'text'   => self::ORDER_ACTION['apply_refund'],
					'action' => 'apply_refund',
				];
				break;
			default:
				$detail['button'] = [
					'text'   => trans('common.after_sale'),
					'action' => 'after_sale',
				];
		}
		
		# 服务人员
		if (empty($detail['washer_id'])) {
			$detail['washer']       = '';
			$detail['washer_phone'] = '';
		} else {
			$washer                 = \ManagerService::getManagerInfoByManagerId($detail['washer_id']);
			$detail['washer']       = $washer['name'];
			$detail['washer_phone'] = $washer['phone'];
		}
		if (empty($detail['username'])) $detail['username'] = '无昵称用户';
		if (empty($detail['brand'])) $detail['brand'] = trans('common.other');
		if (empty($detail['model'])) $detail['model'] = '';
		$detail['status_text'] = self::ORDER_STATUS[$detail['status']];
		$detail['create_at']   = intToTime($detail['create_at']);
		$detail['total']       = currencyFormat($detail['total']);
		
		return $detail;
	}
	
	/**
	 * 获取订单中的清洗前后照片
	 * @param      $orderId
	 * @param bool $flag true：拼接domain false：不拼接
	 * @author 李小同
	 * @date   2018-8-7 20:44:33
	 * @return array
	 */
	public function getWashImages($orderId, $flag = false) {
		
		$images = ['before' => [], 'after' => []];
		$where  = ['wash_order_id' => $orderId, 'status' => '1'];
		$rows   = \DB::table('wash_image')->where($where)->get(['images', 'type'])->toArray();
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$imagesArr = explode(',', $row['images']);
				foreach ($imagesArr as $src) {
					if ($flag) $src = \URL::asset($src);
					$thumb                  = dirname($src).'/'.config('project.THUMB_PREFIX').basename($src);
					$images[$row['type']][] = compact('thumb', 'src');
				}
			}
		}
		
		return $images;
	}
	
	/**
	 * 获取订单操作日志
	 * @param $orderId
	 * @author 李小同
	 * @date   2018-8-5 08:11:41
	 * @return array
	 */
	public function getOrderLogs($orderId) {
		
		$fields = ['action', 'create_at', 'order_status', 'operator'];
		$logs   = \DB::table('wash_order_log')->where('wash_order_id', $orderId)->get($fields)->toArray();
		foreach ($logs as &$log) {
			$log['create_at']    = intToTime($log['create_at']);
			$log['action_text']  = self::ORDER_ACTION[$log['action']];
			$log['order_status'] = self::ORDER_STATUS[$log['order_status']];
		}
		unset($log);
		
		return $logs;
	}
	# endregion
	
	# region 前台
	/**
	 * 订单列表
	 * @param int $page
	 * @author 李小同
	 * @date   2018-8-3 18:01:24
	 * @return array
	 */
	public function getMyWashOrderList($page = 1) {
		
		$fields  = [
			'a.order_id',
			'a.address',
			'a.wash_time',
			'a.status',
			'a.create_at',
			'b.plate_number',
			'c.name AS brand',
			'd.name AS model',
			'e.name AS color',
			'f.name AS wash_product',
		];
		$perPage = \SettingService::getValue('per_page');
		$rows    = \DB::table('wash_order AS a')
		              ->leftJoin('car AS b', 'b.id', '=', 'a.car_id')
		              ->leftJoin('car_brand AS c', 'c.id', '=', 'b.brand_id')
		              ->leftJoin('car_model AS d', 'd.id', '=', 'b.model_id')
		              ->leftJoin('car_color AS e', 'e.id', '=', 'b.color_id')
		              ->leftJoin('article AS f', 'f.id', '=', 'a.wash_product_id')
		              ->where('a.user_id', $this->userId)
		              ->orderBy('a.id', 'desc')
		              ->offset(($page - 1) * $perPage)
		              ->limit($perPage)
		              ->get($fields)
		              ->toArray();
		$list    = [];
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
				'status'       => [
					'text'   => trans('common.order_status'),
					'value'  => self::ORDER_STATUS[$row['status']],
					'status' => $row['status'], # 给客户端显示颜色用
				],
				'wash_product' => [
					'text'  => trans('common.wash_product'),
					'value' => $row['wash_product'],
				],
				'wash_time'    => [
					'text'  => trans('common.wash_time'),
					'value' => $row['wash_time'],
				],
				'car'          => [
					'text'  => trans('common.car_info'),
					'value' => [
						'plate_number' => $row['plate_number'],
						'brand'        => empty($row['brand']) ? trans('common.other') : $row['brand'],
						'model'        => empty($row['model']) ? '' : $row['model'],
						'color'        => $row['color'],
					],
				],
				'address'      => [
					'text'  => trans('common.serve_address'),
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
				'action'        => 'add_order',
				'order_status'  => 1,
				'operator_type' => 'user',
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
		
		switch ($logData['operator_type']) {
			case 'manager':
				$logData['operator_id'] = \ManagerService::getManagerId();
				$logData['operator']    = $this->_getFormatManager();
				break;
			case 'user':
				$logData['operator_id'] = $this->userId;
				$logData['operator']    = $this->_getFormatUser();
				break;
			case 'system':
				$logData['operator_id'] = 0;
				$logData['operator']    = '【系统】system';
				break;
		}
		$data = [
			'wash_order_id' => $logData['wash_order_id'],
			'action'        => $logData['action'],
			'order_status'  => $logData['order_status'],
			'operator'      => $logData['operator'],
			'operator_type' => $logData['operator_type'],
			'operator_id'   => $logData['operator_id'],
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
	 * 用户订单取消或退款
	 * @param array $post
	 * @author 李小同
	 * @date   2018-8-8 15:13:14
	 * @return bool
	 */
	public function userWashOrderChangeStatus(array $post) {
		
		$fields = ['order_id', 'payment_status', 'status'];
		$order  = \DB::table('wash_order')->where('order_id', $post['order_id'])->first($fields);
		if (empty($order['order_id'])) json_msg(trans('error.illegal_action'), 40003);
		switch ($post['action']) {
			case 'refund_order':
				return $this->_refundWashOrder($order);
				break;
			case 'cancel_order':
				return $this->_cancelWashOrder($order);
				break;
			default:
				json_msg(trans('error.illegal_action'), 40003);
		}
		return true;
	}
	
	/**
	 * 用户取消订单
	 * @param array $order
	 * @author 李小同
	 * @date   2018-8-8 16:31:41
	 * @return bool
	 */
	private function _refundWashOrder(array $order) {
		
		if (in_array($order['status'], [3, 4, 5])) {
			
			\DB::beginTransaction();
			try {
				
				# todo lxt 微信退款
				
				# todo lxt 余额退款
				
				$action = 'refund_order';
				$status = self::ACTION_TO_STATUS['refund_order'];
				
				\DB::table('wash_order')->where('order_id', $order['order_id'])->update(['status' => $status]);
				
				$logData = [
					'wash_order_id' => $order['order_id'],
					'action'        => $action,
					'order_status'  => $status,
					'operator_type' => 'user',
				];
				$this->addOrderLog($logData);
				
				\DB::commit();
				return true;
				
			} catch (\Exception $e) {
				\DB::rollback();
				return false;
			}
		} else {
			json_msg(trans('error.illegal_action'), 40003);
		}
	}
	
	/**
	 * 用户取消订单
	 * @param array $order
	 * @author 李小同
	 * @date   2018-8-8 16:31:41
	 * @return bool
	 */
	private function _cancelWashOrder(array $order) {
		
		if (in_array($order['status'], [1, 2])) {
			
			\DB::beginTransaction();
			try {
				$action = 'cancel_order';
				$status = self::ACTION_TO_STATUS['cancel_order'];
				
				\DB::table('wash_order')->where('order_id', $order['order_id'])->update(['status' => $status]);
				
				$logData = [
					'wash_order_id' => $order['order_id'],
					'action'        => $action,
					'order_status'  => $status,
					'operator_type' => 'user',
				];
				$this->addOrderLog($logData);
				
				\DB::commit();
				return true;
				
			} catch (\Exception $e) {
				\DB::rollback();
				return false;
			}
		} else {
			json_msg(trans('error.illegal_action'), 40003);
		}
	}
	
	/**
	 * 获取格式化的用户信息
	 * @author 李小同
	 * @date   2018-8-8 16:30:45
	 * @return string
	 */
	private function _getFormatUser() {
		
		return '【'.trans('common.user').'】'.\UserService::getUserInfo('phone');
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
				'content_type' => \SettingService::getValue('product_content_type'),
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
		} else {
			$this->validateWashTime($data['wash_time']);
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
			'status'             => 1,
		];
		\DB::table('wash_order')->insert($orderData);
		
		return $orderData;
	}
	# endregion
}