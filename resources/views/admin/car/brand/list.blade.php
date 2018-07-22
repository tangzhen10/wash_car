@extends('admin.public')
@section('body')
	<table class="table table-border table-bordered table-striped table-hover table-bg">
		<thead>
		<tr class="text-c">
			<th width="25"><input type="checkbox"></th>
			<th width="40">ID</th>
			<th width="150">{{trans('common.name')}}</th>
			<th width="200">{{trans('common.logo')}}</th>
			<th width="150">{{trans('common.name_en')}}</th>
			<th width="150">{{trans('common.first_letter')}}</th>
			<th width="100">{{trans('common.status')}}</th>
		</tr>
		</thead>
		<tbody>
			@foreach($list as $row)
				<tr class="text-c">
					<td><input type="checkbox"></td>
					<td>{{$row['id']}}</td>
					<td>{{$row['name']}}</td>
					<td>
						@if (!empty($row['logo'])) <img src="{{URL::asset($row['logo'])}}" class="avatar J_car_brand"> @endif
					</td>
					<td>{{$row['name_en']}}</td>
					<td>{{$row['first_letter']}}</td>
					<td>{{$row['status_text']}}</td>
				</tr>
			@endforeach
		</tbody>
	</table>

@endsection