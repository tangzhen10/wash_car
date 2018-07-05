<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-07-04 11:22
 */

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PermissionServiceFacade extends Facade {
	
	protected static function getFacadeAccessor() {
		
		return 'PermissionService';
	}
}