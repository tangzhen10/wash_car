@extends('admin.public')
@section('body')
	<div class="cl pd-5 bg-1 bk-gray">
		<span class="r">共有数据：<strong>{{count($list)}}</strong> 条</span>
	</div>
	<table class="table table-border table-bordered table-striped table-hover table-bg table-sort mt-10">
		<thead>
		<tr class="text-c">
			<th width="40">ID</th>
			<th width="200">{{trans('common.name')}}</th>
			<th width="100">{{trans('common.status')}}</th>
		</tr>
		</thead>
		<tbody>
		@foreach($list as $row)
			<tr class="text-c">
				<td>{{$row['id']}}</td>
				<td>{{$row['name']}}</td>
				<td class="td-status">
					<span class="label label-{{$row['status'] ? 'success' : 'danger'}} radius">{{$row['status_text']}}</span>
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
@endsection