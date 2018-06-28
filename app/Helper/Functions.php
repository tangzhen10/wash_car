<?php /**
 * 全局函数
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: lxt
 * Date: 2018-01-03 0003 21:52
 */
//use Illuminate\Support\Facades\Redis;

/**
 * 获取http header
 * @author xiaotong.li
 * @date   2018-1-18 17:00:24
 * @return array
 */
function get_http_headers() {
	
	$headers = [];
	foreach ($_SERVER as $key => $value) {
		if (substr($key, 0, 5) == 'HTTP_') {
			$key           = substr($key, 5);
			$key           = strtolower($key);
			$headers[$key] = $value;
			continue;
		}
	}
	return $headers;
}

/**
 * 渲染页面，或输出格式化json数据
 * @param array|string $data 数组结果或错误信息
 * @param int          $code 状态码 0 正常 > 0 错误状态
 * @author 李小同
 * @date   2017-9-23 11:17:10
 * @return null|view
 */
function json_msg($data = '', $code = 0) {
	
	header('charset:utf-8');
	header('X-power-by:ahulxt');
	header('Content-Type:text/json');
	
	$result = ['code' => $code];
	
	if ($code > 0) {
		if (!empty($data)) {
			$result['error'] = (string)$data;
		} else {
			$result['error'] = trans('error.error_'.$code);
		}
	} else {
		if (is_array($data)) {
			$result['data'] = $data;
		} elseif (is_string($data)) {
			$result['msg'] = $data;
		}
	}
	echo json_encode($result);
	die;
}

/**
 * 创建盐
 * @author 李小同
 * @date   2017-9-24 00:13:43
 * @return string md5加密
 */
function create_salt() {
	
	$str = microtime();
	$str = str_replace(' ', dechex(mt_rand(1131992, 9211992)), $str);
	$str = substr($str, 2);
	for ($i = 0; $i < 10; ++$i) $str .= dechex(rand(10000, 99999));
	$str = md5($str);
	
	return $str;
}

/**
 * 创建token
 * @author 李小同
 * @date   2017-10-20 23:19:15
 * @return string
 */
function create_token() {
	
	return md5(mt_rand(1131992, 9211992).dechex(time())).uniqid();
}

/**
 * 获取客户端ip 摘自网络
 * @param bool $int true 返回数字 false 返回字符串
 * @author 李小同
 * @date   2018-01-06 00:45:30
 * @return string ip地址
 */
function getClientIp($int = false) {
	
	if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
		$ip = getenv("HTTP_CLIENT_IP");
	} else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	} else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
		$ip = getenv("REMOTE_ADDR");
	} else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
		$ip = $_SERVER['REMOTE_ADDR'];
	} else {
		$ip = "unknown";
	}
	
	return $int ? ip2long($ip) : $ip;
}

/**
 * 简单加密，单向加密，用于生成用户密码
 * @param string $string 明文
 * @param string $salt   盐
 * @author 李小同
 * @date   2018-1-24 22:00:50
 * @return string 密文 32位
 */
function easy_encrypt($string = '', $salt = '') {
	
	return sha1(sha1($string).env('SALT_SECRET_KEY').$salt);
}

function redisSet($key, $data = '') {
	
	$res = \Redis::set($key, json_encode($data));
	
	# 设置缓存时间
	$keyArr = explode('@', $key);
	if (!isset($keyArr[1]) || !is_numeric($keyArr[1])) {
		$expire = config('cache.DEFAULT_CACHE_EXPIRE');
	} else {
		$expire = intval($keyArr[1]);
	}
	if ($expire != -1) \Redis::expire($key, $expire);
	
	return $res;
}

function redisGet($key) {
	
	$res = \Redis::get($key);
	if ($res) {
		return json_decode($res, 1);
	} else {
		return false;
	}
}