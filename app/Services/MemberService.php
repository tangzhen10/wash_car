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
class MemberService extends BaseService {
	
	public $module = 'member';
	
	/**
	 * 检查是否允许修改状态
	 * @param $id
	 * @param $status
	 * @param $module string 要操作的数据表
	 * @author 李小同
	 * @date   2018-7-5 21:45:54
	 */
	public function checkChangeStatus($id, $status, &$module) {
		
		# remind lxt 后台暂不允许修改前台用户状态，若允许修改用户可注释掉
		json_msg(trans('error.illegal_action'), 40003);
		
		$module = 'user'; # 会员即前台用户，这里使用member是为了区分前台后
	}
	
	/**
	 * 获取详情
	 * copy from UserService::getUserInfo()
	 * @param $id
	 * @author 李小同
	 * @date   2018-7-7 00:01:15
	 * @return array
	 */
	public function getDetailById($id) {
		
		$detail = \DB::table('user')->where('user_id', $id)->first();
		
		if (!empty($detail)) {
			
			# 生日在1970-01-01的人，birthday字段为0
			$detail['birthday'] = $detail['birthday'] != -1 ? date('Y-m-d', $detail['birthday']) : '';
			
			$detail['create_at']     = date('Y-m-d H:i:s', $detail['create_at']);
			$detail['last_login_at'] = $detail['last_login_at'] > 0 ? date('Y-m-d H:i:s', $detail['last_login_at']) : '';
			$detail['last_login_ip'] = long2ip($detail['last_login_ip']);
		}
		
		return $detail;
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
			'a.create_at',
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
			$item['gender_text'] = trans('common.gender_'.$item['gender']);
			$item['create_at']   = date('Y-m-d H:i:s', $item['create_at']);
		}
		unset($item);
		
		return $list;
	}
	
	/**
	 * 修改用户数据
	 * @author 李小同
	 * @date   2018-7-6 23:53:06
	 * @return mixed
	 */
	public function update() {
		
		$post = $this->_filterFormData();
		
		\DB::table('user')->where('user_id', $post['user_id'])->update($post);
		
		return true;
	}
	
	/**
	 * 检查并处理表单数据
	 * @author 李小同
	 * @date   2018-7-6 23:51:54
	 * @return array
	 */
	private function _filterFormData() {
		
		$post = request_all();
		
		# 检测该用户名是否被占用
		if (!empty($post['nickname'])) {
			$post['nickname'] = trim($post['nickname']);
			$existNickName    = \DB::table('user')
			                       ->where('user_id', $post['user_id'])
			                       ->where('nickname', '!=', $post['nickname'])
			                       ->count();
			if ($existNickName) {
				$errorMsg = trans('validation.has_been_registered', ['attr' => trans('common.username')]);
				json_msg($errorMsg, 40002);
			}
		}
		
		# 检测手机号码格式
		if (!empty($post['phone'])) {
			if (!preg_match(config('project.PATTERN.PHONE'), $post['phone'])) {
				$errorMsg = trans('validation.invalid', ['attr' => trans('common.phone')]);
				json_msg($errorMsg, 40003);
			}
		}
		
		# 检测邮箱格式
		if (!empty($post['email'])) {
			if (!preg_match(config('project.PATTERN.EMAIL'), $post['email'])) {
				$errorMsg = trans('validation.invalid', ['attr' => trans('common.email')]);
				json_msg($errorMsg, 40003);
			}
		}
		
		# 生日转换成时间戳
		if (!empty($post['birthday'])) $post['birthday'] = strtotime($post['birthday']);
		
		unset($post['uploadfile'], $post['file']);
		
		return $post;
	}
}