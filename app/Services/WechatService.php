<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-07-08 11:06
 */

namespace App\Services;

class WechatService {
	
	# region 公众号
	
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
		die;
	}
	
	/**
	 * 通过code换取网页授权access_token
	 * @author 李小同
	 * @date   2018-7-8 09:15:09
	 */
	public function getAccessTokenAndOpenId($code) {
		
		$url     = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('APPID').'&secret='.env('APPSECRET').'&code='.$code.'&grant_type=authorization_code';
		$resJson = file_get_contents($url);
		$res     = json_decode($resJson, 1);
		if (empty($res['openid'])) return [];
		
		$res['create_at'] = time();
		$key              = sprintf(config('cache.WECHAT.ACCESS_TOKEN'), $res['openid']);
		redisSet($key, $res);
		
		return $res;
	}
	
	/**
	 * @param $refreshToken
	 * 由于access_token拥有较短的有效期，当access_token超时后，可以使用refresh_token进行刷新，refresh_token有效期为30天，当refresh_token失效之后，需要用户重新授权。
	 * @author 李小同
	 * @date   2018-7-9 20:47:52
	 * @return array $res
	 */
	public function refreshAccessToken($refreshToken) {
		
		$url     = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.env('APPID').'&grant_type=refresh_token&refresh_token='.$refreshToken;
		$resJson = file_get_contents($url);
		$res     = json_decode($resJson, 1);
		if (empty($res['openid'])) return [];
		
		$res['create_at'] = time();
		$key              = sprintf(config('cache.WECHAT.ACCESS_TOKEN'), $res['openid']);
		redisSet($key, $res);
		
		return $res;
	}
	
	/**
	 * 获取微信用户信息
	 * @param        $accessToken
	 * @param        $openid
	 * @param string $lang
	 * @author 李小同
	 * @date   2018-7-9 20:48:25
	 * @return mixed
	 */
	public function getUserInfo($accessToken, $openid, $lang = 'zh_CN') {
		
		$url     = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$accessToken.'&openid='.$openid.'&lang='.$lang;
		$resJson = file_get_contents($url);
		$res     = json_decode($resJson, 1);
		$key     = sprintf(config('cache.WECHAT.USER_INFO'), $res['openid']);
		redisSet($key, $res);
		
		return $res;
	}
	
	/*
	 * 附：检验授权凭证（access_token）是否有效
		
		请求方法
		
		http：GET（请使用https协议） https://api.weixin.qq.com/sns/auth?access_token=ACCESS_TOKEN&openid=OPENID
		参数说明
		
		参数	描述
		access_token	网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同
		openid	用户的唯一标识
		返回说明
		正确的JSON返回结果：
		
		{ "errcode":0,"errmsg":"ok"}
		错误时的JSON返回示例：
		
		{ "errcode":40003,"errmsg":"invalid openid"}
	 */
	public function checkAccessToken($accessToken, $openid) {
		
		$url = 'https://api.weixin.qq.com/sns/auth?access_token='.$accessToken.'&openid='.$openid;
	}
	# endregion
	
	# region 小程序
	/**
	 * 通过code换取用户openid
	 * @param string $code 小程序端请求wx.login获得的code
	 * @author 李小同
	 * @date   2018-8-9 18:03:26
	 * @return array|mixed
	 */
	public function getMpOpenId($code = '') {
		
		$url     = 'https://api.weixin.qq.com/sns/jscode2session?appid='.env('MP_APP_ID').'&secret='.env('MP_APP_SECRET').'&js_code='.$code.'&grant_type=authorization_code';
		$resJson = file_get_contents($url);
		$res     = json_decode($resJson, 1);
		if (empty($res['openid'])) return [];
		
		$res['create_at'] = time();
		$key              = sprintf(config('cache.WECHAT_MP.SESSION_KEY'), $res['openid']);
		redisSet($key, $res);
		
		return $res;
	}
	
	/**
	 * 获取小程序access_token
	 * 有效期2h
	 * @author 李小同
	 * @date   2018-08-25 14:37:03
	 * @return mixed
	 */
	public function getMpAccessToken() {
		
		$key = config('cache.WECHAT_MP.ACCESS_TOKEN');
		$res = redisGet($key);
		if (empty($res)) {
			
			$url     = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('MP_APPID').'&secret='.env('MP_APPSECRET');
			$resJson = file_get_contents($url);
			$res     = json_decode($resJson, 1);
			if (empty($res['access_token'])) return '';
			
			$res['create_at'] = time();
			$key              = config('cache.WECHAT_MP.ACCESS_TOKEN');
			redisSet($key, $res);
		}
		
		return $res['access_token'];
	}
	
	/**
	 * 发送模板消息
	 * @param array $data
	 * @author 李小同
	 * @date   2018-08-25 15:08:58
	 * @return mixed
	 */
	public function sendTplMsg(array $data) {
		
		$accessToken = $this->getMpAccessToken();
		$url         = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$accessToken;
		$body        = [
			'template_id' => $data['template_id'],
			'touser'      => $data['openid'],
			'form_id'     => $data['form_id'],
			'url'         => empty($data['url']) ? '' : $data['url'],
			'page'        => 'pages/index/index',
			'data'        => $data['data'],
		];
		if (isset($data['emphasis_keyword'])) $body['emphasis_keyword'] = $data['emphasis_keyword'].'.DATA';
		$res = https_curl_json($url, $body);
		
		return $res;
	}

