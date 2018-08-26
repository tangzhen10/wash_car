<?php

return [
	
	/*
	|--------------------------------------------------------------------------
	| Default Cache Store
	|--------------------------------------------------------------------------
	|
	| This option controls the default cache connection that gets used while
	| using this caching library. This connection is used when another is
	| not explicitly specified when executing a given caching function.
	|
	| Supported: "apc", "array", "database", "file", "memcached", "redis"
	|
	*/
	
	'default' => env('CACHE_DRIVER', 'file'),
	
	/*
	|--------------------------------------------------------------------------
	| Cache Stores
	|--------------------------------------------------------------------------
	|
	| Here you may define all of the cache "stores" for your application as
	| well as their drivers. You may even define multiple stores for the
	| same cache driver to group types of items stored in your caches.
	|
	*/
	
	'stores' => [
		
		'apc' => [
			'driver' => 'apc',
		],
		
		'array' => [
			'driver' => 'array',
		],
		
		'database' => [
			'driver'     => 'database',
			'table'      => 'cache',
			'connection' => null,
		],
		
		'file' => [
			'driver' => 'file',
			'path'   => storage_path('framework/cache/data'),
		],
		
		'memcached' => [
			'driver'        => 'memcached',
			'persistent_id' => env('MEMCACHED_PERSISTENT_ID'),
			'sasl'          => [
				env('MEMCACHED_USERNAME'),
				env('MEMCACHED_PASSWORD'),
			],
			'options'       => [// Memcached::OPT_CONNECT_TIMEOUT  => 2000,
			],
			'servers'       => [
				[
					'host'   => env('MEMCACHED_HOST', '127.0.0.1'),
					'port'   => env('MEMCACHED_PORT', 11211),
					'weight' => 100,
				],
			],
		],
		
		'redis' => [
			'driver'     => 'redis',
			'connection' => 'default',
		],
	
	],
	
	/*
	|--------------------------------------------------------------------------
	| Cache Key Prefix
	|--------------------------------------------------------------------------
	|
	| When utilizing a RAM based store such as APC or Memcached, there might
	| be other applications utilizing the same cache. So, we'll specify a
	| value to get prefixed to all our keys so we can avoid collisions.
	|
	*/
	
	'prefix' => env('CACHE_PREFIX', str_slug(env('APP_NAME', 'laravel'), '_').'_cache'),
	
	'DEFAULT_CACHE_EXPIRE' => 1800, # 默认缓存时间
	
	# admin redis key [86400 = 24h, 604800 = 1 week]
	'ADMIN_LOG_INFO'       => env('APP_NAME').':manager_info:%s@86400', # 管理员登录信息
	'FORM_ELEMENT'         => env('APP_NAME').':form_element@604800', # 表单类型
	
	# redis key [86400 = 24h, 604800 = 1 week]
	'USER_INFO'            => env('APP_NAME').':user_info:%s@604800', # 用户登录信息
	'WECHAT'               => [ # 公众号
	                            'ACCESS_TOKEN' => env('APP_NAME').':wechat:access_token:%s@7200', # 微信登录用户的access_token
	                            'USER_INFO'    => env('APP_NAME').':wechat:user_info:%s', # 微信用户信息
	],
	'WECHAT_MP'            => [ # 小程序
	                            'ACCESS_TOKEN' => env('APP_NAME').':wechat_mp:access_token@7200', # 微信登录用户的access_token
	                            'SESSION_KEY'  => env('APP_NAME').':wechat_mp:session_key:%s@7200', # 小程序session_key
	],
	'TABLE_COLUMN'         => env('APP_NAME').':table_column:%s@604800', # 表结构字段
	'CONTENT_TYPE'         => env('APP_NAME').':content_type:%s@604800', # 文档类型结构
	'SETTING'              => env('APP_NAME').':setting:setting@604800', # 系统配置
	'CAR'                  => [
		'BRAND'    => env('APP_NAME').':car:brand@604800', # 品牌
		'PROVINCE' => env('APP_NAME').':car:province@604800', # 车牌省份
		'COLOR'    => env('APP_NAME').':car:color@604800', # 颜色
	],
	'VERIFY_CODE'          => [
		'REGISTER'       => env('APP_NAME').':verify_code:register:%s@300', # 注册验证码
		'LOGIN'          => env('APP_NAME').':verify_code:login:%s@60', # 登录验证码
		'LOGIN_BY_PHONE' => env('APP_NAME').':verify_code:login_by_phone:%s@300', # 手机登录验证码
	],
	'ARTICLE'              => [
		'DETAIL' => env('APP_NAME').':article:detail:%s@3600', # 文章详情
	],
	'ORDER'                => [
		'PRODUCT_SALE_COUNT'  => env('APP_NAME').':order:product_sale_count:%s', # 商品的销售次数
		'TODAY_ORDER_ID_LIST' => env('APP_NAME').':order:order_id_list:%s', # 订单号列表
	],
	'MAIL_LIST'            => [
		'TO_SEND'  => env('APP_NAME').':mail_list:to_send', # 待发邮件列表
		'HAS_SENT' => env('APP_NAME').':mail_list:has_sent', # 已发邮件列表
	],
];
