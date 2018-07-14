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
	
	/**
	 * 获取详情
	 * @param $id
	 * @author 李小同
	 * @date   2018-7-4 17:40:46
	 * @return array
	 */
	public function getDetailById($id) {
		
		if ($id) {
			
			$detail = \DB::table($this->module)->where('id', $id)->first();
			
			$detail['start_time'] = intToTime($detail['start_time']);
			$detail['end_time']   = intToTime($detail['end_time']);
			
			$options = \DB::table('article_detail')->where('article_id', $id)->get(['name', 'value'])->toArray();
			foreach ($options as $option) {
				$detail[$option['name']] = $option['value'];
			}
			
			# 复选框的值炸开成数组
			$contentTypeId = $detail['content_type'];
			$contentType   = \ContentTypeService::getDetailById($contentTypeId);
			foreach ($contentType['structure'] as $field) {
				if ($field['type'] == 'checkbox' && strpos($field['name'], '[]') > -1) {
					$name = substr($field['name'], 0, -2);
					if (empty($detail[$name])) $detail[$name] = '';
					$detail[$name] = explode(',', $detail[$name]);
				}
			}
			
		} else {
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
		}
		
		return $detail;
	}
	
	/**
	 * 创建
	 * @author 李小同
	 * @date   2018-7-4 13:59:36
	 * @return mixed
	 */
	public function create() {
		
		return $this->update();
	}
	
	/**
	 * 修改
	 * @author 李小同
	 * @date   2018-7-14 08:25:29
	 * @return mixed
	 */
	public function update() {
		
		$data = request_all();
		
		\DB::beginTransaction();
		try {
			
			# 公共属性
			$articleBaseData = [
				'name'         => $data['name'],
				'sub_name'     => $data['sub_name'],
				'start_time'   => empty($data['start_time']) ? 0 : strtotime($data['start_time']),
				'end_time'     => empty($data['end_time']) ? 0 : strtotime($data['end_time']),
				'content_type' => $data['content_type'],
				'create_at'    => time(),
				'create_by'    => \ManagerService::getManagerId(),
			
			];
			if ($data['id']) {
				\DB::table($this->module)->where('id', $data['id'])->update($articleBaseData);
				$articleId = $data['id'];
			} else {
				$articleId = \DB::table($this->module)->insertGetId($articleBaseData);
			}
			
			# 私有属性
			$baseFields = \ContentTypeService::getArticleBaseFields();
			foreach ($baseFields as $field) {
				if (isset($data[$field])) unset($data[$field]);
			}
			
			\DB::table('article_detail')->where('article_id', $articleId)->delete();
			
			if (count($data)) {
				
				$sqlDetail = 'INSERT INTO 
								`t_article_detail` 
							(
								`article_id`,
								`name`,
								`value`
							) VALUES ';
				foreach ($data as $name => $value) {
					if (is_array($value)) $value = implode(',', $value);
					$sqlDetail .= sprintf('(\'%s\', \'%s\', \'%s\'),', $articleId, addslashes($name), addslashes($value));
				}
				$sqlDetail = substr($sqlDetail, 0, -1);
				
				\DB::insert($sqlDetail);
			}
			
			\DB::commit();
			
			return $articleId;
			
		} catch (\Exception $e) {
			
			\DB::rollback();
			
			json_msg($e->getMessage(), $e->getCode());
		}
	}
	
	/**
	 * 获取文章列表
	 * @param array $filter 帅选条件
	 * @author 李小同
	 * @date   2018-7-13 22:00:57
	 * @return mixed
	 */
	public function getList(array $filter = []) {
		
		if (empty($filter['perPage'])) $filter['perPage'] = config('project.DEFAULT_PER_PAGE');
		
		$fields   = [
			'a.id',
			'b.name AS content_type',
			'a.name',
			'a.start_time',
			'a.end_time',
			'a.create_at',
			'c.name AS create_by',
			'a.update_at',
			'a.update_by',
			'a.status',
		];
		$listPage = \DB::table('article AS a')
		               ->join('content_type AS b', 'b.id', 'a.content_type')
		               ->join('manager AS c', 'c.id', 'a.create_by')
		               ->where('a.status', '!=', '-1')
		               ->select($fields)
		               ->paginate($filter['perPage']);
		$list     = json_decode(json_encode($listPage), 1)['data'];
		
		foreach ($list as &$item) {
			$item['start_time']  = intToTime($item['start_time']);
			$item['end_time']    = intToTime($item['end_time']);
			$item['create_at']   = intToTime($item['create_at']);
			$item['update_at']   = intToTime($item['update_at']);
			$item['status_text'] = trans('common.'.($item['status'] ? 'enable' : 'disable'));
		}
		unset($item);
		
		return compact('list', 'listPage');
	}
	
}