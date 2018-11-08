<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-08-19 14:25
 */

namespace App\Services;

class CardService extends BaseService {
	
	public $module = 'card';
	
	# region 后台
	/**
	 * 获取洗车卡列表
	 * @author 李小同
	 * @date   2018-08-19 14:31:36
	 * @return mixed
	 */
	public function getList() {
		
		$fields = [
			'id',
			'name',
			'price',
			'price_ori',
			'expire_days',
			'use_times',
			'hot_status',
			'create_at',
			'status',
		];
		$list   = \DB::table($this->module)->where('status', '!=', '-1')->get($fields)->toArray();
		$this->addStatusText($list);
		foreach ($list as &$item) {
			$item['price']      = currencyFormat($item['price']);
			$item['price_ori']  = currencyFormat($item['price_ori']);
			$item['create_at']  = intToTime($item['create_at']);
			$item['hot_status'] = $item['hot_status'] == '1' ? trans('common.yes') : trans('common.no');
		}
		unset($item);
		
		return $list;
	}
	
	/**
	 * 初始化的数据，用于填充新增数据表单默认值
	 * @author 李小同
	 * @date   2018-08-19 14:54:38
	 * @return array
	 */
	public function initDetail() {
		
		$detail = [
			'id'           => 0,
			'name'         => '',
			'price'        => '',
			'price_ori'    => '',
			'expire_days'  => '',
			'use_times'    => '',
			'hot_status'   => '0',
			'introduction' => '',
			'background'   => '',
		];
		
		return $detail;
	}
	
	/**
	 * 预处理请求数据
	 * @param array $data
	 * @author 李小同
	 * @date   2018-08-19 16:05:09
	 */
	public function handleFormData(array &$data) {
		
		$name = 'background';
		if (!empty($data['uploadfile_'.$name])) {
			if (empty($data[$name]) || $data[$name] == [null]) {
				$value = $data['uploadfile_'.$name];
			} else {
				$files = \Request::file($name);
				$value = ToolService::uploadFiles($files);
			}
			$data[$name] = $value;
		}
		unset($data['uploadfile_'.$name]);
		
		if ($data['id'] == 0) $data['create_at'] = time();
	}
	# endregion
	
	# region 前台
	/**
	 * 获取可售卡券
	 * @param array $cardIds
	 * @author 李小同
	 * @date   2018-08-19 16:11:33
	 * @return mixed
	 */
	public function getEnableCardList(array $cardIds = []) {
		
		$fields = [
			'a.id',
			'a.name',
			'a.wash_product_id',
			'b.name AS wash_product',
			'a.price',
			'a.price_ori',
			'a.expire_days',
			'a.use_times',
			'a.hot_status',
			'a.introduction',
			'a.background',
			'a.status',
		];
		$list   = \DB::table('card AS a')->join('article AS b', function ($join) {
			
			$productContentType = \SettingService::getValue('product_content_type');
			$join->on('b.id', '=', 'a.wash_product_id')->where('b.content_type', $productContentType);
		});
		if ($cardIds) $list = $list->whereIn('a.id', $cardIds);
		$list = $list->where('a.status', '1')->get($fields)->toArray();
		
		foreach ($list as &$item) {
			$item['background'] = \URL::asset($item['background']);
		}
		unset($item);
		
		return $list;
	}
	
	/**
	 * 获取我的卡券
	 * @param $status int 0失效 1生效 2全部
	 * @author 李小同
	 * @date   2018-08-19 20:25:34
	 * @return array
	 */
	public function getMyCards($status = 2) {
		
		$fields  = ['card_id', 'effect_from', 'use_times'];
		$myCards = \DB::table('user_card')
		              ->where('user_id', $this->userId)
		              ->where('status', '1')
		              ->get($fields)
		              ->toArray();
		if (empty($myCards)) return [];
		
		$cardListArr = $this->getEnableCardList(array_column($myCards, 'card_id'));
		$cardList    = [];
		foreach ($cardListArr as $item) $cardList[$item['id']] = $item;
		
		foreach ($myCards as $item) {
			
			# 有效期
			$effectFrom = date('Y-m-d H:i:s', $item['effect_from']);
			$expire     = $cardList[$item['card_id']]['expire_days'] * 86400 - 1;
			$expireAt   = strtotime($effectFrom) + $expire;
			
			$cardList[$item['card_id']]['effect_from'] = $effectFrom;
			$cardList[$item['card_id']]['expire_at']   = date('Y-m-d H:i:s', $expireAt);
			$cardList[$item['card_id']]['left_times']  = $cardList[$item['card_id']]['use_times'] - $item['use_times'];
			
			# 1是有效，2是未生效，3是已使用，4是过期
			$effectStatus = 1;
			if ($cardList[$item['card_id']]['effect_from'] > time()) $effectStatus = 2;
			if ($expireAt < time()) $effectStatus = 4; # 过期 优先级高于 未生效
			if ($cardList[$item['card_id']]['left_times'] <= 0) $effectStatus = 3; # 已使用 优先级高于 过期
			$cardList[$item['card_id']]['effect_status'] = $effectStatus;
			
			if ($status == 1) {
				if ($effectStatus != 1) unset($cardList[$item['card_id']]);
			} elseif ($status == 0) {
				if ($effectStatus == 1) unset($cardList[$item['card_id']]);
			}
		}
		$cardList = array_values($cardList);
		
		# 按过期时间倒序排序
		if (!empty($cardList)) array_multisort(array_column($cardList, 'expire_at'), SORT_DESC, $cardList);
		
		return $cardList;
	}
	
