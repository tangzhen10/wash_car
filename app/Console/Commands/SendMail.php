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
		$mail           = \Redis::rpoplpush($mailToSendKey, $mailHasSentKey);
		$mail           = json_decode($mail, 1);
		if (preg_match(config('project.PATTERN.EMAIL'), $mail['to'])) {
			\ToolService::sendTextMail($mail['to'], $mail['subject'], $mail['content']);
		}
	}
}
