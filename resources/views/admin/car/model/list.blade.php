@extends('admin.public_list')
@section('body')
	<div class="cl pd-5 bg-1 bk-gray">
		<span class="l">
			@if ($filter['brand_id'])
				<a href="javascript:history.back();" class="btn btn-secondary radius">
					<i class="Hui-iconfont">&#xe66b;</i> 返回
				</a>
			@endif
			
			<a href="javascript:;" class="btn btn-primary radius"
			   onclick="layer_show('添加车型','{{route('modelForm')}}?brand_id={{$filter['brand_id']}}','800','300')">
				<i class="Hui-iconfont">&#xe600;</i> 添加车型
			</a>
		</span>
		<span class="r">共有数据：<strong>{{$total}}</strong> 条</span>
	</div>
	<table class="table table-border table-bordered table-striped table-hover table-bg table-sort mt-10">
		<thead>
		<tr class="text-c">
			{{--<th width="25"><input type="checkbox"></th>--}}
			<th width="40">ID</th>
			<th width="100">{{trans('common.brand')}}</th>
			<th width="200">{{trans('common.name')}}</th>
			<th width="100">{{trans('common.status')}}</th>
			<th width="100">{{trans('common.action')}}</th>
		</tr>
		</thead>
		<tbody>
		@foreach($list as $row)
			<tr class="text-c">
				{{--<td><input type="checkbox"></td>--}}
				<td>{{$row['id']}}</td>
				<td>
					{{$row['brand_name']}}
					@if ($row['brand_status'] == '0')
						<span class="label label-danger' radius">{{trans('common.disable')}}</span>
					@endif
				</td>
				<td>{{$row['name']}}</td>
				<td class="td-status">
					<span class="label label-{{$row['status'] ? 'success' : 'danger'}} radius">{{$row['status_text']}}</span>
				</td>
				<td class="td-manage">
					
					@if ($row['status'] == '1')
						<a title="{{trans('common.disable')}}" href="javascript:;"
						   onClick="handleDataStop(this,'{{$row['id']}}','{{route('modelChangeStatus')}}')">
							<i class="Hui-iconfont">&#xe631;</i>
						</a>
					@else
						<a title="{{trans('common.enable')}}" href="javascript:;"
						   onClick="handleDataStart(this,'{{$row['id']}}','{{route('modelChangeStatus')}}')">
							<i class="Hui-iconfont">&#xe615;</i>
						</a>
					@endif
					
					<a title="{{trans('common.edit')}}" href="javascript:;" class="ml-5"
					   onclick="layer_show($(this).attr('title'),'{{route('modelForm', $row['id'])}}?brand_id={{$filter['brand_id']}}','800','300')">
						<i class="Hui-iconfont">&#xe6df;</i>
					</a>
					<a title="{{trans('common.delete')}}" class="ml-5" href="javascript:;"
					   onclick="handleDataDel(this,'{{$row['id']}}','{{route('modelChangeStatus')}}')">
						<i class="Hui-iconfont">&#xe6e2;</i>
					</a>
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
	{{$pagination->render()}}
@endsection