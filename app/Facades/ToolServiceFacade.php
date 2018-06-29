<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-06-28 0028 14:23
 */
class ToolServiceFacade extends Facade {
	
	protected static function getFacadeAccessor() {
		
		return 'ToolService';
	}
	
}