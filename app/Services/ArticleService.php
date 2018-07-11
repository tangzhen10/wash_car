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
}