@extends('admin.public')
@section('body')
	<section class="Hui-article-box">
		<nav class="breadcrumb">
			<i class="Hui-iconfont">&#xe67f;</i> 首页
			<span class="c-gray en">&gt;</span> 管理员管理
			<span class="c-gray en">&gt;</span> 权限管理
			<a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新">
				<i class="Hui-iconfont">&#xe68f;</i>
			</a>
		</nav>
		<div class="Hui-article">
			<article class="cl pd-20">
				{{--<div class="text-c mb-20">
					<form class="Huiform" method="post" action="" target="_self">
						<input type="text" class="input-text" style="width:250px" placeholder="权限名称" id="" name="">
						<button type="submit" class="btn btn-success" id="" name=""><i class="Hui-iconfont">&#xe665;</i>
							搜权限节点
						</button>
					</form>
				</div>--}}
				<div class="cl pd-5 bg-1 bk-gray">
					<span class="l">
						<a href="javascript:;" onclick="datadel()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a>
						<a href="javascript:;" onclick="admin_permission_add(this,'{{route('permissionForm')}}','','400')" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 添加权限节点</a>
					</span>
					<span class="r">共有数据：<strong>54</strong> 条</span></div>
				<table class="table table-border table-bordered table-bg">
					<thead>
					<tr>
						<th scope="col" colspan="7">权限节点</th>
					</tr>
					<tr class="text-c">
						<th width="25"><input type="checkbox" name="" value=""></th>
						<th width="40">ID</th>
						<th width="200">权限名称</th>
						<th>路由</th>
						<th>排序</th>
						<th>状态</th>
						<th width="100">操作</th>
					</tr>
					</thead>
					<tbody>
					@foreach($permissions as $row)
						<tr class="text-c">
							<td><input type="checkbox" value="{{$row['id']}}" name=""></td>
							<td>{{$row['id']}}</td>
							<td style="text-align: left;{{$row['level'] == '1' ? 'font-weight:bold' : ''}};text-indent: {{($row['level'] - 1) * 30}}px;">{{$row['name']}}</td>
							<td>{{$row['route']}}</td>
							<td>{{$row['sort']}}</td>
							<td class="td-status">
								<span class="label label-{{$row['status'] ? 'success' : 'danger'}} radius">{{$row['status_text']}}</span>
							</td>
							<td class="td-manage">
								
								@if ($row['status'] == '1')
									<a title="{{trans('common.disable')}}" style="text-decoration:none"
									   onClick="permission_stop(this,'{{$row['id']}}')" href="javascript:;">
										<i class="Hui-iconfont">&#xe631;</i>
									</a>
								@else
									<a title="{{trans('common.enable')}}" style="text-decoration:none"
									   onClick="permission_start(this,'{{$row['id']}}')" href="javascript:;">
										<i class="Hui-iconfont">&#xe615;</i>
									</a>
								@endif
								
								<a title="{{trans('common.edit')}}" onclick="admin_permission_edit(this,'{{route('permissionForm', ['id' => $row['id']])}}','800','500')"
								   href="javascript:;" class="ml-5" style="text-decoration:none">
									<i class="Hui-iconfont">&#xe6df;</i>
								</a>
								<a title="{{trans('common.delete')}}" onclick="admin_permission_del(this,'{{$row['id']}}')"
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
	<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/My97DatePicker/4.8/WdatePicker.js')}}"></script>
	<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/datatables/1.10.0/jquery.dataTables.min.js')}}"></script>
	<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/laypage/1.2/laypage.js')}}"></script>
	<script type="text/javascript">
		/*
		 参数解释：
		 title	标题
		 url	请求的url
		 id		需要操作的数据id
		 w		弹出层宽度（缺省调默认值）
		 h		弹出层高度（缺省调默认值）
		 */
		/*管理员-权限-添加*/
		function admin_permission_add(obj, url, w, h) {
			var title = $(obj).text();
			layer_show(title, url, w, h);
		}
		/*管理员-权限-编辑*/
		function admin_permission_edit(obj, url, w, h) {
			var title = $(obj).attr('title');
			layer_show(title, url, w, h);
		}
		
		/*管理员-权限-删除*/
		function admin_permission_del(obj, id) {
			layer.confirm('角色删除须谨慎，确认要删除吗？', function (index) {
				//此处请求后台程序，下方是成功后的前台处理……
				$.ajax({
					url        : '{{route('permissionChangeStatus')}}',
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
		
		/*权限-停用*/
		function permission_stop(obj, id) {
			layer.confirm('确认要停用吗？', function (index) {
				//此处请求后台程序，下方是成功后的前台处理……
				$.ajax({
					url        : '{{route('permissionChangeStatus')}}',
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
							$(obj).parents("tr").find(".td-manage").prepend('<a onClick="permission_start(this,'+id+')" href="javascript:;" title="{{trans('common.enable')}}" style="text-decoration:none"><i class="Hui-iconfont">&#xe615;</i></a>');
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
		
		/*权限-启用*/
		function permission_start(obj, id) {
			layer.confirm('确认要启用吗？', function (index) {
				//此处请求后台程序，下方是成功后的前台处理……
				$.ajax({
					url        : '{{route('permissionChangeStatus')}}',
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
							$(obj).parents("tr").find(".td-manage").prepend('<a onClick="permission_stop(this,'+id+')" href="javascript:;" title="{{trans('common.disable')}}" style="text-decoration:none"><i class="Hui-iconfont">&#xe631;</i></a>');
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
@endsection