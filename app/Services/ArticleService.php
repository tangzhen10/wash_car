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
			
			if (empty($detail)) json_msg(trans('error.illegal_param'), 40003);
			
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
				if (in_array($field['type'], ['checkbox', 'images'])) {
					if (empty($detail['detail'][$field['name']])) $detail['detail'][$field['name']] = '';
					$detail['detail'][$field['name']] = explode(',', $detail['detail'][$field['name']]);
				}
			}
			
		} else {
			$detail = [
				'id'           => '0',
				'content_type' => \Request::input('content_type', '0'),
				'name'         => '',
				'sub_name'     => '',
				'start_time'   => '',
				'end_time'     => '',
				'sort'         => '0',
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
				'sort'         => empty($data['sort']) ? 0 : $data['sort'],
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
					case 'images':
						if (empty($value) || $value == [null]) {
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
		
		if (empty($filter['perPage'])) $filter['perPage'] = \SettingService::getValue('per_page');
		
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
			'a.sort',
			'a.status',
		];
		$listPage = \DB::table('article AS a')
		               ->join('content_type AS b', 'b.id', 'a.content_type')
		               ->join('manager AS c', 'c.id', 'a.create_by')
		               ->where('a.status', '!=', '-1');
		
		# 按文章标题筛选
		if (!empty($filter['filter_article_name'])) {
			$listPage = $listPage->where('a.name', 'LIKE', '%'.$filter['filter_article_name'].'%');
		}
		
		# 精确筛选
		$where = [];
		# 按类型筛选
		if (!empty($filter['filter_content_type'])) $where['a.content_type'] = $filter['filter_content_type'];
		
		$listPage = $listPage->where($where)
		                     ->select($fields)
		                     ->orderBy('a.sort', 'desc')
		                     ->orderBy('a.id', 'desc')
		                     ->paginate($filter['perPage']);
		$listArr  = json_decode(json_encode($listPage), 1);
		$total    = $listArr['total'];
		$list     = $listArr['data'];
		foreach ($list as &$item) {
			$item['start_time']  = intToTime($item['start_time']);
			$item['end_time']    = intToTime($item['end_time']);
			$item['create_at']   = intToTime($item['create_at']);
			$item['update_at']   = intToTime($item['update_at']);
			$item['status_text'] = trans('common.'.($item['status'] ? 'enable' : 'disable'));
		}
		unset($item);
		
		return compact('list', 'listPage', 'total');
	}
	
	/**
	 * 为文章池获取列表，显示更多
	 * @param int $contentType
	 * @param int $page
	 * @author 李小同
	 * @date   2018-7-22 00:31:42
	 * @return mixed
	 */
	public function getListForArticlePond($contentType, $page = 1) {
		
		$perPage = \SettingService::getValue('article_pond_product_per_page');
		$list    = \DB::table($this->module)
		              ->where('status', '!=', '-1')
		              ->where('content_type', $contentType)
		              ->offset(($page - 1) * $perPage)
		              ->limit($perPage)
		              ->get(['id', 'name', 'status', 'start_time', 'end_time'])
		              ->toArray();
		$now     = time();
		foreach ($list as &$item) {
			
			$iconEnable          = '<i class="Hui-iconfont c-success" title="'.trans('common.enable').'">&#xe6a8;</i>';
			$iconDisable         = '<i class="Hui-iconfont c-danger" title="'.trans('common.disable').'">&#xe706;</i>';
			$item['status_icon'] = $item['status'] ? $iconEnable : $iconDisable;
			$item['time_status'] = '<span class="c-success">'.trans('common.time_status_1').'</span>';
			if ($item['start_time'] > 0 && $item['start_time'] > $now) $item['time_status'] = '<span class="c-666">'.trans('common.time_status_2').'</span>';
			if ($item['end_time'] > 0 && $item['end_time'] < $now) $item['time_status'] = '<span class="c-danger">'.trans('common.time_status_3').'</span>';
		}
		unset($item);
		
		return $list;
	}
	
	# region 前台
	/**
	 * 获取文章列表
	 * @param array $filter
	 * @author 李小同
	 * @date   2018-7-15 23:02:17
	 * @return array
	 */
	public function getArticleList(array $filter = []) {
		
		$articles = $this->getArticlePublicInfo($filter);
		
		# 按article_id筛选
		if (isset($filter['article_id_arr'])) $articles = $articles->whereIn('id', $filter['article_id_arr']);
		
		$fields   = ['id', 'name', 'sub_name'];
		$articles = $articles->orderBy('sort', 'desc')->orderBy('id', 'desc')->get($fields)->toArray();
		
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
	
	/**
	 * 获取文章公共属性（基本信息）
	 * @param array $filter
	 * @author 李小同
	 * @date   2018-7-29 11:53:23
	 * @return array
	 */
	public function getArticleBaseInfo(array $filter = []) {
		
		$articles = $this->getArticlePublicInfo($filter);
		$articles = $articles->orderBy('sort', 'desc')->orderBy('id', 'desc')->get(['id', 'name'])->toArray();
		
		return $articles;
	}
	
	/**
	 * 获取文章公共属性（基本信息）
	 * @param array $filter
	 * @author 李小同
	 * @date   2018-7-29 11:53:23
	 * @return array
	 */
	public function getArticlePublicInfo(array $filter = []) {
		
		$filter['status'] = '1';
		if (!empty($filter['content_type'])) {
			$filter['content_type'] = intval($filter['content_type']);
		} else {
			json_msg('必须指定一种文档类型', 40001);
		}
		
		# 公共属性
		$now      = time();
		$articles = \DB::table('article')->where($filter)->where(function ($query) use ($now) {
			
			$query->where('start_time', 0)->orWhere('start_time', '<=', $now);
		})->where(function ($query) use ($now) {
			
			$query->where('end_time', 0)->orWhere('end_time', '>=', $now);
		});
		
		return $articles;
	}
	
	# endregion
}