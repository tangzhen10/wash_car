<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-07-03 8:56
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;

class BaseController extends Controller {
	
	protected $user = null; # 用户
	
	public function __construct() {
		
		$this->user = new UserService();
	}
}