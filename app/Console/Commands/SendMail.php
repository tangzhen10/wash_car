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
		
		$fields = ['id', 'to', 'subject', 'content', 'cc', 'attach', 'times'];
		$emails = \DB::table('mail')
		             ->where('status', '0')
		             ->where('times', '<', config('project.MAIL_RETRY_TIMES'))
		             ->orderBy('priority', 'DESC')
		             ->get($fields)
		             ->toArray();
		
		foreach ($emails as $mail) {
			
			$to = explode(',', $mail['to']);
			foreach ($to as $key => $value) {
				if (!preg_match(config('project.PATTERN.EMAIL'), $value)) unset($to[$key]);
			}
			
			if (count($to)) {
				$mail['to'] = $to;
				try {
					
					if (\ToolService::sendTextMail($mail, true)) $updateData = ['status' => '1'];
					
				} catch (\Exception $e) {
					
					$error      = [
						'msg'  => $e->getMessage(),
						'file' => $e->getFile(),
						'line' => $e->getLine(),
					];
					$updateData = ['error' => json_encode($error)];
				}
				$updateData['update_at'] = time();
				$updateData['times']     = ++$mail['times'];
				\DB::table('mail')->where('id', $mail['id'])->update($updateData);
			}
		}
	}
	
	/**
	 * 完全使用redis储存邮件队列
	 * @author 李小同
	 * @date   2018-09-21 18:00:37
	 */
	public function handleBak() {
		
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
