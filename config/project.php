<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-06-29 0029 16:54
 */
return [
	'PATTERN'                    => [
		'PHONE' => '/^1[3456789]{1}[\d]{9}$/',
		'EMAIL' => '/^[a-z0-9\-_\.]+[a-z0-9\-_\.]*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/',
		'DATE'  => '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|2\d|3[01])$/',
		//		'PLATE' => '/^[京沪浙苏粤鲁晋冀豫川渝辽吉黑皖鄂津贵云桂琼青新藏蒙宁甘陕闽赣湘][A-Za-z][A-Za-z0-9]{5,6}$/', # 车牌号
		'PLATE' => '/^[A-Za-z][A-Za-z0-9]{5}$/', # 车牌号
	],
	'ALLOW_IDENTITY_TYPE'        => ['phone'/*, 'email'*/], # 合法的注册渠道，目前只允许手机注册
	'ADMIN_LOGIN_COOKIE'         => 'admin_log_id', # admin登录ID的cookie名
	'UPLOAD_STORAGE_PATH'        => 'src/upload/image', # 图片上传目录
	'ALLOW_PICTURE_TYPE'         => ['jpg', 'jpeg', 'png', 'gif'], # 允许上传文件格式
	'MAX_SIZE_UPLOAD_FILE'       => 10240 * 1024, # 10M
	'THUMB_WIDTH'                => '300', # 缩略图长度最大值
	'THUMB_HEIGHT'               => '300', # 缩略图高度最大值
	'THUMB_PREFIX'               => 'thumb_', # 缩略图前缀
	'ALLOW_TYPE_OF_CONTENT_TYPE' => ['1', '2'], # 合法的文档类型的类型
	'FIRST_LETTER'               => 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z', # 车辆品牌首字母
	'CONTENT_TYPE'               => [
		'WASH_INDEX_BANNER' => 25, # 首页banner
		'WASH_CARD'         => 26, # 洗车卡
	],
	'PATH_TO_WASH_ORDER_LOG'     => storage_path('logs/wash_order/'.date('Y-m-d').'.log'), # 洗车订单的日志位置
	'PATH_TO_CANCEL_ORDER_LOG'   => storage_path('logs/cancel_order/'.date('Y-m-d').'.log'), # 超时自动取消洗车订单的日志位置
	'WECHAT_MP'                  => [
		'TPL_ID' => [ # 模板消息
			'ADD_ORDER' => 'wsVdSzOvJt2p2GwhLRwEVpMCepZq9k2Ng3HZ4paKFSo', # 下单成功
		],
	],
	'ORDER_WAIT_PAY'             => 3600, # 订单自动取消的时间
];