# endregion
	# region 微信支付
	/**
	 * 微信统一下单接口
	 * @param $openid
	 * @param $orderId
	 * @author 李小同
	 * @date   2018-11-04 21:44:35
	 */
	public function unifiedOrder($openid, $orderId) {
		
		$orderInfo = \OrderService::getWashOrder($orderId);
		
		$param           = [];
		$param['appid']  = env('MP_APP_ID');
		$param['attach'] = ''; # 非必填
		$param['body']   = $orderInfo['wash_product'];
		$param['mch_id'] = env('MCH_ID');
		$param['detail'] = '{ "goods_detail":[ { "goods_id":"iphone6s_16G", "wxpay_goods_id":"1001", "goods_name":"iPhone6s 16G", "quantity":1, "price":528800, "goods_category":"123456", "body":"apple" }] }';
		// $param['device_info']  = ''; # 非必填
		$param['nonce_str']        = md5(uniqid());
		$param['notify_url']       = 'https://www.yexingxia2018.com/';
		$param['openid']           = $openid;
		$param['out_trade_no']     = $orderId;
		$param['spbill_create_ip'] = getClientIp();
		$param['time_start']       = date('YmdHis', $orderInfo['create_at']); # 交易起始时间，非必填
		$param['time_expire']      = date('YmdHis', $orderInfo['create_at'] + 3570); # 交易结束时间，非必填
		$param['total_fee']        = $orderInfo['total'] * 100; # 订单总金额，单位为分
		$param['trade_type']       = 'JSAPI';
//		$param['sign_type']        = 'MD5';
//		$param['fee_type']         = 'CNY'; # 默认CNY，非必填
		$param['total_fee'] = '1';
//		$param['goods_tag']        = ''; # 非必填
//		$param['product_id'] = '';
		
		$sign = $this->getSign($param);
		
		$tplPost = <<<EOL
<xml>
   <appid>%s</appid>
   <attach>%s</attach>
   <body>%s</body>
   <mch_id>%s</mch_id>
   <detail>%s</detail>
   <nonce_str>%s</nonce_str>
   <notify_url>%s</notify_url>
   <openid>%s</openid>
   <out_trade_no>%s</out_trade_no>
   <spbill_create_ip>%s</spbill_create_ip>
   <time_start>%s</time_start>
   <time_expire>%s</time_expire>
   <total_fee>%s</total_fee>
   <trade_type>%s</trade_type>
   <sign>%s</sign>
</xml>
EOL;
		$postStr = sprintf($tplPost, $param['appid'], $param['attach'], $param['body'], $param['mch_id'], $param['detail'], $param['nonce_str'], $param['notify_url'], $param['openid'], $param['out_trade_no'], $param['spbill_create_ip'], $param['time_start'], $param['time_expire'], $param['total_fee'], $param['trade_type'], $sign);
		
		$url  = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
		$xml  = request_post($url, $postStr);
		$resp = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		
		$resp['timestamp'] = strval(time());
		
		$data            = [
			'appId'     => $resp['appid'],
			'nonceStr'  => $resp['nonce_str'],
			'package'   => 'prepay_id='.$resp['prepay_id'],
			'signType'  => 'MD5',
			'timeStamp' => $resp['timestamp'],
		];
		$resp['paySign'] = $this->getSign($data);
		
		json_msg($resp);
	}
	# endregion
	
	/**
	 * 微信支付签名生成
	 * @param $data
	 * @author 李小同
	 * @date   2018-11-04 22:03:17
	 * @return string
	 */
	public function getSign($data) {
		
		//签名步骤一：按字典序排序参数
		ksort($data);
		
		$string = '';
		foreach ($data as $k => $v) {
			if ($k != 'sign' && $v != '' && !is_array($v)) {
				$string .= $k.'='.$v.'&';
			}
		}
		$string = trim($string, '&');
		//签名步骤二：在string后加入KEY
		$string = $string.'&key='.env('MCH_SIGN_KEY');
		//签名步骤三：MD5加密
		$string = md5($string);
		//签名步骤四：所有字符转为大写
		$result = strtoupper($string);
		
		return $result;
	}
}