<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-07-03 8:54
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ManagerService;

class BaseController extends Controller {
	
	protected $manager = null; # 管理员
	
	public function __construct() {
		
		$this->manager = new ManagerService();
	}
	
}