<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-06-29 0029 16:54
 */
return [
	'PATTERN'            => [
		'PHONE' => '/^1[34578]{1}[\d]{9}$/',
		'EMAIL' => '/^[a-z0-9\-_\.]+[a-z0-9\-_\.]*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/',
	],
	'ADMIN_LOGIN_COOKIE' => 'admin_log_id', # admin登录ID的cookie名
	'DEFAULT_PER_PAGE'   => 10, # 每页默认条目数量
];