@extends('admin.public_form')
@section('body')
	<div class="text-c">
		<h2>{{$detail['name']}}</h2>
		<p>{{$detail['description']}}</p>
		
		<table class="table table-border table-bordered table-bg table-striped table-hover table-sort" style="width: 300px;margin: 0 auto;">
			<thead>
			<tr class="text-c">
				<th>{{trans('common.manager_name')}}</th>
				<th>{{trans('common.action')}}</th>
			</tr>
			</thead>
			@foreach($managers as $manager)
				<tr class="text-c">
					<td>{{$manager['name']}}</td>
					<td class="td-manage">
						<a title="{{trans('common.delete')}}" class="ml-5 J_remove_manager" style="text-decoration:none" href="javascript:;"
						   data_manager_id="{{$manager['id']}}">
							<i class="Hui-iconfont">&#xe6e2;</i>
						</a>
					</td>
				</tr>
			@endforeach
		</table>
	</div>
@endsection
@section('js')
	<script>
		
		// 移除管理员
		$('.J_remove_manager').click(function () {
			
			var manager_id = $(this).attr('data_manager_id'),
			    role_id    = '{{$detail['id']}}',
			    _this      = this;
			layer.confirm('确认要删除吗？', function () {
				$.ajax({
					url        : '{{route('removeManager')}}',
					data       : {
						role_id    : role_id,
						manager_id : manager_id
					},
					type       : 'post',
					dataType   : 'json',
					beforeSend : function () {layer.load(3)},
					success    : function (data) {
						layer.close(layer.load());
						if (data.code == 0 && data.msg == 'ok') {
							$(_this).parents("tr").remove();
							layer.msg('已删除!', {icon : 1, time : 1000});
						} else {
							layer.msg(data.error, function () {});
						}
					}
				});
			});
		});
	</script>
@endsection