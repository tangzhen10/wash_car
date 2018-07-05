<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-07-03 8:54
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ManagerService;

class BaseController extends Controller {
	
	protected $manager = null; # 管理员
	protected $data = []; # 模板渲染用的数据
	
	public function __construct() {
		
		$this->manager         = new ManagerService();
		$this->data['manager'] = $this->manager->getManagerInfoByManagerId();
	}
	
	/**
	 * 修改状态
	 * @author 李小同
	 * @date   2018-7-4 09:16:25
	 */
	protected function changeStatus() {
		
		$id     = \Request::input('id');
		$status = \Request::input('status');
		$res    = $this->manager->changeStatus($id, $status, static::TABLE);
		$res ? json_msg('ok') : json_msg(trans('common.action_failed'), 40004);
	}
	
	/**
	 * 统一返回固定格式的json
	 * @param $res
	 * @author 李小同
	 * @date   2018-7-4 17:09:42
	 */
	public function render($res) {
		
		if ($res) {
			json_msg('ok');
		} else {
			json_msg(trans('common.action_failed'), 40004);
		}
	}
	
}