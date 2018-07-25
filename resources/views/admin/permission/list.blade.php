@extends('admin.public')
@section('body')
	<div class="cl pd-5 bg-1 bk-gray">
		<span class="l">
			<a href="javascript:;" onclick="layer_show('添加权限节点','{{route('permissionForm')}}','','400')" class="btn btn-primary radius">
				<i class="Hui-iconfont">&#xe600;</i> 添加权限节点
			</a>
		</span>
		<span class="r">共有数据：<strong>{{count($permissions)}}</strong> 条</span></div>
	<table class="table table-border table-bordered table-striped table-hover table-bg">
		<thead>
		<tr class="text-c">
			<th width="40">ID</th>
			<th width="200">权限名称</th>
			<th>路由</th>
			<th>排序</th>
			<th>状态</th>
			<th>显示</th>
			<th width="100">操作</th>
		</tr>
		</thead>
		<tbody>
		@foreach($permissions as $row)
			<tr class="text-c">
				<td>{{$row['id']}}</td>
				<td style="text-align: left;{{$row['level'] == '1' ? 'font-weight:bold' : ''}};text-indent: {{($row['level'] - 1) * 30}}px;">{{$row['name']}}</td>
				<td>{{$row['route']}}</td>
				<td>{{$row['sort']}}</td>
				<td class="td-status">
					<span class="label label-{{$row['status'] ? 'success' : 'danger'}} radius">{{$row['status_text']}}</span>
				</td>
				<td style="font-size: 20px;"><?= $row['show'] ? '<i class="Hui-iconfont">&#xe725;</i>' : ''?></td>
				<td class="td-manage">
					
					@if ($row['status'] == '1')
						<a title="{{trans('common.disable')}}"
						   onClick="handleDataStop(this,'{{$row['id']}}','{{route('permissionChangeStatus')}}')" href="javascript:;">
							<i class="Hui-iconfont">&#xe631;</i>
						</a>
					@else
						<a title="{{trans('common.enable')}}"
						   onClick="handleDataStart(this,'{{$row['id']}}','{{route('permissionChangeStatus')}}')" href="javascript:;">
							<i class="Hui-iconfont">&#xe615;</i>
						</a>
					@endif
					
					<a title="{{trans('common.edit')}}" onclick="layer_show($(this).attr('title'),'{{route('permissionForm', $row['id'])}}','800','500')"
					   href="javascript:;" class="ml-5">
						<i class="Hui-iconfont">&#xe6df;</i>
					</a>
					<a title="{{trans('common.delete')}}" onclick="handleDataDel(this,'{{$row['id']}}','{{route('permissionChangeStatus')}}')"
					   class="ml-5" href="javascript:;">
						<i class="Hui-iconfont">&#xe6e2;</i>
					</a>
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
@endsection