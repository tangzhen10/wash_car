@extends('admin.public')
@section('body')
	<table class="table table-border table-bordered table-striped table-hover table-bg table-sort">
		<thead>
		<tr class="text-c">
			{{--<th width="25"><input type="checkbox"></th>--}}
			<th width="40">ID</th>
			<th width="80">{{trans('common.first_letter')}}</th>
			<th width="100">{{trans('common.logo')}}</th>
			<th width="200">{{trans('common.name')}}</th>
			<th width="150">{{trans('common.name_en')}}</th>
			<th width="100">{{trans('common.hot_value')}}</th>
			<th width="100">{{trans('common.status')}}</th>
			<th width="100">{{trans('common.action')}}</th>
		</tr>
		</thead>
		<tbody>
		@foreach($list as $row)
			<tr class="text-c">
				{{--<td><input type="checkbox"></td>--}}
				<td>{{$row['id']}}</td>
				<td>{{$row['first_letter']}}</td>
				<td>
					@if (!empty($row['logo']))
						<img src="{{URL::asset($row['logo'])}}" class="avatar J_car_brand" alt="{{$row['name']}}">
					@endif
				</td>
				<td>{{$row['name']}}</td>
				<td>{{$row['name_en']}}</td>
				<td>
					@if ($row['hot'] > 0)
						<i class="Hui-iconfont c-red f-20">&#xe6c1;</i> × {{$row['hot']}}
					@endif
				</td>
				<td class="td-status">
					<span class="label label-{{$row['status'] ? 'success' : 'danger'}} radius">{{$row['status_text']}}</span>
				</td>
				<td class="td-manage">
					
					@if ($row['status'] == '1')
						<a title="{{trans('common.disable')}}" style="text-decoration:none"
						   onClick="handleDataStop(this,'{{$row['id']}}','{{route('brandChangeStatus')}}')" href="javascript:;">
							<i class="Hui-iconfont">&#xe631;</i>
						</a>
					@else
						<a title="{{trans('common.enable')}}" style="text-decoration:none"
						   onClick="handleDataStart(this,'{{$row['id']}}','{{route('brandChangeStatus')}}')" href="javascript:;">
							<i class="Hui-iconfont">&#xe615;</i>
						</a>
					@endif
					
					<a title="{{trans('common.edit')}}" onclick="layer_show($(this).attr('title'),'{{route('brandForm', $row['id'])}}','800','500')"
					   href="javascript:;" class="ml-5" style="text-decoration:none">
						<i class="Hui-iconfont">&#xe6df;</i>
					</a>
					<a title="{{trans('common.delete')}}" onclick="handleDataDel(this,'{{$row['id']}}','{{route('brandChangeStatus')}}')"
					   class="ml-5" style="text-decoration:none" href="javascript:;">
						<i class="Hui-iconfont">&#xe6e2;</i>
					</a>
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
@endsection
@section('js')
	<script>
		// 点击查看logo大图
		$('.J_car_brand').css({cursor : 'pointer'}).click(function () {
			layer.open({
				type       : 1,
				title      : $(this).attr('alt'),
				shade      : 0.5,
				closeBtn   : 0,
				shadeClose : true,
				content    : '<img class="pd-20" src="'+$(this).attr('src')+'">'
			});
		});
	</script>
@endsection