<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-07-11 21:41
 */

namespace App\Services;

/**
 * 文章服务类
 * Class ArticleService
 * @package App\Services
 */
class ArticleService extends BaseService {
	
	public $module = 'article';
	
	public function initDetail() {
		
		$detail = [
			'id'           => '0',
			'content_type' => '0',
			'name'         => '',
			'sub_name'     => '',
			'start_time'   => '',
			'end_time'     => '',
			'image'        => '',
			'status'       => '1',
		];
		
		return $detail;
	}
	
	/**
	 * 创建
	 * @author 李小同
	 * @date   2018-7-4 13:59:36
	 * @return mixed
	 */
	public function create() {
		
		$data = request_all();
		
		\DB::beginTransaction();
		try {
			
			$articleBaseData = [
				'name'         => $data['name'],
				'sub_name'     => $data['sub_name'],
				'start_time'   => empty($data['start_time']) ? 0 : strtotime($data['start_time']),
				'end_time'     => empty($data['end_time']) ? 0 : strtotime($data['end_time']),
				'content_type' => 1,#$data['content_type'],
				'create_at'    => time(),
				'create_by'    => \ManagerService::getManagerId(),
			
			];
			$articleId       = \DB::table('article_base')->insertGetId($articleBaseData);
			\DB::commit();
			
		} catch (\Exception $e) {
			
			\DB::rollback();
			json_msg($e->getMessage(), 40004);
		}
		
		json_msg($data);
	}
}