<?php
/**
 * Created by PhpStorm.
 * Sign: Nothing is true, everything is permitted.
 * User: 李小同
 * Date: 2018-11-10 22:28
 */

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class IndexController extends Controller {
	
	# 首页
	public function index() {
		
		return $this->article(55);
	}
	
	/**
	 * 静态文章展示
	 * @param $articleId
	 * @author 李小同
	 * @date   2018-11-10 22:50:31
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function article($articleId) {
		
		$article = \ArticleService::getDetailById($articleId);
		
		return view('web.common.article', $article);
	}
}