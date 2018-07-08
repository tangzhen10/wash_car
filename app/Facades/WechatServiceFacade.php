<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-07-08 11:08
 */

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class WechatServiceFacade extends Facade {
	
	protected static function getFacadeAccessor() {
		
		return 'WechatService';
	}
}