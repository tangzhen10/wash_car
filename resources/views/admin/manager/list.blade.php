@extends('admin.public_list')
@section('body')
	<div class="text-c">
		<input type="text" class="input-text" style="width:250px" placeholder="输入管理员名称" id="" name="">
		<button type="submit" class="btn btn-success radius J_filter">
			<i class="Hui-iconfont">&#xe665;</i> {{trans('common.filter')}}
		</button>
	</div>
	<div class="cl pd-5 bg-1 bk-gray mt-10">
		<span class="l">
			<a href="javascript:;" onclick="batch_delete('{{route('batchDeleteManager')}}')" class="btn btn-danger radius">
				<i class="Hui-iconfont">&#xe6e2;</i> 批量删除
			</a>
			<a href="javascript:;" onclick="layer_show('添加管理员','{{route('managerForm')}}','800','500')" class="btn btn-primary radius">
				<i class="Hui-iconfont">&#xe600;</i> 添加管理员
			</a>
		</span>
		<span class="r">共有数据：<strong>{{count($managers)}}</strong> 条</span>
	</div>
	<table class="table table-border table-bordered table-striped table-hover table-bg mt-10">
		<thead>
		<tr class="text-c">
			<th width="25"><input type="checkbox" name="" value=""></th>
			<th width="40">ID</th>
			<th>{{trans('common.name')}}</th>
			<th>{{trans('common.role')}}</th>
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
						<a title="{{trans('common.disable')}}"
						   onClick="handleDataStop(this,'{{$row['id']}}','{{route('managerChangeStatus')}}')" href="javascript:;">
							<i class="Hui-iconfont">&#xe631;</i>
						</a>
					@else
						<a title="{{trans('common.enable')}}"
						   onClick="handleDataStart(this,'{{$row['id']}}','{{route('managerChangeStatus')}}')" href="javascript:;">
							<i class="Hui-iconfont">&#xe615;</i>
						</a>
					@endif
					
					<a title="{{trans('common.edit')}}" onclick="layer_show($(this).attr('title'),'{{route('managerForm', $row['id'])}}','800','500')"
					   href="javascript:;" class="ml-5">
						<i class="Hui-iconfont">&#xe6df;</i>
					</a>
					<a title="{{trans('common.delete')}}" onclick="handleDataDel(this,'{{$row['id']}}','{{route('managerChangeStatus')}}')"
					   class="ml-5" href="javascript:;">
						<i class="Hui-iconfont">&#xe6e2;</i>
					</a>
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
@endsection