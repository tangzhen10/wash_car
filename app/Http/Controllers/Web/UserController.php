<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller {
	
	# 个人中心
	public function info() {
		
		$pageTitle = '个人中心';
		
		if (is_wechat()) {
			
			$flagLogin = false;
			
			$token = isset($_COOKIE['token']) ? $_COOKIE['token'] : '';
			if ($token) {
				$cacheKey = sprintf(config('cache.USER_INFO'), $token);
				$userInfo = redisGet($cacheKey);
				if (is_array($userInfo) && !empty($userInfo['user_id'])) $flagLogin = true;
			}
			
			if (!$flagLogin) {
				
				$refreshToken = isset($_COOKIE['refresh_token']) ? $_COOKIE['refresh_token'] : '';
				if ($refreshToken) {
					$res = \WechatService::refreshAccessToken($refreshToken);
				} elseif (!empty($_GET['code'])) {
					$code = $_GET['code'];
					$res  = \WechatService::getAccessTokenAndOpenId($code);
				} else {
					\WechatService::getCode('user/info', 'snsapi_base');
				}
				
				if (!empty($res['openid'])) {
					# 获取到openid后，检测之前有无注册过
					$userId = \UserService::checkExistIdentity('wechat', $res['openid']);
					
					if (!$userId) { # 微信自动注册
						$userId = $this->_bindUserId($res['access_token'], $res['openid']);
					}
					
					# 登录
					$userInfo = \UserService::handleLogin($userId, ['wechat' => $res]);
					
					setcookie('token', $userInfo['token'], time() + 7000, '/');
					setcookie('refresh_token', $res['refresh_token'], time() + 29*24*3600, '/');
					
					$userInfo = \UserService::getUserInfoFromDB($userId);
				} else {
					return redirect()->route(\Route::currentRouteName());
				}
			}
			
			return view('wechat/user/info', compact('userInfo', 'pageTitle'));
		} else {
			echo '非微信浏览器，个人中心待完成';
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
			'gender'   => strval($wechatUserInfo['sex']),
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
