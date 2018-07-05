<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 后台角色控制器
 * Class RoleController
 * @package App\Http\Controllers\Admin
 */
class RoleController extends Controller {
	
	public function roleList() {
		
		return view('admin/role/list');
		
	}
	
	public function createRole() {
		
		
	}
}
