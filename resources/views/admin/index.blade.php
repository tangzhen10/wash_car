@extends('admin.public_list')
@section('body')
	<p class="f-20 text-success">欢迎您，{{$manager['name']}}！</p>
	
	<table class="table table-border table-bordered table-bg">
		<thead>
		<tr>
			<th colspan="7" scope="col">信息统计</th>
		</tr>
		<tr class="text-c">
			<th>订单总量</th>
			<th>今日订单量</th>
			<th>产品</th>
			<th>会员</th>
			<th>管理员</th>
		</tr>
		</thead>
		<tbody>
		<tr class="text-c">
			<td><a href="{{route('washOrderList')}}">{{$total['order']}}</a></td>
			<td><a href="{{route('washOrderList')}}?filter_date_from={{date('Y-m-d')}}&filter_date_to={{date('Y-m-d')}}">
					{{$total['order_today']}}</a></td>
			<td><a href="{{route('productList')}}">{{$total['product']}}</a></td>
			<td><a href="{{route('memberList')}}">{{$total['member']}}</a></td>
			<td><a href="{{route('managerList')}}">{{$total['manager']}}</a></td>
		</tr>
		</tbody>
	</table>
	<table class="table table-border table-bordered table-bg mt-20 mb-20">
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
	
	<footer class="footer">
		<p>&copy;版权所有，侵权必究</p>
	</footer>
@endsection