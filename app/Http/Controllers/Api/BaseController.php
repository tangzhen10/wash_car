<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-07-03 8:56
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;

class BaseController extends Controller {
	
	protected $user = null; # 用户
	
	public function __construct() {
		
		$this->user = new UserService();
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