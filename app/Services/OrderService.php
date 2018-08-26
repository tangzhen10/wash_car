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
		'add_order'     => '提交订单',
		'pay_order'     => '订单支付',
		'confirm_pay'   => '确认支付',
		'take_order'    => '派单成功',
		'serve_start'   => '开始服务',
		'serve_finish'  => '完成服务',
		'refund_order'  => '订单退款',
		'cancel_order'  => '取消订单',
		'apply_refund'  => '申请退款',
		'agree_refund'  => '同意退款',
		'reject_refund' => '不予退款',
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
			'a.total',
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
		if (!empty($filter['filter_wash_product_id'])) $listPage = $listPage->where('a.wash_product_id', '=', $filter['filter_wash_product_id']);
		if (!empty($filter['filter_status'])) $listPage = $listPage->where('a.status', '=', $filter['filter_status']);
		
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
		# 待我服务，状态为已接单、开始服务，接单人为我
		if (!empty($filter['filter_serve_by_me'])) {
			$listPage = $listPage->where(function ($query) {
				
				$query->where('a.washer_id', \ManagerService::getManagerId())->whereIn('a.status', [3, 4]);
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
			$item['total']       = currencyFormat($item['total']);
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
				
				$updateData = [
					'order_id'       => $orderId,
					'action'         => 'confirm_pay',
					'status'         => 2,
					'operator_type'  => 'manager',
					'payment_status' => '1',
				];
				$this->_updateOrder($updateData);
				
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
	 * @param int    $orderId
	 * @param string $action
	 * @author 李小同
	 * @date   2018-8-5 00:25:49
	 * @return bool
	 */
	public function adminWashOrderChangeStatus($orderId, $action) {
		
		$order = \DB::table('wash_order')->where('order_id', $orderId)->first(['status', 'payment_status']);
		
		\DB::beginTransaction();
		try {
			$flag = false;
			switch ($action) {
				case 'take_order':
					if ($order['status'] == 2) {
						$flag   = true;
						$status = 3;
						$this->_setWasher($orderId);
					}
					break;
				case 'serve_start':
					if ($order['status'] == 3) {
						$flag   = true;
						$status = 4;
					}
					break;
				case 'serve_finish':
					if ($order['status'] == 4) {
						$flag   = true;
						$status = 5;
					}
					break;
				case 'agree_refund': # 同意退款
					if ($order['status'] == 8 && $order['payment_status'] == '1') {
						$flag   = true;
						$status = 6;
						$this->refundOrder($orderId, 'admin');
					}
					break;
				case 'reject_refund': # 不予退款
					if ($order['status'] == 8 && $order['payment_status'] == '1') {
						$flag   = true;
						$status = 3;
					}
					break;
			}
			if ($flag) {
				
				$updateData = [
					'order_id'      => $orderId,
					'action'        => $action,
					'status'        => $status,
					'operator_type' => 'manager',
				];
				$this->_updateOrder($updateData);
				
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
	 * 修改订单并添加日志
	 * @param array $data
	 * @author 李小同
	 * @date   2018-08-21 10:43:55
	 * @return bool
	 */
	private function _updateOrder(array $data) {
		
		$updateData = [
			'status'    => $data['status'],
			'update_at' => time(),
		];
		if (isset($data['payment_status'])) $updateData['payment_status'] = $data['payment_status'];
		if (isset($data['payment_method'])) $updateData['payment_method'] = $data['payment_method'];
		
		\DB::table('wash_order')->where('order_id', $data['order_id'])->update($updateData);
		
		$logData = [
			'wash_order_id' => $data['order_id'],
			'action'        => $data['action'],
			'order_status'  => $data['status'],
			'operator_type' => $data['operator_type'],
		];
		$this->addOrderLog($logData);
		
		return true;
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
	
	/**
	 * 获取格式化的管理员名称
	 * @author 李小同
	 * @date   2018-8-5 14:08:29
	 * @return string
	 */
	private function _getFormatManager() {
		
		return '【'.trans('common.manager').'】'.\ManagerService::getManagerName();
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
	 * 获取洗车订单
	 * @param $orderId
	 * @author 李小同
	 * @date   2018-08-25 10:44:37
	 * @return array
	 */
	public function getWashOrder($orderId) {
		
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
		$order  = \DB::table('wash_order AS a')
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
		if (empty($order)) json_msg(trans('common.not_exist_order'), 40003);
		
		if ($order['status'] == 1) {
			
			# 未付款，1小时倒计时
			$cancelAt = $order['create_at'] + 3600;
			if ($cancelAt <= time()) {
				if ($this->_cancelWashOrder($order, true)) {
					return $this->getWashOrder($orderId);
				}
			}
		}
		
		return $order;
	}
	
	/**
	 * 获取洗车订单详情
	 * @param $orderId
	 * @author 李小同
	 * @date   2018-8-4 10:03:18
	 * @return array
	 */
	public function getWashOrderDetail($orderId) {
		
		$detail = $this->getWashOrder($orderId);
		
		# 取消 & 退款 & 申请售后
		switch ($detail['status']) {
			case 1:
				
				# 未付款，1小时倒计时
				$cancelAt              = $detail['create_at'] + 3600;
				$detail['cancel_at']   = $cancelAt;
				$detail['cancel_left'] = $cancelAt - time();
				$detail['button']      = [
					'text'   => trans('common.cancel'),
					'action' => 'cancel_order',
				];
				break;
			case 2:
				$detail['button'] = [
					'text'   => trans('common.cancel_refund'),
					'action' => 'refund_order',
				];
				break;
			case 3:
				$detail['button'] = [
					'text'   => trans('common.apply_refund'),
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
		$detail['total_value'] = $detail['total'];
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
	
	/**
	 * 退款
	 * @param int    $orderId
	 * @param string $operateType admin|user|system
	 * @author 李小同
	 * @date   2018-08-25 11:25:10
	 * @return bool
	 */
	public function refundOrder($orderId, $operateType) {
		
		$paymentLogs = $this->_getPaymentLogs($orderId);
		foreach ($paymentLogs as $paymentLog) {
			
			if ($paymentLog['payment_method'] == 'balance') {
				$data = [
					'order_id'       => $orderId,
					'payment_method' => 'balance',
					'amount'         => $paymentLog['amount'],
					'user_id'        => $paymentLog['create_by'],
					'operate_type'   => $operateType,
				];
				$this->_balanceRefund($data);
				
			} elseif ($paymentLog['payment_method'] == 'wechat') {
				
				$this->_wechatRefund($orderId);
			}
		}
		
		return true;
	}
	
	/**
	 * 获取支付记录
	 * @param $orderId
	 * @author 李小同
	 * @date   2018-08-25 11:06:45
	 * @return array
	 */
	private function _getPaymentLogs($orderId) {
		
		# amount大于0为支付，小于0为退款
		$fields = ['payment_method', 'amount', 'create_by'];
		$logs   = \DB::table('payment_log')
		             ->where('order_id', $orderId)
		             ->where('amount', '>', 0)
		             ->get($fields)
		             ->toArray();
		return $logs;
	}
	
	/**
	 * 余额退款
	 * @param array $data
	 * @author 李小同
	 * @date   2018-08-25 11:25:37
	 * @return bool
	 */
	private function _balanceRefund(array $data) {
		
		# 余额使用记录
		$useBalanceData = [
			'amount'   => floatval($data['amount']),
			'type'     => 'refund_order',
			'order_id' => $data['order_id'],
			'comment'  => '【订单退款】'.$data['order_id'],
			'user_id'  => $data['user_id'],
		];
		$this->_addBalanceDetail($useBalanceData);
		
		$refundData = [
			'order_id'       => $data['order_id'],
			'payment_method' => $data['payment_method'],
			'amount'         => -$data['amount'],
			'operate_type'   => $data['operate_type'],
			'creator'        => $data['operate_type'] == 'admin' ? $this->_getFormatManager() : $this->_getFormatUser(),
			'create_by'      => $data['operate_type'] == 'admin' ? \ManagerService::getManagerId() : $this->userId,
			'create_at'      => time(),
		];
		\DB::table('payment_log')->insertGetId($refundData);
		
		return true;
	}
	
	/**
	 * 微信退款
	 * @param array $data
	 * @author 李小同
	 * @date   2018-08-25 11:28:46
	 * @return bool
	 */
	private function _wechatRefund(array $data) {
		
		# todo lxt 微信退款
		
		return true;
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
		              ->offset(0)
		              ->limit($page * $perPage)
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
	 * 订单列表
	 * @param int $page
	 * @author 李小同
	 * @date   2018-8-3 18:01:24
	 * @return array
	 */
	public function getMyWashOrderListBak($page = 1) {
		
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
	 * 我的订单列表总页数
	 * @author 李小同
	 * @date   2018-08-18 10:58:54
	 * @return int
	 */
	public function getMyWashOrderTotalPage() {
		
		$perPage    = \SettingService::getValue('per_page');
		$totalCount = \DB::table('wash_order')->where('user_id', $this->userId)->count('order_id');
		
		return ceil($totalCount / $perPage);
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
			
			# 发送模板消息
			if (!empty($post['openid']) && !empty($post['form_id'])) {
				$orderData['openid']  = $post['openid'];
				$orderData['form_id'] = $post['form_id'];
				$this->sendAddOrderMsg($orderData);
			}
			
			# 给管理员发送邮件通知
			$to        = \SettingService::getValue('manager_email');
			$subject   = '新的洗车订单#'.$orderData['order_id'];
			$orderLink = route('washOrderList').'?filter_order_id='.$orderData['order_id'];
			$content   = '有新的洗车订单了，详情请点击以下链接：'.PHP_EOL.$orderLink;
			\ToolService::pushMailList($to, $subject, $content);
			
			$logger->info('success', $orderData);
			
			return $orderData['order_id'];
			
		} catch (\Exception $e) {
			
			$logger->error($e->getMessage(), [
				'error_code' => $e->getCode(),
				'file'       => $e->getFile(),
				'line'       => $e->getLine(),
			]);
			
			\DB::rollback();
			
			return false;
		}
	}
	
	/**
	 * 下单成功，发送模板消息
	 * @param array $orderData
	 * @author 李小同
	 * @date   2018-08-25 21:23:28
	 * @return mixed
	 */
	public function sendAddOrderMsg(array $orderData) {
		
		$washProduct = \DB::table('article')->where('id', $orderData['wash_product_id'])->first(['name']);
		$plate       = \DB::table('car')->where('id', $orderData['car_id'])->first(['plate_number']);
		
		$tplData = [
			'template_id' => config('project.WECHAT_MP.TPL_ID.ADD_ORDER'),
			'openid'      => $orderData['openid'],
			'form_id'     => $orderData['form_id'],
			'data'        => [
				'keyword1' => ['value' => $orderData['order_id']],
				'keyword2' => ['value' => $washProduct['name']],
				'keyword3' => ['value' => $orderData['wash_time']],
				'keyword4' => ['value' => $plate['plate_number']],
				'keyword5' => ['value' => currencyFormat($orderData['total'])],
				'keyword6' => ['value' => $orderData['address']],
				'keyword7' => ['value' => date('Y-m-d H:i:s', $orderData['create_at'])],
			],
		];
		
		$res = \WechatService::sendTplMsg($tplData);
		
		return $res;
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
	 * 获取联系人
	 * @author 李小同
	 * @date   2018-8-13 17:22:43
	 * @return array
	 */
	public function getContact() {
		
		$contact = [
			'user'  => \UserService::getUserInfo('nickname'),
			'phone' => \UserService::getUserInfo('phone'),
		];
		if ($this->userId > 0) {
			# 最新订单的联系人信息
			$lastContact = \DB::table('wash_order')
			                  ->where('user_id', $this->userId)
			                  ->orderBy('id', 'desc')
			                  ->first(['contact_user', 'contact_phone']);
			if ($lastContact) {
				$contact = [
					'user'  => $lastContact['contact_user'],
					'phone' => $lastContact['contact_phone'],
				];
			}
		}
		
		return $contact;
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
			case 'cancel_order':
				$this->_cancelWashOrder($order);
				break;
			case 'refund_order':
				$this->_refundWashOrder($order);
				break;
			case 'apply_refund':
				$this->_applyRefundWashOrder($order);
				break;
			default:
				json_msg(trans('error.illegal_action'), 40003);
		}
		return true;
	}
	
	/**
	 * 支付订单
	 * @param array $post
	 * @author 李小同
	 * @date   2018-08-21 9:35:42
	 * @return bool
	 */
	public function payOrder(array $post) {
		
		$orderId = $post['order_id'];
		$order   = $this->getWashOrder($orderId);
		
		if ($order['status'] != 1 || $order['payment_status'] == '1') {
			json_msg(trans('error.illegal_action'), 40003);
		}
		
		$paymentMethod = explode(',', $post['payment_method']);
		$balance       = \UserService::getBalance();
		if (in_array('balance', $paymentMethod)) {
			
			# 检测余额是否充足
			if (count($paymentMethod) == 1) {
				if ($balance < $order['total']) {
					json_msg(trans('common.balance_not_enough'), 50001);
				}
			} else { # 组合支付
				if ($balance <= 0) json_msg(trans('error.balance_not_enough'), 40003);
			}
		}
		
		\DB::beginTransaction();
		try {
			
			$action = 'pay_order';
			
			if (in_array('balance', $paymentMethod)) {
				
				if (count($paymentMethod) == 1) {
					$amount = $order['total'];
				} else {
					# 组合支付，若余额充足，仍选了组合支付，支付金额不得超过订单总金额
					$amount = min([$balance, $order['total']]);
				}
				
				# 余额使用记录
				$useBalanceData = [
					'amount'   => -floatval($amount),
					'type'     => $action,
					'order_id' => $orderId,
					'comment'  => '【支付订单】'.$orderId,
					'user_id'  => $this->userId,
				];
				$this->_addBalanceDetail($useBalanceData);
				
				# 支付记录
				$paymentData = [
					'order_id'       => $orderId,
					'payment_method' => 'balance',
					'amount'         => $amount,
				];
				$this->_addPaymentLog($paymentData);
			}
			
			# 修改订单状态
			$updateData = [
				'order_id'       => $orderId,
				'action'         => $action,
				'status'         => 2,
				'operator_type'  => 'user',
				'payment_status' => '1',
				'payment_method' => implode(',', $paymentMethod),
			];
			$this->_updateOrder($updateData);
			
			# 可能存在同时一个账号，多处登录并同时支付的情况，支付完成再查一次用户余额，如果余额小于0则回滚
			if (in_array('balance', $paymentMethod)) {
				$balance = \UserService::getBalance();
				if ($balance < 0) {
					\DB::rollback();
					json_msg(trans('common.balance_not_enough'), 50001);
				}
			}
			
			\DB::commit();
			return true;
			
		} catch (\Exception $e) {
			print_r($e->getMessage());
			print_r($e->getFile());
			print_r($e->getLine());
			
			\DB::rollback();
			return false;
		}
	}
	
	/**
	 * 用户取消订单
	 * @param array $order
	 * @param bool  $system 是否系统自动取消
	 * @author 李小同
	 * @date   2018-8-8 16:31:41
	 * @return bool
	 */
	private function _cancelWashOrder(array $order, $system = false) {
		
		if ($order['status'] == 1) {
			
			\DB::beginTransaction();
			try {
				
				$updateData = [
					'order_id'      => $order['order_id'],
					'action'        => 'cancel_order',
					'status'        => 7,
					'operator_type' => $system ? 'system' : 'user',
				];
				$this->_updateOrder($updateData);
				
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
	private function _refundWashOrder(array $order) {
		
		if ($order['status'] == 2) {
			
			\DB::beginTransaction();
			try {
				$this->refundOrder($order['order_id'], 'user');
				
				$updateData = [
					'order_id'      => $order['order_id'],
					'action'        => 'refund_order',
					'status'        => 6,
					'operator_type' => 'user',
				];
				$this->_updateOrder($updateData);
				
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
	 * 申请退款
	 * @param array $order
	 * @author 李小同
	 * @date   2018-08-17 14:52:10
	 * @return bool
	 */
	private function _applyRefundWashOrder(array $order) {
		
		if ($order['status'] == 3) {
			
			\DB::beginTransaction();
			try {
				
				$updateData = [
					'order_id'      => $order['order_id'],
					'action'        => 'apply_refund',
					'status'        => 8,
					'operator_type' => 'user',
				];
				$this->_updateOrder($updateData);
				
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
	 * 添加余额流水
	 * @param array $data
	 * @author 李小同
	 * @date   2018-08-21 15:26:38
	 */
	private function _addBalanceDetail(array $data) {
		
		$useBalanceData = [
			'user_id'   => $data['user_id'],
			'amount'    => $data['amount'],
			'type'      => $data['type'],
			'order_id'  => $data['order_id'],
			'comment'   => $data['comment'],
			'create_at' => time(),
			'create_ip' => getClientIp(true),
		];
		$detailId       = \DB::table('balance_detail')->insertGetId($useBalanceData);
		
		return $detailId;
	}
	
	/**
	 * 添加支付记录
	 * @param array $data
	 * @author 李小同
	 * @date   2018-08-21 15:23:04
	 */
	private function _addPaymentLog(array $data) {
		
		$paymentData = [
			'order_id'       => $data['order_id'],
			'payment_method' => $data['payment_method'],
			'amount'         => $data['amount'],
			'creator'        => $this->_getFormatUser(),
			'create_by'      => $this->userId,
			'create_at'      => time(),
		];
		$logId       = \DB::table('payment_log')->insertGetId($paymentData);
		
		return $logId;
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