	public function buyCard(array $post) {
		
		$cardId        = $post['key_id'];
		$paymentMethod = explode(',', trim($post['payment_method']));

//		if (in_array('balance', $paymentMethod)) {
//
//			# 检测余额是否充足
//			$balance = \UserService::getBalance();
//
//			if (count($paymentMethod) == 1) {
//				if ($balance < $detail['price']) {
//					json_msg(trans('common.balance_not_enough'), 50001);
//				}
//			} else { # 组合支付
//				if ($balance <= 0) json_msg(trans('error.balance_not_enough'), 40003);
//			}
//		}
		
		# 微信支付，拉起支付界面
		if (in_array('wechat', $paymentMethod)) \WechatService::unifiedOrderForCard($post);
	}
	
	/**
	 * 购卡，放入用户卡包中
	 * @param array $order
	 * @author 李小同
	 * @date   2018-11-08 15:07:09
	 * @return bool
	 */
	public function realBuyCard(array $order) {
		
		$paymentMethod = explode(',', $order['payment_method']);
		$orderId       = $order['order_id'];
		
		if (in_array('balance', $paymentMethod)) {
			
			# 检测余额是否充足
			$balance = \UserService::getBalance($order['user_id']);
			
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
					'type'     => 'buy_card',
					'order_id' => $orderId,
					'comment'  => '【购买卡券】'.$orderId,
					'user_id'  => $order['user_id'],
				];
				\PaymentService::addBalanceDetail($useBalanceData);
			}
			
			# 修改订单状态
			\DB::table('card_order')->where('order_id', $orderId)->upate(['status' => '1']);
			
			# 卡券放入卡包
			$now = time();
			\DB::table('user_card')->insert([
				'user_id'     => $order['user_id'],
				'card_id'     => $order['card_id'],
				'effect_from' => $now,
				'create_at'   => $now,
			]);
			
			# 可能存在同时一个账号，多处登录并同时支付的情况，支付完成再查一次用户余额，如果余额小于0则回滚
			if (in_array('balance', $paymentMethod)) {
				$balance = \UserService::getBalance($order['user_id']);
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
	 * 创建购卡订单
	 * @param array $post
	 * @author 李小同
	 * @date   2018-11-08 14:48:47
	 * @return mixed
	 */
	public function createCardOrder(array $post) {
		
		$orderData = [
			'order_id'       => \OrderServie::getOrderId(),
			'user_id'        => $this->userId,
			'card_id'        => $post['key_id'],
			'total'          => $post['total'],
			'payment_method' => $post['payment_method'],
			'create_at'      => time(),
		];
		\DB::table('card_order')->insert($orderData);
		
		return $orderData['order_id'];
	}
	
	/**
	 * 购卡订单详情
	 * @param $orderId
	 * @author 李小同
	 * @date   2018-11-08 14:52:41
	 * @return mixed
	 */
	public function cardOrderDetail($orderId) {
		
		$fields = ['order_id', 'card_id', 'user_id', 'total', 'status', 'payment_method'];
		$order  = \DB::table('card_order')->where('order_id', $orderId)->first($fields);
		
		return $order;
	}
	
	/**
	 * 下单时检测可用卡券
	 * @param $orderId
	 * @author 李小同
	 * @date   2018-08-28 21:02:58
	 * @return bool true：使用卡券成功 false：无卡券使用
	 */
	public function useCard($orderId) {
		
		$orderInfo = \DB::table('wash_order')->where('order_id', $orderId)->first(['wash_product_id', 'total']);
		
		$myCards = $this->getMyCards(1);
		
		# 优先使用最先要过期的卡券
		if (!empty($myCards)) array_multisort(array_column($myCards, 'expire_at'), SORT_ASC, $myCards);
		
		foreach ($myCards as $myCard) {
			
			if ($myCard['wash_product_id'] != $orderInfo['wash_product_id']) continue;
			
			# 添加使用卡券记录
			$now     = time();
			$logData = [
				'card_id'   => $myCard['id'],
				'user_id'   => $this->userId,
				'order_id'  => $orderId,
				'create_at' => $now,
			];
			\DB::table('card_use_log')->insert($logData);
			
			# 更新用户卡券使用次数
			\DB::table('user_card')
			   ->where('card_id', $myCard['id'])
			   ->where('user_id', $this->userId)
			   ->increment('use_times');
			
			# 支付记录
			$paymentData = [
				'order_id'       => $orderId,
				'payment_method' => 'card',
				'amount'         => $orderInfo['total'],
			];
			\PaymentService::addPaymentLog($paymentData);
			
			$updateData = ['status' => 2, 'payment_status' => 1, 'payment_method' => 'card'];
			\DB::table('wash_order')->where('order_id', $orderId)->update($updateData);
			
			# 订单日志
			$logData = [
				'wash_order_id' => $orderId,
				'action'        => 'pay_order',
				'order_status'  => 2,
				'operator_type' => 'user',
			];
			\OrderService::addOrderLog($logData);
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * 恢复卡券使用记录
	 * @param $orderId
	 * @author 李小同
	 * @date   2018-08-28 21:37:59
	 * @return bool
	 */
	public function rollbackCard($orderId) {
		
		$user   = \DB::table('wash_order')->where('order_id', $orderId)->first(['user_id']);
		$userId = $user['user_id'];
		
		$usedCard   = \DB::table('card_use_log')->where('order_id', $orderId)->where('status', '1')->first(['card_id']);
		$usedCardId = $usedCard['card_id'];
		
		# 撤销卡券使用效果
		\DB::table('card_use_log')->where('order_id', $orderId)->update(['status' => '0', 'update_at' => time()]);
		
		# 恢复用户卡券使用次数
		\DB::table('user_card')->where('card_id', $usedCardId)->where('user_id', $userId)->decrement('use_times');
		
		return true;
	}
	# endregion
	
}