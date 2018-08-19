@extends('admin.public_list')
@section('body')
	<div class="cl pd-5 bg-1 bk-gray mt-10">
		<a class="btn btn-primary radius" href="javascript:;" onclick="layer_show('添加洗车卡','{{route('cardForm')}}','1000')">
			<i class="Hui-iconfont">&#xe600;</i> 添加洗车卡
		</a>
		<span class="r">共有数据：<strong>{{count($list)}}</strong> 条</span>
	</div>
	<table class="table table-border table-bordered table-striped table-hover table-bg table-sort mt-10">
		<thead>
		<tr class="text-c">
			<th width="35">ID</th>
			<th width="150">{{trans('common.name')}}</th>
			<th width="40">{{trans('common.price')}}</th>
			<th width="90">{{trans('common.expire_date')}}</th>
			<th width="150">{{trans('common.hot')}}</th>
			<th width="150">{{trans('common.create_at')}}</th>
			<th width="150">{{trans('common.status')}}</th>
			<th width="100">{{trans('common.action')}}</th>
		</tr>
		</thead>
		<tbody>
		@foreach($list as $row)
			<tr class="text-c">
				<td>{{$row['id']}}</td>
				<td>{{$row['name']}}</td>
				<td>{{$row['price']}}</td>
				<td>{{$row['expire_date']}}</td>
				<td>{{$row['hot_status']}}</td>
				<td>{{$row['create_at']}}</td>
				<td class="td-status">
					<span class="label label-{{$row['status'] ? 'success' : 'danger'}} radius">{{$row['status_text']}}</span>
				</td>
				<td class="td-manage">
					@if ($row['status'] == '1')
						<a title="{{trans('common.disable')}}" href="javascript:;"
						   onClick="handleDataStop(this,'{{$row['id']}}','{{route('cardChangeStatus')}}')">
							<i class="Hui-iconfont">&#xe631;</i>
						</a>
					@else
						<a title="{{trans('common.enable')}}" href="javascript:;"
						   onClick="handleDataStart(this,'{{$row['id']}}','{{route('cardChangeStatus')}}')">
							<i class="Hui-iconfont">&#xe615;</i>
						</a>
					@endif
					
					<a title="{{trans('common.edit')}}" href="javascript:;" class="ml-5"
					   onclick="layer_show($(this).attr('title'),'{{route('cardForm', $row['id'])}}','1000','600')">
						<i class="Hui-iconfont">&#xe6df;</i>
					</a>
					<a title="{{trans('common.delete')}}" class="ml-5" href="javascript:;"
					   onclick="handleDataDel(this,'{{$row['id']}}','{{route('cardChangeStatus')}}')">
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
		
	</script>
@endsection