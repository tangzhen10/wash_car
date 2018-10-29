@extends('admin.public_list')
@section('body')
	<div class="cl pd-5 bg-1 bk-gray">
		<span class="l">
			<a href="javascript:;" onclick="layer_show('添加模板','{{route('contentTypeForm')}}','1200','600')" class="btn btn-primary radius">
				<i class="Hui-iconfont">&#xe600;</i> 添加模板
			</a>
		</span>
		<span class="r">{!!sprintf(trans('common.total_count'), count($typeList))!!}</span>
	</div>
	<table class="table table-border table-bordered table-striped table-hover table-bg mt-10">
		<thead>
		<tr class="text-c">
			<th width="40">ID</th>
			<th width="100">{{trans('common.name')}}</th>
			<th width="100">{{trans('common.status')}}</th>
			<th width="100">{{trans('common.action')}}</th>
		</tr>
		</thead>
		<tbody>
		@foreach($typeList as $row)
			<tr class="text-c">
				<td>{{$row['id']}}</td>
				<td>{{$row['name']}}</td>
				<td class="td-status">
					<span class="label label-{{$row['status'] ? 'success' : 'danger'}} radius">{{$row['status_text']}}</span>
				</td>
				<td class="td-manage">
					
					@if ($row['status'] == '1')
						<a title="{{trans('common.disable')}}"
						   onClick="handleDataStop(this,'{{$row['id']}}', '{{route('contentTypeChangeStatus')}}')" href="javascript:;">
							<i class="Hui-iconfont">&#xe631;</i>
						</a>
					@else
						<a title="{{trans('common.enable')}}"
						   onClick="handleDataStart(this,'{{$row['id']}}', '{{route('contentTypeChangeStatus')}}')" href="javascript:;">
							<i class="Hui-iconfont">&#xe615;</i>
						</a>
					@endif
					
					<a title="{{trans('common.edit')}}" onclick="layer_show($(this).attr('title'), '{{route('contentTypeForm', $row['id'])}}','1200','600')"
					   href="javascript:;" class="ml-5">
						<i class="Hui-iconfont">&#xe6df;</i>
					</a>
					<a title="{{trans('common.delete')}}" onclick="handleDataDel(this,'{{$row['id']}}', '{{route('contentTypeChangeStatus')}}')"
					   class="ml-5" href="javascript:;">
						<i class="Hui-iconfont">&#xe6e2;</i>
					</a>
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
@endsection
