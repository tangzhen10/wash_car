<?php

return [
	
	/*
	|--------------------------------------------------------------------------
	| Application Name
	|--------------------------------------------------------------------------
	|
	| This value is the name of your application. This value is used when the
	| framework needs to place the application's name in a notification or
	| any other location as required by the application or its packages.
	|
	*/
	
	'name' => env('APP_NAME', 'Laravel'),
	
	/*
	|--------------------------------------------------------------------------
	| Application Environment
	|--------------------------------------------------------------------------
	|
	| This value determines the "environment" your application is currently
	| running in. This may determine how you prefer to configure various
	| services your application utilizes. Set this in your ".env" file.
	|
	*/
	
	'env' => env('APP_ENV', 'production'),
	
	/*
	|--------------------------------------------------------------------------
	| Application Debug Mode
	|--------------------------------------------------------------------------
	|
	| When your application is in debug mode, detailed error messages with
	| stack traces will be shown on every error that occurs within your
	| application. If disabled, a simple generic error page is shown.
	|
	*/
	
	'debug' => env('APP_DEBUG', false),
	
	/*
	|--------------------------------------------------------------------------
	| Application URL
	|--------------------------------------------------------------------------
	|
	| This URL is used by the console to properly generate URLs when using
	| the Artisan command line tool. You should set this to the root of
	| your application so that it is used when running Artisan tasks.
	|
	*/
	
	'url' => env('APP_URL', 'http://localhost'),
	
	/*
	|--------------------------------------------------------------------------
	| Application Timezone
	|--------------------------------------------------------------------------
	|
	| Here you may specify the default timezone for your application, which
	| will be used by the PHP date and date-time functions. We have gone
	| ahead and set this to a sensible default for you out of the box.
	|
	*/
	
	'timezone' => env('TIMEZONE', 'UTC'),
	
	/*
	|--------------------------------------------------------------------------
	| Application Locale Configuration
	|--------------------------------------------------------------------------
	|
	| The application locale determines the default locale that will be used
	| by the translation service provider. You are free to set this value
	| to any of the locales which will be supported by the application.
	|
	*/
	
	'locale' => env('APP_LOCALE', 'zh-CN'),
	
	/*
	|--------------------------------------------------------------------------
	| Application Fallback Locale
	|--------------------------------------------------------------------------
	|
	| The fallback locale determines the locale to use when the current one
	| is not available. You may change the value to correspond to any of
	| the language folders that are provided through your application.
	|
	*/
	
	'fallback_locale' => 'en',
	
	/*
	|--------------------------------------------------------------------------
	| Encryption Key
	|--------------------------------------------------------------------------
	|
	| This key is used by the Illuminate encrypter service and should be set
	| to a random, 32 character string, otherwise these encrypted strings
	| will not be safe. Please do this before deploying an application!
	|
	*/
	
	'key' => env('APP_KEY'),
	
	'cipher' => 'AES-256-CBC',
	
	/*
	|--------------------------------------------------------------------------
	| Logging Configuration
	|--------------------------------------------------------------------------
	|
	| Here you may configure the log settings for your application. Out of
	| the box, Laravel uses the Monolog PHP logging library. This gives
	| you a variety of powerful log handlers / formatters to utilize.
	|
	| Available Settings: "single", "daily", "syslog", "errorlog"
	|
	*/
	
	'log' => env('APP_LOG', 'daily'),
	
	'log_max_files' => 30, # 保留的daily日志的最大天数
	
	'log_level' => env('APP_LOG_LEVEL', 'debug'),
	
	/*
	|--------------------------------------------------------------------------
	| Autoloaded Service Providers
	|--------------------------------------------------------------------------
	|
	| The service providers listed here will be automatically loaded on the
	| request to your application. Feel free to add your own services to
	| this array to grant expanded functionality to your applications.
	|
	*/
	
	'providers' => [
		
		/*
		 * Laravel Framework Service Providers...
		 */
		Illuminate\Auth\AuthServiceProvider::class,
		Illuminate\Broadcasting\BroadcastServiceProvider::class,
		Illuminate\Bus\BusServiceProvider::class,
		Illuminate\Cache\CacheServiceProvider::class,
		Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
		Illuminate\Cookie\CookieServiceProvider::class,
		Illuminate\Database\DatabaseServiceProvider::class,
		Illuminate\Encryption\EncryptionServiceProvider::class,
		Illuminate\Filesystem\FilesystemServiceProvider::class,
		Illuminate\Foundation\Providers\FoundationServiceProvider::class,
		Illuminate\Hashing\HashServiceProvider::class,
		Illuminate\Mail\MailServiceProvider::class,
		Illuminate\Notifications\NotificationServiceProvider::class,
		Illuminate\Pagination\PaginationServiceProvider::class,
		Illuminate\Pipeline\PipelineServiceProvider::class,
		Illuminate\Queue\QueueServiceProvider::class,
		Illuminate\Redis\RedisServiceProvider::class,
		Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
		Illuminate\Session\SessionServiceProvider::class,
		Illuminate\Translation\TranslationServiceProvider::class,
		Illuminate\Validation\ValidationServiceProvider::class,
		Illuminate\View\ViewServiceProvider::class,
		
		/*
		 * Package Service Providers...
		 */
		
		/*
		 * Application Service Providers...
		 */
		App\Providers\AppServiceProvider::class,
		App\Providers\AuthServiceProvider::class,
		// App\Providers\BroadcastServiceProvider::class,
		App\Providers\EventServiceProvider::class,
		App\Providers\RouteServiceProvider::class,
		Maatwebsite\Excel\ExcelServiceProvider::class, # excel composer安装 2018-08-27 14:52:30
		
		# 自定义服务
		App\Providers\UserServiceProvider::class,           # 用户 李小同 2018-6-28 14:26:45
		App\Providers\ToolServiceProvider::class,           # 工具 李小同 2018-6-29 16:39:52
		App\Providers\ManagerServiceProvider::class,        # 管理员 李小同 2018-7-3 08:26:50
		App\Providers\RoleServiceProvider::class,           # 角色 李小同 2018-7-3 15:14:54
		App\Providers\PermissionServiceProvider::class,     # 权限 李小同 2018-7-4 11:23:07
		App\Providers\WechatServiceProvider::class,         # 微信 李小同 2018-7-8 11:10:27
		App\Providers\ContentTypeServiceProvider::class,    # 文章模板 李小同 2018-7-10 23:59:16
		App\Providers\ArticleServiceProvider::class,        # 文章 李小同 2018-7-11 21:43:25
		App\Providers\SettingServiceProvider::class,        # 设置 李小同 2018-7-22 13:02:40
		App\Providers\CarServiceProvider::class,            # 车辆 李小同 2018-7-22 22:13:12
		App\Providers\OrderServiceProvider::class,          # 订单 李小同 2018-7-30 20:34:27
		App\Providers\CardServiceProvider::class,           # 卡券 李小同 2018-08-19 14:28:29
		App\Providers\PaymentServiceProvider::class,        # 支付 李小同 2018-08-28 22:44:29
	],
	
	/*
	|--------------------------------------------------------------------------
	| Class Aliases
	|--------------------------------------------------------------------------
	|
	| This array of class aliases will be registered when this application
	| is started. However, feel free to register as many as you wish as
	| the aliases are "lazy" loaded so they don't hinder performance.
	|
	*/
	
	'aliases' => [
		
		'App'                => Illuminate\Support\Facades\App::class,
		'Artisan'            => Illuminate\Support\Facades\Artisan::class,
		'Auth'               => Illuminate\Support\Facades\Auth::class,
		'Blade'              => Illuminate\Support\Facades\Blade::class,
		'Broadcast'          => Illuminate\Support\Facades\Broadcast::class,
		'Bus'                => Illuminate\Support\Facades\Bus::class,
		'Cache'              => Illuminate\Support\Facades\Cache::class,
		'Config'             => Illuminate\Support\Facades\Config::class,
		'Cookie'             => Illuminate\Support\Facades\Cookie::class,
		'Crypt'              => Illuminate\Support\Facades\Crypt::class,
		'DB'                 => Illuminate\Support\Facades\DB::class,
		'Eloquent'           => Illuminate\Database\Eloquent\Model::class,
		'Event'              => Illuminate\Support\Facades\Event::class,
		'File'               => Illuminate\Support\Facades\File::class,
		'Gate'               => Illuminate\Support\Facades\Gate::class,
		'Hash'               => Illuminate\Support\Facades\Hash::class,
		'Lang'               => Illuminate\Support\Facades\Lang::class,
		'Log'                => Illuminate\Support\Facades\Log::class,
		'Mail'               => Illuminate\Support\Facades\Mail::class,
		'Notification'       => Illuminate\Support\Facades\Notification::class,
		'Password'           => Illuminate\Support\Facades\Password::class,
		'Queue'              => Illuminate\Support\Facades\Queue::class,
		'Redirect'           => Illuminate\Support\Facades\Redirect::class,
		'Redis'              => Illuminate\Support\Facades\Redis::class,
		'Request'            => Illuminate\Support\Facades\Request::class,
		'Response'           => Illuminate\Support\Facades\Response::class,
		'Route'              => Illuminate\Support\Facades\Route::class,
		'Schema'             => Illuminate\Support\Facades\Schema::class,
		'Session'            => Illuminate\Support\Facades\Session::class,
		'Storage'            => Illuminate\Support\Facades\Storage::class,
		'URL'                => Illuminate\Support\Facades\URL::class,
		'Validator'          => Illuminate\Support\Facades\Validator::class,
		'View'               => Illuminate\Support\Facades\View::class,
		'Excel'              => Maatwebsite\Excel\Facades\Excel::class,
		
		# 自定义服务
		'UserService'        => App\Facades\UserServiceFacade::class,           # 用户 李小同 2018-6-28 14:28:08
		'ToolService'        => App\Facades\ToolServiceFacade::class,           # 工具 李小同 2018-6-29 16:39:59
		'ManagerService'     => App\Facades\ManagerServiceFacade::class,        # 管理员 李小同 2018-7-3 08:26:27
		'RoleService'        => App\Facades\RoleServiceFacade::class,           # 角色 李小同 2018-7-3 15:14:34
		'PermissionService'  => App\Facades\PermissionServiceFacade::class,     # 权限 李小同 2018-7-4 11:23:21
		'WechatService'      => App\Facades\WechatServiceFacade::class,         # 微信 李小同 2018-7-8 11:10:14
		'ContentTypeService' => App\Facades\ContentTypeServiceFacade::class,    # 文章模板 李小同 2018-7-10 23:58:55
		'ArticleService'     => App\Facades\ArticleServiceFacade::class,        # 文章 李小同 2018-7-11 21:43:49
		'SettingService'     => App\Facades\SettingServiceFacade::class,        # 设置 李小同 2018-7-22 13:02:23
		'CarService'         => App\Facades\CarServiceFacade::class,            # 车辆 李小同 2018-7-22 22:13:40
		'OrderService'       => App\Facades\OrderServiceFacade::class,          # 订单 李小同 2018-7-30 20:33:55
		'CardService'        => App\Facades\CardServiceFacade::class,           # 卡券 李小同 2018-08-19 14:28:04
		'PaymentService'     => App\Facades\PaymentServiceFacade::class,        # 支付 李小同 2018-08-28 22:43:57
	],

];
