<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller {
	
	# 个人中心
	public function info() {
		
		if (is_weixin()) {
			
			if (!empty($_GET['code'])) {
				$code     = $_GET['code'];
				$res      = \WechatService::getAccessTokenAndOpenId($code);
				$userInfo = \WechatService::getUserInfo($res['access_token'], $res['openid']);
				print_r($userInfo);
				if (!empty($userInfo)) {
					echo '<img src="'.$userInfo['headimgurl'].'"/>';
				}
				
				# 绑定微信
				$authData = [
					'identityType' => 'wechat',
					'identity'     => $userInfo['openid'],
				];
				$userData = [
					'nickname' => $userInfo['nickname'],
					'gender'   => $userInfo['sex'],
					'avatar'   => $userInfo['headimgurl'],
					'language' => $userInfo['language'],
					'city'     => $userInfo['city'],
					'province' => $userInfo['province'],
					'country'  => $userInfo['country'],
				];
				$userId   = \UserService::create($authData, $userData);
				echo $userId;die;
				
			} else {
				\WechatService::getCode('user/info', 'snsapi_userinfo');
			}

//			if (!empty($_GET['code'])) {
//				$code = $_GET['code'];
//				\WechatService::getAccessTokenAndOpenId($code);
//			} else {
//				\WechatService::getCode('user/info', 'snsapi_base');
//			}
			
		}
	}
}
