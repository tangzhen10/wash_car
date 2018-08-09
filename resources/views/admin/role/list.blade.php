@extends('admin.public_list')
@section('body')
	<div class="cl pd-5 bg-1 bk-gray">
		<span class="l">
			<a href="javascript:;" onclick="batch_delete('{{route('batchDeleteRole')}}')" class="btn btn-danger radius">
				<i class="Hui-iconfont">&#xe6e2;</i> 批量删除
			</a>
			<a class="btn btn-primary radius" href="javascript:;" onclick="layer_show('添加角色','{{route('roleForm')}}','800')">
				<i class="Hui-iconfont">&#xe600;</i> 添加角色
			</a>
		</span>
		<span class="r">共有数据：<strong>{{count($roles)}}</strong> 条</span>
	</div>
	<table class="table table-border table-bordered table-hover table-bg mt-10">
		<thead>
		<tr class="text-c">
			<th width="25"><input type="checkbox"></th>
			<th width="40">ID</th>
			<th width="200">{{trans('common.name')}}</th>
			<th width="300">{{trans('common.description')}}</th>
			<th width="70">{{trans('common.action')}}</th>
		</tr>
		</thead>
		<tbody>
		@foreach($roles as $row)
			<tr class="text-c">
				<td><input type="checkbox" value="{{$row['id']}}"></td>
				<td>{{$row['id']}}</td>
				<td>{{$row['name']}}</td>
				<td>{{$row['description']}}</td>
				<td class="f-14 td-manage">
					<a title="{{trans('common.view').trans('common.manager')}}" href="javascript:;"
					   onclick="layer_show($(this).attr('title'),'{{route('roleManager', ['id' => $row['id']])}}', 600, 600)">
						<i class="Hui-iconfont">&#xe725;</i></a>
					<a title="{{trans('common.edit')}}" href="javascript:;"
					   onclick="layer_show($(this).attr('title'),'{{route('roleForm', $row['id'])}}')">
						<i class="Hui-iconfont">&#xe6df;</i></a>
					<a title="{{trans('common.delete')}}" href="javascript:;" class="ml-5"
					   onclick="handleDataDel(this,'{{$row['id']}}','{{route('roleChangeStatus')}}')">
						<i class="Hui-iconfont">&#xe6e2;</i></a>
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
@endsection