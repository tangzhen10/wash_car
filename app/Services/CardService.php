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
			'expire_date',
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
			'expire_date'  => '',
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
			'a.expire_date',
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
	 * @author 李小同
	 * @date   2018-08-19 20:25:34
	 * @return array
	 */
	public function getMyCards() {
		
		$fields      = ['card_id', 'effect_from', 'use_times'];
		$myCards     = \DB::table('user_card')
		                  ->where('user_id', $this->userId)
		                  ->where('status', '1')
		                  ->get($fields)
		                  ->toArray();
		$cardListArr = $this->getEnableCardList(array_column($myCards, 'card_id'));
		$cardList    = [];
		foreach ($cardListArr as $item) {
			$cardList[$item['id']] = $item;
		}
		
		foreach ($myCards as $item) {
			
			# 有效期
			$effectFrom = date('Y-m-d 00:00:00', $item['effect_from']);
			$expire     = $cardList[$item['card_id']]['expire_date'] * 86400 - 1;
			$expireAt   = strtotime($effectFrom) + $expire;
			
			$cardList[$item['card_id']]['expire_at']  = date('Y-m-d H:i:s', $expireAt);
			$cardList[$item['card_id']]['left_times'] = $cardList[$item['card_id']]['use_times'] - $item['use_times'];
		}
		
		return array_values($cardList);
	}
	# endregion
	
}