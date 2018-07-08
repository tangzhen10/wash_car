<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-07-08 11:06
 */

namespace App\Services;

class WechatService {
	
	/**
	 * 基础支持access_token
	 * 该access_token用于调用其他接口
	 * @author 李小同
	 * @date   2018-7-8 09:04:30
	 */
	public function baseAccessToken() {
		
		$url              = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('APPID').'&secret='.env('APPSECRET');
		$resJson          = file_get_contents($url);
		$res              = json_decode($resJson, 1);
		$res['create_at'] = time(); # 记录当前时间戳
		$res              = json_encode($res);
		file_put_contents('access_token', $res);
		echo $res;
		die;
	}
	
	/**
	 * 获取code
	 * @param string $scope
	 * 参数    是否必须    说明
	 * appid    是    公众号的唯一标识
	 * redirect_uri    是    授权后重定向的回调链接地址， 请使用 urlEncode 对链接进行处理
	 * response_type    是    返回类型，请填写code
	 * scope    是    应用授权作用域，snsapi_base （不弹出授权页面，直接跳转，只能获取用户openid），snsapi_userinfo （弹出授权页面，可通过openid拿到昵称、性别、所在地。并且， 即使在未关注的情况下，只要用户授权，也能获取其信息 ）
	 * state    否    重定向后会带上state参数，开发者可以填写a-zA-Z0-9的参数值，最多128字节
	 * #wechat_redirect    是    无论直接打开还是做页面302重定向时候，必须带此参数
	 * 如果用户同意授权，页面将跳转至 redirect_uri/?code=CODE&state=STATE。
	 * @author 李小同
	 * @date   2018-7-8 09:04:26
	 * @return string
	 */
	public function getCode($url, $scope = 'snsapi_base') {
		
		# 若提示“该链接无法访问”，请检查参数是否填写错误，是否拥有scope参数对应的授权作用域权限。
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.env('APPID').'&redirect_uri='.urlencode(env('REDIRECT_URI').'/'.$url).'&response_type=code&scope='.$scope.'&state=113#wechat_redirect';
		header('Location:'.$url);
	}
	
	/**
	 * 通过code换取网页授权access_token
	 * @author 李小同
	 * @date   2018-7-8 09:15:09
	 */
	public function getAccessTokenAndOpenId($code) {
		
		$url              = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('APPID').'&secret='.env('APPSECRET').'&code='.$code.'&grant_type=authorization_code';
		$resJson          = file_get_contents($url);
		$res              = json_decode($resJson, 1);
		$res['create_at'] = time();
		$key              = sprintf(config('cache.WECHAT.ACCESS_TOKEN'), $res['openid']);
		redisSet($key, $res);
		
		return $res;
	}
	
	/**
	 * 由于access_token拥有较短的有效期，当access_token超时后，可以使用refresh_token进行刷新，refresh_token有效期为30天，当refresh_token失效之后，需要用户重新授权。
	 * @author 李小同
	 * @date
	 */
	public function refreshAccessToken($refreshToken) {
		
		$url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.env('APPID').'&grant_type=refresh_token&refresh_token='.$refreshToken;
	}
	
	public function getUserInfo($accessToken, $openid, $lang = 'zh_CN') {
		
		$url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$accessToken.'&openid='.$openid.'&lang='.$lang;
		$resJson          = file_get_contents($url);
		$res              = json_decode($resJson, 1);
		$key              = sprintf(config('cache.WECHAT.USER_INFO'), $res['openid']);
		redisSet($key, $res);
		
		return $res;
	}
	
	public function checkAccessToken($accessToken, $openid) {
		
		$url = 'https://api.weixin.qq.com/sns/auth?access_token='.$accessToken.'&openid='.$openid;
	}
	
}