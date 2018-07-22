<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-06-29 0029 16:54
 */
return [
	'PATTERN'                    => [
		'PHONE' => '/^1[34578]{1}[\d]{9}$/',
		'EMAIL' => '/^[a-z0-9\-_\.]+[a-z0-9\-_\.]*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/',
		'DATE'  => '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|2\d|3[01])$/',
	],
	'ADMIN_LOGIN_COOKIE'         => 'admin_log_id', # admin登录ID的cookie名
	'UPLOAD_STORAGE_PATH'        => 'src/upload/image', # 图片上传目录
	'ALLOW_PICTURE_TYPE'         => ['jpg', 'jpeg', 'png', 'gif'], # 允许上传文件格式
	'MAX_SIZE_UPLOAD_FILE'       => 10240 * 1024, # 10M
	'THUMB_WIDTH'                => '200', # 缩略图长度最大值
	'THUMB_HEIGHT'               => '200', # 缩略图高度最大值
	'THUMB_PREFIX'               => 'thumb_', # 缩略图前缀
	'ALLOW_TYPE_OF_CONTENT_TYPE' => ['1', '2'], # 合法的文档类型的类型
];