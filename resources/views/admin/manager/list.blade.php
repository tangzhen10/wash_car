@extends('admin.public')
@section('body')
	<section class="Hui-article-box">
		<nav class="breadcrumb">
			<i class="Hui-iconfont">&#xe67f;</i> 首页
			<span class="c-gray en">&gt;</span> 管理员管理
			<span class="c-gray en">&gt;</span> 管理员列表
			<a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新">
				<i class="Hui-iconfont">&#xe68f;</i>
			</a>
		</nav>
		<div class="Hui-article">
			<article class="cl pd-20">
				<div class="text-c"> 日期范围：
					<input type="text" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'datemax\')||\'%y-%M-%d\'}'})" id="datemin" class="input-text Wdate" style="width:120px;">
				                     -
					<input type="text" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'datemin\')}',maxDate:'%y-%M-%d'})" id="datemax" class="input-text Wdate" style="width:120px;">
					<input type="text" class="input-text" style="width:250px" placeholder="输入管理员名称" id="" name="">
					<button type="submit" class="btn btn-success" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜用户
					</button>
				</div>
				<div class="cl pd-5 bg-1 bk-gray mt-20">
					<span class="l">
						<a href="javascript:;" onclick="datadel()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a>
						<a href="javascript:;" onclick="admin_add('添加管理员','{{route('managerForm')}}','800','500')" class="btn btn-primary radius">
							<i class="Hui-iconfont">&#xe600;</i> 添加管理员
						</a>
					</span>
					<span class="r">共有数据：<strong>54</strong> 条</span>
				</div>
				<table class="table table-border table-bordered table-bg">
					<thead>
					<tr>
						<th scope="col" colspan="9">员工列表</th>
					</tr>
					<tr class="text-c">
						<th width="25"><input type="checkbox" name="" value=""></th>
						<th width="40">ID</th>
						<th>登录名</th>
						<th>角色</th>
						<th>{{trans('common.create_at')}}</th>
						<th>上次登录时间</th>
						<th>上次登录ip</th>
						<th width="100">{{trans('common.status')}}</th>
						<th width="100">{{trans('common.action')}}</th>
					</tr>
					</thead>
					<tbody>
					@foreach($managers as $row)
						<tr class="text-c">
							<td><input type="checkbox" value="{{$row['id']}}" name=""></td>
							<td>{{$row['id']}}</td>
							<td>{{$row['name']}}</td>
							<td>{{$row['role']}}</td>
							<td>{{$row['create_at']}}</td>
							<td>{{$row['last_login_at']}}</td>
							<td>{{$row['last_login_ip']}}</td>
							<td class="td-status">
								<span class="label label-{{$row['status'] ? 'success' : 'danger'}} radius">{{$row['status_text']}}</span>
							</td>
							<td class="td-manage">
								
								@if ($row['status'] == '1')
									<a title="{{trans('common.disable')}}" style="text-decoration:none"
									   onClick="admin_stop(this,'{{$row['id']}}')" href="javascript:;">
										<i class="Hui-iconfont">&#xe631;</i>
									</a>
								@else
									<a title="{{trans('common.enable')}}" style="text-decoration:none"
									   onClick="admin_start(this,'{{$row['id']}}')" href="javascript:;">
										<i class="Hui-iconfont">&#xe615;</i>
									</a>
								@endif
								
								<a title="{{trans('common.edit')}}" onclick="admin_edit(this,'{{route('managerForm', $row['id'])}}','800','500')"
								   href="javascript:;" class="ml-5" style="text-decoration:none">
									<i class="Hui-iconfont">&#xe6df;</i>
								</a>
								<a title="{{trans('common.delete')}}" onclick="admin_del(this,'{{$row['id']}}')"
								   class="ml-5" style="text-decoration:none" href="javascript:;">
									<i class="Hui-iconfont">&#xe6e2;</i>
								</a>
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</article>
		</div>
	</section>
@endsection
@section('js')
	<!--请在下方写此页面业务相关的脚本-->
	<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/My97DatePicker/4.8/WdatePicker.js')}}"></script>
	<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/datatables/1.10.0/jquery.dataTables.min.js')}}"></script>
	<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/laypage/1.2/laypage.js')}}"></script>
	<script type="text/javascript">
		/*
		 参数解释：
		 title	标题
		 url		请求的url
		 id		需要操作的数据id
		 w		弹出层宽度（缺省调默认值）
		 h		弹出层高度（缺省调默认值）
		 */
		/*管理员-增加*/
		function admin_add(title, url, w, h) {
			layer_show(title, url, w, h);
		}
		/*管理员-删除*/
		function admin_del(obj, id) {
			layer.confirm('确认要删除吗？', function (index) {
				//此处请求后台程序，下方是成功后的前台处理……
				$.ajax({
					url        : '{{route('managerChangeStatus')}}',
					data       : {
						id     : id,
						status : '-1'
					},
					type       : 'post',
					dataType   : 'json',
					beforeSend : function () {layer.load(2)},
					success    : function (data) {
						layer.close(layer.load());
						if (data.code == 0 && data.msg == 'ok') {
							$(obj).parents("tr").remove();
							layer.msg('已删除!', {icon : 1, time : 1000});
						} else {
							layer.msg(data.error, function () {});
						}
					}
				});
			});
		}
		/*管理员-编辑*/
		function admin_edit(obj, url, w, h) {
			var title = $(obj).attr('title');
			layer_show(title, url, w, h);
		}
		/*管理员-停用*/
		function admin_stop(obj, id) {
			layer.confirm('确认要停用吗？', function (index) {
				//此处请求后台程序，下方是成功后的前台处理……
				$.ajax({
					url        : '{{route('managerChangeStatus')}}',
					data       : {
						id     : id,
						status : '0'
					},
					type       : 'post',
					dataType   : 'json',
					beforeSend : function () {layer.load(2)},
					success    : function (data) {
						layer.close(layer.load());
						if (data.code == 0 && data.msg == 'ok') {
							$(obj).parents("tr").find(".td-manage").prepend('<a onClick="admin_start(this,'+id+')" href="javascript:;" title="{{trans('common.enable')}}" style="text-decoration:none"><i class="Hui-iconfont">&#xe615;</i></a>');
							$(obj).parents("tr").find(".td-status").html('<span class="label label-danger radius">{{trans('common.disable')}}</span>');
							$(obj).remove();
							layer.msg('{{trans('common.disable')}}！', {icon : 5, time : 1000});
						} else {
							layer.msg(data.error, function () {});
						}
					}
				});
			});
		}
		
		/*管理员-启用*/
		function admin_start(obj, id) {
			layer.confirm('确认要启用吗？', function (index) {
				//此处请求后台程序，下方是成功后的前台处理……
				$.ajax({
					url        : '{{route('managerChangeStatus')}}',
					data       : {
						id     : id,
						status : '1'
					},
					type       : 'post',
					dataType   : 'json',
					beforeSend : function () {layer.load(2)},
					success    : function (data) {
						layer.close(layer.load());
						if (data.code == 0 && data.msg == 'ok') {
							$(obj).parents("tr").find(".td-manage").prepend('<a onClick="admin_stop(this,'+id+')" href="javascript:;" title="{{trans('common.disable')}}" style="text-decoration:none"><i class="Hui-iconfont">&#xe631;</i></a>');
							$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">{{trans('common.enable')}}</span>');
							$(obj).remove();
							layer.msg('{{trans('common.enable')}}！', {icon : 6, time : 1000});
						} else {
							layer.msg(data.error, function () {});
						}
					}
				});
			});
		}
	</script>
	<!--/请在上方写此页面业务相关的脚本-->
@endsection
