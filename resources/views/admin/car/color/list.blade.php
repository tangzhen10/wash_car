@extends('admin.public_list')
@section('body')
	<div class="cl pd-5 bg-1 bk-gray">
		<span class="l">
			<a href="javascript:;" class="btn btn-primary radius"
			   onclick="layer_show('添加颜色','{{route('colorForm')}}','800','320')">
				<i class="Hui-iconfont">&#xe600;</i> 添加颜色
			</a>
		</span>
		<span class="r">{!!sprintf(trans('common.total_count'), count($list))!!}</span>
	</div>
	<table class="table table-border table-bordered table-striped table-hover table-bg table-sort mt-10">
		<thead>
		<tr class="text-c">
			<th width="40">ID</th>
			<th width="200">{{trans('common.name')}}</th>
			<th width="100">{{trans('common.color_code')}}</th>
			<th width="100">{{trans('common.preview')}}</th>
			<th width="100">{{trans('common.sort')}}</th>
			<th width="100">{{trans('common.status')}}</th>
			<th width="100">{{trans('common.action')}}</th>
		</tr>
		</thead>
		<tbody>
		@foreach($list as $row)
			<tr class="text-c">
				<td>{{$row['id']}}</td>
				<td>{{$row['name']}}</td>
				<td>{{$row['code']}}</td>
				<td>
					<span class="round" style="display: inline-block;width: 20px;height: 20px;background: #{{$row['code']}};"></span>
				</td>
				<td>{{$row['sort']}}</td>
				<td class="td-status">
					<span class="label label-{{$row['status'] ? 'success' : 'danger'}} radius">{{$row['status_text']}}</span>
				</td>
				<td class="td-manage">
					@if ($row['id'])
						@if ($row['status'] == '1')
							<a title="{{trans('common.disable')}}" href="javascript:;"
							   onClick="handleDataStop(this,'{{$row['id']}}','{{route('colorChangeStatus')}}')">
								<i class="Hui-iconfont">&#xe631;</i>
							</a>
						@else
							<a title="{{trans('common.enable')}}" href="javascript:;"
							   onClick="handleDataStart(this,'{{$row['id']}}','{{route('colorChangeStatus')}}')">
								<i class="Hui-iconfont">&#xe615;</i>
							</a>
						@endif
						
						<a title="{{trans('common.edit')}}" href="javascript:;" class="ml-5"
						   onclick="layer_show($(this).attr('title'),'{{route('colorForm', $row['id'])}}','800','320')">
							<i class="Hui-iconfont">&#xe6df;</i>
						</a>
						<a title="{{trans('common.delete')}}" class="ml-5" href="javascript:;"
						   onclick="handleDataDel(this,'{{$row['id']}}','{{route('colorChangeStatus')}}')">
							<i class="Hui-iconfont">&#xe6e2;</i>
						</a>
					@endif
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
@endsection