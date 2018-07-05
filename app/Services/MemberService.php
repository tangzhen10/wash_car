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
	 * @author 李小同
	 * @date
	 * @return mixed
	 */
	public function getPaginationList() {
		
		$fields     = ['a.user_id', 'a.nickname', 'a.phone', 'a.email', 'a.last_login_at', 'a.last_login_ip'];
		$pagination = \DB::table('user AS a')->select($fields)->paginate(2);
		
		return $pagination;
	}
	
	public function getList() {
		
	}
	
}