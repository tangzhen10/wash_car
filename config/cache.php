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
	
	# redis key [86400 = 24h, 604800 = 1 week]
	'USER_INFO'            => env('APP_NAME').':user_info:%s@604800', # 用户登录信息
	'ADMIN_LOG_INFO'       => env('APP_NAME').':manager_info:%s@86400', # 管理员登录信息
	'WECHAT'               => [
		'ACCESS_TOKEN' => env('APP_NAME').':wechat:access_token:%s@7200', # 微信登录用户的access_token
		'USER_INFO'    => env('APP_NAME').':wechat:user_info:%s', # 微信用户信息
	],
	'TABLE_COLUMN'         => env('APP_NAME').':table_column:%s@604800', # 表结构字段
	'CONTENT_TYPE'         => env('APP_NAME').':content_type:%s@604800', # 文档类型结构
	'SETTING'              => env('APP_NAME').':setting:%s@604800', # 系统配置

];
