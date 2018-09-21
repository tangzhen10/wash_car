<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendMail extends Command {
	
	/**
	 * The name and signature of the console command.
	 * @var string
	 */
	protected $signature = 'SendMail';
	
	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = '发送邮件';
	
	/**
	 * Create a new command instance.
	 * @return void
	 */
	public function __construct() {
		
		parent::__construct();
	}
	
	/**
	 * Execute the console command.
	 * @return mixed
	 */
	public function handle() {
		
		$mailToSendKey  = config('cache.MAIL_LIST.TO_SEND');
		$mailHasSentKey = config('cache.MAIL_LIST.HAS_SENT');
		while ($mail = \Redis::rpop($mailToSendKey)) {
			
			$mail = json_decode($mail, 1);
			if (preg_match(config('project.PATTERN.EMAIL'), $mail['to'])) {
				
				try {
					if (\ToolService::sendTextMail($mail, true)) {
						\Redis::lpush($mailHasSentKey, json_encode($mail));
					}
				} catch (\Exception $e) {
					
					//\Redis::lpush($mailToSendKey, json_encode($mail));
					$mail['error_msg']  = $e->getMessage();
					$mail['error_file'] = $e->getFile();
					$mail['error_line'] = $e->getLine();
					\Redis::lpush($mailHasSentKey.' - error', json_encode($mail));
				}
			}
		}
	}
}
