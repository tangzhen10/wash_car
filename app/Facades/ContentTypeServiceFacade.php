<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-07-03 15:12
 */

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ContentTypeServiceFacade extends Facade {
	
	protected static function getFacadeAccessor() {
		
		return 'ContentTypeService';
	}
}