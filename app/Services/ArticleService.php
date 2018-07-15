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
				$detail['detail'][$option['name']] = $option['value'];
			}
			
			# 复选框的值炸开成数组
			$contentTypeId = $detail['content_type'];
			$contentType   = \ContentTypeService::getDetailById($contentTypeId);
			foreach ($contentType['structure'] as $field) {
				if ($field['type'] == 'checkbox') {
					if (empty($detail['detail'][$field['name']])) $detail['detail'][$field['name']] = '';
					$detail['detail'][$field['name']] = explode(',', $detail['detail'][$field['name']]);
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
				'detail'       => [],
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
		
		# validate
		if (empty(trim($data['name']))) {
			json_msg(trans('validation.required', ['attr' => trans('common.article_name')]), 40001);
		}
		if (empty($data['content_type'])) {
			json_msg(trans('validation.required', ['attr' => trans('common.content_type')]), 40001);
		}
		
		\DB::beginTransaction();
		try {
			
			# 公共属性
			$baseData = [
				'name'         => $data['name'],
				'sub_name'     => $data['sub_name'],
				'start_time'   => empty($data['start_time']) ? 0 : strtotime($data['start_time']),
				'end_time'     => empty($data['end_time']) ? 0 : strtotime($data['end_time']),
				'content_type' => $data['content_type'],
			];
			if ($data['id']) {
				$baseData['update_at'] = time();
				$baseData['update_by'] = \ManagerService::getManagerId();
				\DB::table($this->module)->where('id', $data['id'])->update($baseData);
				$articleId = $data['id'];
			} else {
				$baseData['create_at'] = time();
				$baseData['create_by'] = \ManagerService::getManagerId();
				
				$articleId = \DB::table($this->module)->insertGetId($baseData);
			}
			
			# 私有属性
			$fields = \ContentTypeService::getDetailById($data['content_type'], true);
			
			\DB::table('article_detail')->where('article_id', $articleId)->delete();
			
			$sqlFields = '';
			foreach ($fields as $field) {
				
				$name = $field['name'];
				if (!isset($data[$name]) && empty($data['uploadfile_'.$name])) continue;
				$value = $data[$name];
				
				switch ($field['type']) {
					case 'checkbox':
						$value = implode(',', $value);
						break;
					case 'image':
						if (empty($value)) {
							$value = $data['uploadfile_'.$name];
						} else {
							$files = \Request::file($name);
							$value = ToolService::uploadFiles($files);
						}
						break;
				}
				
				$sqlFields .= sprintf('(\'%s\', \'%s\', \'%s\'),', $articleId, addslashes($name), addslashes($value));
			}
			
			if ($sqlFields) {
				$sqlDetail = 'INSERT INTO `t_article_detail` (`article_id`,`name`,`value`) VALUES '.$sqlFields;
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
		               ->orderBy('a.id', 'desc')
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
	
	# region 前台
	public function getArticleList(array $filter = []) {
		
		$where = ['a.status' => '1'];
		if (!empty($filter['content_type'])) {
			$where['a.content_type'] = intval($filter['content_type']);
		} else {
			json_msg('必须指定一种文档类型', 40001);
		}
		
		# 公共属性
		$articles = \DB::table('article AS a')->where($where)->orderBy('a.id', 'desc')->get()->toArray();
		
		# 私有属性
		$privateFields = \ContentTypeService::getDetailById($filter['content_type'], true);
		$detailFields  = [];
		foreach ($privateFields as $item) $detailFields[$item['name']] = $item;
		
		$articleIds = array_column($articles, 'id');
		$fields     = ['article_id', 'name', 'value'];
		$detailRows = \DB::table('article_detail')->whereIn('article_id', $articleIds)->get($fields)->toArray();
		
		$details = [];
		foreach ($detailRows as $item) {
			
			if (!isset($detailFields[$item['name']])) continue;
			
			$field = $detailFields[$item['name']];
			
			switch ($field['type']) {
				case 'image':
					$item['value'] = \URL::asset($item['value']);
					break;
				case 'checkbox':
					$item['value'] = explode(',', $item['value']);
					break;
			}
			
			$details[$item['article_id']][$item['name']] = [
				'text'  => $field['name_text'],
				'value' => $item['value'],
			];
		}
		
		foreach ($articles as &$item) {
			$item['detail'] = $details[$item['id']];
		}
		unset($item);
		
		return $articles;
	}
	# endregion
}