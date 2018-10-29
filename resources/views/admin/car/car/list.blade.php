@extends('admin.public_list')
@section('css')
	<style>
		.car_plate_out {
			display: inline-block;
			background: #003ECC;
			padding: 1px;
			border-radius: 4px;
		}
		.car_plate {
			display: inline-block;
			padding: 5px;
			border: 1px solid #E3E6EF;
			color: #E3E6EF;
			font-size: 18px;
			border-radius: 3px;
		}
	</style>
@endsection
@section('body')
	<div class="cl pd-5 bg-1 bk-gray mt-10">
		用户ID：<input type="number" class="input-text" name="filter_user_id" value="{{$filter['filter_user_id']}}">
		<span class="btn btn-success radius" id="J_search">
			<i class="Hui-iconfont">&#xe665;</i> {{trans('common.filter')}}
		</span>
		<span class="r">{!!sprintf(trans('common.total_count'), $total)!!}</span>
	</div>
	<table class="table table-border table-bordered table-striped table-hover table-bg table-sort mt-10">
		<thead>
		<tr class="text-c">
			<th width="40">ID</th>
			<th width="80">{{trans('common.username')}}</th>
			<th width="100">{{trans('common.brand')}}</th>
			<th width="200">{{trans('common.car_model')}}</th>
			<th width="150">{{trans('common.plate_number')}}</th>
			<th width="100">{{trans('common.color')}}</th>
		</tr>
		</thead>
		<tbody>
		@foreach($list as $row)
			<tr class="text-c">
				<td>{{$row['id']}}</td>
				<td><a href="{{route('memberList')}}?filter_user_id={{$row['user_id']}}">{{$row['username']}}</a></td>
				<td><a href="{{route('brandList')}}?filter_id={{$row['brand_id']}}">{{$row['brand']}}</a></td>
				<td>{{$row['model']}}</td>
				<td><span class="car_plate_out"><span class="car_plate">{{$row['plate']}}</span></span></td>
				<td>{{$row['color']}}</td>
			</tr>
		@endforeach
		</tbody>
	</table>
	{{$pagination->links()}}
@endsection
@section('js')
	<script>
		$('#J_search').click(function () {
			var filter_user_id   = $('input[name="filter_user_id"]').val().trim(),
			    filter_account   = $('input[name="filter_account"]').val(),
			    filter_date_from = $('input[name="filter_date_from"]').val(),
			    filter_date_to   = $('input[name="filter_date_to"]').val();
			
			if (filter_user_id || filter_account || filter_date_from || filter_date_to) {
				
				var query_string = [];
				if (filter_user_id) query_string.push('filter_user_id='+filter_user_id);
				if (filter_account) query_string.push('filter_account='+filter_account);
				if (filter_date_from) query_string.push('filter_date_from='+filter_date_from);
				if (filter_date_to) query_string.push('filter_date_to='+filter_date_to);
				
				location.href = '{{route('carList')}}?'+query_string.join('&');
			} else {
				location.href = '{{route('carList')}}';
			}
		});
	</script>
@endsection