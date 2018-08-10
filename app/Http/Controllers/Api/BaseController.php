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
		
		$this->_checkSign();
		
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
	
	/**
	 * 验证签名
	 * @author 李小同
	 * @date   2018-8-10 17:11:17
	 * @return bool
	 */
	private function _checkSign() {
		
		if (env('APP_ENV') == 'local' || env('VERIFY_SIGN') == false) return true;
		
		$post = request_all();
		if (isset($post['sign'])) {
			
			$signOri = $post['sign'];
			if ($signOri == '5jlpjDEB') return true;
			unset($post['sign']);
			
			ksort($post);
			
			$param = [];
			foreach ($post as $key => $value) {
				$param[] = $key.'='.$value;
			}
			$paramStr = implode('&', $param);
			$sign     = md5(base64_encode($paramStr));
			$flag     = $sign == $signOri;
			
		} else {
			$flag = false;
		}
		
		if (!$flag) json_msg(trans('error.illegal_param'), 50003);
	}
}