@extends('admin.public')
@section('body')
	
	<section class="Hui-article-box">
		<nav class="breadcrumb"><i class="Hui-iconfont"></i>
			<span class="c-666">首页</span>
			<span class="c-999 en">&gt;</span>
			<span class="c-666">我的桌面</span>
			<a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);"
			   title="刷新"><i class="Hui-iconfont">&#xe68f;</i></a>
		</nav>
		<div class="Hui-article">
			<article class="cl pd-20">
				<p class="f-20 text-success">欢迎您，{{$manager['name']}}！</p>
				
				<table class="table table-border table-bordered table-bg">
					<thead>
					<tr>
						<th colspan="7" scope="col">信息统计</th>
					</tr>
					<tr class="text-c">
						<th>统计</th>
						<th>产品分类</th>
						<th>产品栏目</th>
						<th>产品</th>
						<th>管理员</th>
					</tr>
					</thead>
					<tbody>
					<tr class="text-c">
						<td>总数</td>
						<td>{{$total['category']}}</td>
						<td>{{$total['tab']}}</td>
						<td>{{$total['product']}}</td>
						<td>{{$total['manager']}}</td>
					</tr>
					</tbody>
				</table>
				<table class="table table-border table-bordered table-bg mt-20">
					<thead>
					<tr>
						<th colspan="2" scope="col">服务器信息</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td>服务器IP地址</td>
						<td>{{$server['SERVER_ADDR']}}</td>
					</tr>
					<tr>
						<td>服务器域名</td>
						<td>{{$server['SERVER_NAME']}}</td>
					</tr>
					<tr>
						<td>服务器端口</td>
						<td>{{$server['SERVER_PORT']}}</td>
					</tr>
					<tr>
						<td>服务器IIS版本</td>
						<td>{{$server['SERVER_SOFTWARE']}}</td>
					</tr>
					<tr>
						<td>User Agent</td>
						<td>{{$server['HTTP_USER_AGENT']}}</td>
					</tr>
					<tr>
						<td>本文件所在文件夹</td>
						<td>{{$server['DOCUMENT_ROOT']}}</td>
					</tr>
					<tr>
						<td>服务器时区</td>
						<td>{{$server['TIMEZONE']}}</td>
					</tr>
					<tr>
						<td>服务器当前时间</td>
						<td>{{date('Y-m-d H:i:s')}}</td>
					</tr>
					</tbody>
				</table>
			</article>
			<footer class="footer">
				<p>
					Copyright &copy;2015 H-ui.admin v3.0 All Rights Reserved.<br>
			</footer>
		</div>
	</section>
@endsection