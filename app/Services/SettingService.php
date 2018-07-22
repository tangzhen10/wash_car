<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-07-22 12:59
 */

namespace App\Services;

class SettingService extends BaseService {
	
	public $module = 'setting';
	
	/**
	 * 获取前台设置
	 * @author 李小同
	 * @date   2018-7-22 15:17:52
	 * @return mixed
	 */
	public function getMainList() {
		
		$list = \DB::table($this->module)->where('group', 'main')->get()->toArray();
		
		return $list;
	}
	
	/**
	 * 获取后台设置
	 * @author 李小同
	 * @date   2018-7-22 15:18:11
	 * @return mixed
	 */
	public function getAdminList() {
		
		$list = \DB::table($this->module)->where('group', 'admin')->get()->toArray();
		
		return $list;
	}
	
	/**
	 * 获取系统设置
	 * @param string $name
	 * @author 李小同
	 * @date   2018-7-22 15:05:07
	 * @return bool
	 */
	public function getValue($name = '') {
		
		$cacheKey    = config('cache.SETTING');
		$settingList = redisGet($cacheKey);
		if ($settingList === false) {
			
			$settingRows = \DB::table($this->module)->get(['name', 'value'])->toArray();
			foreach ($settingRows as $setting) {
				$settingList[$setting['name']] = $setting['value'];
			}
			redisSet($cacheKey, $settingList);
		}
		return isset($settingList[$name]) ? $settingList[$name] : false;
	}
	
	/**
	 * 更新操作后执行的函数
	 * @author 李小同
	 * @date   2018-7-22 15:10:43
	 */
	public function handleAfterUpdate() {
		
		$cacheKey = config('cache.SETTING');
		return redisDel($cacheKey);
	}
	
	public function getTotalInfo() {
		
		# 版块
		$categoryInfo = \DB::table('content_type')->where('type', '1')->where('status', '1')->pluck('id');
		$tabInfo      = \DB::table('article')
		                   ->whereIn('content_type', $categoryInfo)
		                   ->where('status', '1')
		                   ->pluck('id');
		$productInfo  = \DB::table('article')
		                   ->where('content_type', \SettingService::getValue('product_content_type'))
		                   ->where('status', '1')
		                   ->pluck('id');
		$managerInfo  = \DB::table('manager')->where('status', '1')->pluck('id');
		
		$total = [
			'category' => count($categoryInfo),
			'tab'      => count($tabInfo),
			'product'  => count($productInfo),
			'manager'  => count($managerInfo),
		];
		
		return $total;
	}
}