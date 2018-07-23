<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-6-29 16:43:08
 */
namespace App\Http\Controllers\Api;

/**
 * 工具类
 * Class ToolController
 * @package App\Http\Controllers\Main
 */
class ToolController extends BaseController {
	
	public function sendSMSCode() {
		
		\ToolService::sendSMSCode();
	}
}
