<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller {
	
	public function info() {
		
		echo '这里是用户个人中心的web页面';
		
		if (is_weixin()) {
			
			if ($_GET['code']) {
				$code = $_GET['code'];
				\WechatService::getAccessTokenAndOpenId($code);
			} else {
				\WechatService::getCode('user/info', 'snsapi_base');
			}
			
		}
	}
}
