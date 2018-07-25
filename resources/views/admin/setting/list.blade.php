@extends('admin.public')
@section('body')
	<table class="table table-border table-bordered table-striped table-hover table-bg">
		<thead>
		<tr class="text-c">
			<th width="40">ID</th>
			<th width="100">{{trans('common.name')}}</th>
			<th width="150">{{trans('common.value')}}</th>
			<th width="200">{{trans('common.description')}}</th>
			<th width="100">{{trans('common.action')}}</th>
		</tr>
		</thead>
		<tbody>
		@foreach($list as $row)
			<tr class="text-c">
				<td class="row_id">{{$row['id']}}</td>
				<td class="row_name">{{$row['name']}}</td>
				<td class="row_value">{{$row['value']}}</td>
				<td class="row_description">{{$row['description']}}</td>
				<td>
					<a title="{{trans('common.edit')}}" href="javascript:;" class="ml-5 J_edit">
						<i class="Hui-iconfont">&#xe6df;</i>
					</a>
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
@endsection
@section('js')
	<script>
		$('.J_edit').click(function () {
			var id    = $(this).parents('tr').find('.row_id').text(),
			    _this = this;
			layer.prompt({
				formType : 2,
				value    : $(this).parents('tr').find('.row_value').text(),
				title    : $(this).parents('tr').find('.row_description').text()
			}, function (value, index) {
				$.ajax({
					url        : '{{route('saveSetting')}}/'+id,
					type       : 'post',
					data       : {
						id    : id,
						value : value
					},
					beforeSend : function () {layer.load(3)},
					success    : function () {
						layer.close(layer.load());
						layer.close(index);
						$(_this).parents('tr').find('.row_value').text(value);
					}
				});
			});
		});
	</script>
@endsection