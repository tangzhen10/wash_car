<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-07-03 8:54
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class BaseController extends Controller {
	
	protected $data = []; # 模板渲染用的数据
	protected $service = null;
	protected $baseService = null;
	
	public function __construct() {
		
		$serviceNameStr = ucfirst(static::MODULE);
		if (strpos(static::MODULE, '_') > -1) {
			$module         = explode('_', static::MODULE);
			$serviceNameStr = '';
			foreach ($module as $item) {
				$serviceNameStr .= ucfirst($item);
			}
		}
		$serviceName               = 'App\Services\\'.$serviceNameStr.'Service';
		$this->service             = new $serviceName();
		$this->data['manager']     = \ManagerService::getManagerInfoByManagerId();
		$this->data['menus']       = \PermissionService::getMenuList();
		$this->data['breadcrumbs'] = \PermissionService::getBreadCrumbs($this->data['menus']);
	}
	
	/**
	 * 增改
	 * @param $id int
	 * @author 李小同
	 * @date   2018-7-5 08:30:55
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function form($id = 0) {
		
		if (\Request::getMethod() == 'GET') {
			
			$this->data['detail'] = $this->service->getDetailById($id);
			if (method_exists(static::class, 'assocDataForForm')) {
				$this->data += static::assocDataForForm($this->data['detail']);
			}
			
			return view('admin/'.static::MODULE.'/form', $this->data);
			
		} else {
			
			if ($id) {
				$res = $this->service->update();
			} else {
				$res = $this->service->create();
			}
			$this->render($res);
		}
	}
	
	/**
	 * 修改状态
	 * @author 李小同
	 * @date   2018-7-4 09:16:25
	 */
	protected function changeStatus() {
		
		$id     = \Request::input('id');
		$status = \Request::input('status');
		$res    = $this->service->changeStatus($id, $status, static::MODULE);
		$res ? json_msg('ok') : json_msg(trans('common.action_failed'), 40004);
	}
	
	/**
	 * 批量删除
	 * @author 李小同
	 * @date   2018-7-20 23:54:25
	 */
	public function batchDelete() {
		
		$ids = \Request::input('ids', []);
		if (!empty($ids)) {
			$res = $this->service->batchDelete($ids, static::MODULE);
			$res ? json_msg('ok') : json_msg(trans('common.action_failed'), 40004);
		} else {
			json_msg(trans('validation.no_one_selected'), 40001);
		}
	}
	
	/**
	 * 统一返回固定格式的json
	 * @param $res
	 * @author 李小同
	 * @date   2018-7-4 17:09:42
	 */
	public function render($res) {
		
		if ($res) {
			json_msg('ok');
		} else {
			json_msg(trans('common.action_failed'), 40004);
		}
	}
	
}