<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller {
	
	# 个人中心
	public function info() {
		
		if (is_weixin()) {
			
			if (!empty($_GET['code'])) {
				$code = $_GET['code'];
				$res  = \WechatService::getAccessTokenAndOpenId($code);
				
				if (!empty($res['openid'])) {
					# 获取到openid后，检测之前有无注册过
					$userId = \UserService::checkExistIdentity('wechat', $res['openid']);
					if (!$userId) {
						$userId = $this->_bindUserId($res['access_token'], $res['openid']);
					} else {
						# 登录
						$userInfo = \UserService::handleLogin($userId);
						
						setcookie('token', $userInfo['token']);
					}
					
					$userInfo = \UserService::getUserInfo($userId);
					
					return view('wechat/user/info', compact('userInfo'));
				}
				
			} else {
				\WechatService::getCode('user/info', 'snsapi_base');
//				\WechatService::getCode('user/info', 'snsapi_userinfo');
			}

//			if (!empty($_GET['code'])) {
//				$code = $_GET['code'];
//				\WechatService::getAccessTokenAndOpenId($code);
//			} else {
//				\WechatService::getCode('user/info', 'snsapi_base');
//			}
			
		}
	}
	
	/**
	 * # 绑定微信
	 * @param $accessToken
	 * @param $openid
	 * @author 李小同
	 * @date   2018-7-8 15:30:11
	 * @return 绑定的user_id
	 */
	private function _bindUserId($accessToken, $openid) {
		
		$wechatUserInfo = \WechatService::getUserInfo($accessToken, $openid);
		
		$authData = [
			'identityType' => 'wechat',
			'identity'     => $openid,
		];
		$userData = [
			'nickname' => $wechatUserInfo['nickname'],
			'gender'   => $wechatUserInfo['sex'],
			'avatar'   => $wechatUserInfo['headimgurl'],
			'language' => $wechatUserInfo['language'],
			'city'     => $wechatUserInfo['city'],
			'province' => $wechatUserInfo['province'],
			'country'  => $wechatUserInfo['country'],
		];
		$userId   = \UserService::create($authData, $userData);
		
		return $userId;
	}
}
