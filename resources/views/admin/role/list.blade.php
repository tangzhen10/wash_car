@extends('admin.public')
@section('body')
	<section class="Hui-article-box">
		@include('admin.breadcrumb')
		<div class="Hui-article">
			<article class="cl pd-20">
				<div class="cl pd-5 bg-1 bk-gray">
					<span class="l"> <a href="javascript:;" onclick="datadel()" class="btn btn-danger radius">
							<i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a>
						<a class="btn btn-primary radius" href="javascript:;" onclick="admin_role_add(this,'{{route('roleForm')}}','800')">
							<i class="Hui-iconfont">&#xe600;</i> 添加角色
						</a>
					</span>
					<span class="r">共有数据：<strong>{{count($roles)}}</strong> 条</span></div>
				<div class="mt-10">
					<table class="table table-border table-bordered table-hover table-bg">
						<thead>
						<tr>
							<th scope="col" colspan="6">角色管理</th>
						</tr>
						<tr class="text-c">
							<th width="25"><input type="checkbox" value="" name=""></th>
							<th width="40">ID</th>
							<th width="200">角色名</th>
							<th width="300">描述</th>
							<th width="70">操作</th>
						</tr>
						</thead>
						<tbody>
						@foreach($roles as $row)
							<tr class="text-c">
								<td><input type="checkbox" value="{{$row['id']}}" name=""></td>
								<td>{{$row['id']}}</td>
								<td>{{$row['name']}}</td>
								<td>{{$row['description']}}</td>
								<td class="f-14">
									<a title="编辑" href="javascript:;" onclick="admin_role_edit(this,'{{route('roleForm', $row['id'])}}')" style="text-decoration:none"><i class="Hui-iconfont">
											&#xe6df;</i></a>
									<a title="删除" href="javascript:;" onclick="admin_role_del(this,'{{$row['id']}}')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">
											&#xe6e2;</i></a></td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
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
		/*管理员-角色-添加*/
		function admin_role_add(obj, url, w, h) {
			var title = $(obj).text();
			layer_show(title, url, w, h);
		}
		/*管理员-角色-编辑*/
		function admin_role_edit(obj, url, w, h) {
			var title = $(obj).attr('title');
			layer_show(title, url, w, h);
		}
		
		/*管理员-角色-删除*/
		function admin_role_del(obj, id) {
			layer.confirm('角色删除须谨慎，确认要删除吗？', function (index) {
				//此处请求后台程序，下方是成功后的前台处理……
				$.ajax({
					url        : '{{route('roleChangeStatus')}}',
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
		
		/*角色-停用*/
		function role_stop(obj, id) {
			layer.confirm('确认要停用吗？', function (index) {
				//此处请求后台程序，下方是成功后的前台处理……
				$.ajax({
					url        : '{{route('roleChangeStatus')}}',
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
							$(obj).parents("tr").find(".td-manage").prepend('<a onClick="role_start(this,'+id+')" href="javascript:;" title="{{trans('common.enable')}}" style="text-decoration:none"><i class="Hui-iconfont">&#xe615;</i></a>');
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
		
		/*角色-启用*/
		function role_start(obj, id) {
			layer.confirm('确认要启用吗？', function (index) {
				//此处请求后台程序，下方是成功后的前台处理……
				$.ajax({
					url        : '{{route('roleChangeStatus')}}',
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
							$(obj).parents("tr").find(".td-manage").prepend('<a onClick="role_stop(this,'+id+')" href="javascript:;" title="{{trans('common.disable')}}" style="text-decoration:none"><i class="Hui-iconfont">&#xe631;</i></a>');
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