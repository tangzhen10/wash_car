<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-07-05 21:04
 */

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class MemberServiceFacade extends Facade {
	
	protected static function getFacadeAccessor() {
		
		return 'MemberService';
	}
}