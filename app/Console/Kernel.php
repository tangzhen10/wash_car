<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {
	
	/**
	 * The Artisan commands provided by your application.
	 * @var array
	 */
	protected $commands = [
		\App\Console\Commands\CancelOrder::class, # 超时自动取消未支付的订单
		\App\Console\Commands\SendMail::class, # 发送邮件
		\App\Console\Commands\SocketServer::class, # socket服务器
	];
	
	/**
	 * Define the application's command schedule.
	 * @param  \Illuminate\Console\Scheduling\Schedule $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule) {
		
		$schedule->command('CancelOrder')->everyFiveMinutes();
		
		$schedule->command('SendMail')->everyMinute();
	}
	
	/**
	 * Register the commands for the application.
	 * @return void
	 */
	protected function commands() {
		
		$this->load(__DIR__.'/Commands');
		
		require base_path('routes/console.php');
	}
}
