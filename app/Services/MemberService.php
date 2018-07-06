<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-07-05 21:03
 */

namespace App\Services;

/**
 * 会员（前台用户）服务类
 * Class MemberService
 * @package App\Services
 */
class MemberService {
	
	public $module = 'member';
	
	/**
	 * 检查是否允许修改状态
	 * @param $id
	 * @param $status
	 * @param $table string 要操作的数据表
	 * @author 李小同
	 * @date   2018-7-5 21:45:54
	 */
	public function checkChangeStatus($id, $status, &$table) {
		
		$table = 'user'; # 会员即前台用户，这里使用member是为了区分前台后
	}
	
	/**
	 * 获取前台用户
	 * @param int $perPage
	 * @author 李小同
	 * @date
	 * @return mixed
	 */
	public function getPaginationList($perPage = 0) {
		
		$fields = [
			'a.user_id',
			'a.nickname',
			'a.phone',
			'a.email',
			'a.gender',
			'a.last_login_at',
			'a.last_login_ip',
		];
		if (empty($perPage)) $perPage = config('project.DEFAULT_PER_PAGE');
		$pagination = \DB::table('user AS a')->select($fields)->paginate($perPage);
		
		return $pagination;
	}
	
	/**
	 * 通过分页对象获取数据
	 * @param $pagination DB::pagination()
	 * @author 李小同
	 * @date   2018-7-6 15:09:40
	 * @return array
	 */
	public function getListByPage($pagination) {
		
		$paginationArr = json_decode(json_encode($pagination), 1);
		if (empty($paginationArr)) return [];
		$list = $paginationArr['data'];
		foreach ($list as &$item) {
			//$item['status_text'] = trans('common.'.($item['status'] ? 'enable' : 'disable'));
			$item['gender_text'] = trans('common.gender_'.$item['gender']);
		}
		unset($item);
		
		return $list;
	}
	
}