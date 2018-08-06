@extends('admin.public')
@section('body')
	<div class="cl pd-5 bg-1 bk-gray mt-10">
		{{trans('common.order_id')}}：
		<input style="width: 150px;" class="input-text" name="filter_order_id" value="{{$filter['filter_order_id']}}">
		{{trans('common.create_at')}}：
		<input type="text" name="filter_date_from" class="input-text Wdate" style="width:120px;" value="{{$filter['filter_date_from']}}"
		       id="datemin" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'datemax\')||\'%y-%M-%d\'}',skin:'whyGreen'})"> -
		<input type="text" name="filter_date_to" class="input-text Wdate" style="width:120px;" value="{{$filter['filter_date_to']}}"
		       id="datemax" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'datemin\')}',maxDate:'%y-%M-%d',skin:'whyGreen'})">
		<input type="text" class="input-text" style="width:250px" placeholder="输入会员名称、手机" name="filter_account"
		       value="{{$filter['filter_account']}}">
		<span class="btn btn-success radius" id="J_search">
			<i class="Hui-iconfont">&#xe665;</i> {{trans('common.filter')}}
		</span>
		<span class="r">共有数据：<strong>{{$total}}</strong> 条</span>
	</div>
	<table class="table table-border table-bordered table-striped table-hover table-bg table-sort mt-10">
		<thead>
		<tr class="text-c">
			<th>{{trans('common.order_id')}}</th>
			<th>{{trans('common.wash_product')}}</th>
			<th>{{trans('common.car_info')}}</th>
			<th>{{trans('common.wash_time')}}</th>
			<th>{{trans('common.address')}}</th>
			<th>{{trans('common.create_at')}}</th>
			<th>{{trans('common.status')}}</th>
			<th>{{trans('common.action')}}</th>
		</tr>
		</thead>
		<tbody>
		@foreach($list as $row)
			<tr class="text-c">
				<td>{{$row['order_id']}}</td>
				<td>{{$row['wash_product']}}</td>
				<td>{{$row['plate_number']}} | {{$row['brand']}} {{$row['model']}} | {{$row['color']}}</td>
				<td>{{$row['wash_time']}}</td>
				<td style="width:15%;" title="{{$row['address']}}">{{$row['address']}}</td>
				<td>{{$row['create_at']}}</td>
				<td>{{$row['status_text']}}</td>
				<td class="td-manage" style="width: 50px;">
					<a title="{{trans('common.edit')}}" href="javascript:;" class="ml-5"
					   onclick="layer_show($(this).attr('title'),'{{route('washOrderForm', $row['order_id'])}}','','570')">
						<i class="Hui-iconfont">&#xe6df;</i>
					</a>
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
	{{$pagination->links()}}
@endsection
@section('js')
	<script>
		$('#J_search').click(function () {
			var filter_order_id  = $('input[name="filter_order_id"]').val().trim(),
			    filter_account   = $('input[name="filter_account"]').val().trim(),
			    filter_date_from = $('input[name="filter_date_from"]').val().trim(),
			    filter_date_to   = $('input[name="filter_date_to"]').val().trim();
			
			if (filter_order_id || filter_account || filter_date_from || filter_date_to) {
				
				var query_string = [];
				if (filter_order_id) query_string.push('filter_order_id='+filter_order_id);
				if (filter_account) query_string.push('filter_account='+filter_account);
				if (filter_date_from) query_string.push('filter_date_from='+filter_date_from);
				if (filter_date_to) query_string.push('filter_date_to='+filter_date_to);
				
				location.href = '{{route('washOrderList')}}?'+query_string.join('&');
			} else {
				location.href = '{{route('washOrderList')}}';
			}
		});
	</script>
@endsection