<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider {
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot() {
		
		# 如果要放入日志文件中
		DB::listen(function ($sql) {
			
			//$sql is an object with the properties:
			//sql: The query
			//bindings: the sql query variables
			//time: The execution time for the query
			//connectionName: The name of the connection
			//To save the executed queries to file:
			//Process the sql and the bindings:
			foreach ($sql->bindings as $i => $binding) {
				if ($binding instanceof \DateTime) {
					$sql->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
				} else {
					if (is_string($binding)) {
						$sql->bindings[$i] = "'$binding'";
					}
				}
			}
			//Insert bindings into query
			$query    = str_replace(array('%', '?'), array('%%', '%s'), $sql->sql);
			$query    = vsprintf($query, $sql->bindings);
			$filename = storage_path('logs'.DIRECTORY_SEPARATOR.'query.log');
			Log::useDailyFiles($filename, 30);
			Log::info($query);
			//Save the query to file
			# 设置日志文件为777权限
			//$filename = storage_path('logs'.DIRECTORY_SEPARATOR.date('Y-m-d').'_query.log');
			//$logFile  = fopen($filename, 'a+');
			//chmod($filename, 0777);
			//fwrite($logFile, date('Y-m-d H:i:s').': '.$query.PHP_EOL);
			//fclose($logFile);
		});
	}
	
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() {
		//
	}
}
