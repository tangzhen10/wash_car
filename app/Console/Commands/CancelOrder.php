<?php

namespace App\Console\Commands;

use App\Services\OrderService;
use Illuminate\Console\Command;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class CancelOrder extends Command {
	
	/**
	 * The name and signature of the console command.
	 * @var string
	 */
	protected $signature = 'CancelOrder';
	
	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = '未支付的订单一小时自动取消';
	
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
		
		$orderIds = \DB::table('wash_order')
		               ->where('status', '1')
		               ->where('payment_status', '0')
		               ->where('create_at', '<', time() - 3600)
		               ->pluck('order_id')
		               ->toArray();
		$logger = new Logger('cancel_order');
		$logger->pushHandler(new StreamHandler(config('project.PATH_TO_CANCEL_ORDER_LOG')));
		if (count($orderIds)) {
			
			foreach ($orderIds as $orderId) {
				
				\DB::beginTransaction();
				try {
					
					$updateData = ['status' => 7];
					\DB::table('wash_order')->where('order_id', $orderId)->update($updateData);
					
					$logData = [
						'wash_order_id' => $orderId,
						'action'        => 'cancel_order',
						'order_status'  => $updateData['status'],
						'operator_type' => 'system',
					];
					\OrderService::addOrderLog($logData);
					\DB::commit();
					$logger->info($orderId.'取消成功', $logData);
					
				} catch (\Exception $e) {
					
					\DB::rollback();
					$error = ['msg' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()];
					$logger->error($orderId.'取消失败', $error);
				}
			}
		} else {
			$logger->info('无订单可取消');
		}
	}
}
