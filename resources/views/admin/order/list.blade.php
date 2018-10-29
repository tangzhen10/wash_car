@extends('admin.public_list')
@section('css')
	<style>
		.status_1 {
			color: red;
		}
		.status_2 {
			color: #333;
		}
		.status_3 {
			color: orange;
		}
		.status_4 {
			color: green;
		}
		.status_5 {
			color: dodgerblue;
			font-weight: bold;
		}
		.status_6, .status_7 {
			color: grey;
		}
		.status_8 {
			color: plum;
		}
	</style>
@endsection
@section('body')
	<div class="cl pd-5 bg-1 bk-gray mt-10">
		{{trans('common.order_id')}}：
		<input class="input-text width-120" name="filter_order_id" value="{{$filter['filter_order_id']}}">
		{{trans('common.wash_product')}}：
		<select name="filter_wash_product_id" class="select-box width-120">
			<option></option>
			@foreach($wash_product_list as $item)
				<option value="{{$item['id']}}" @if ($filter['filter_wash_product_id'] == $item['id']) selected @endif >{{$item['name']}}</option>
			@endforeach
		</select>
		{{trans('common.order_status')}}：
		<select name="filter_status" class="select-box width-120">
			<option></option>
			@foreach($status_list as $status => $name)
				<option value="{{$status}}" @if ($filter['filter_status'] == $status) selected @endif >{{$name}}</option>
			@endforeach
		</select>
		<p style="height: 5px;margin: 0;"></p>
		{{trans('common.create_at')}}：
		<input type="text" name="filter_date_from" class="input-text Wdate width-120" value="{{$filter['filter_date_from']}}"
		       id="datemin" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'datemax\')||\'%y-%M-%d\'}',skin:'whyGreen'})"> -
		<input type="text" name="filter_date_to" class="input-text Wdate width-120" value="{{$filter['filter_date_to']}}"
		       id="datemax" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'datemin\')}',maxDate:'%y-%M-%d',skin:'whyGreen'})">
		{{trans('common.user')}}：
		<input type="text" class="input-text mr-10" style="width:200px" placeholder="输入用户名称、手机" name="filter_account"
		       value="{{$filter['filter_account']}}">
		<p class="check-box skin-minimal">
			<label>
				<input name="filter_serve_by_me" type="checkbox" value="1"
				       @if ($filter['filter_serve_by_me']) checked @endif>待我服务
			</label>
			<i class="Hui-iconfont c-warning J_serve_by_me_tip" data-title="由我接单且状态为已接单和服务中的订单">&#xe633;</i>
		</p>
		<span class="btn btn-success radius" id="J_search">
			<i class="Hui-iconfont">&#xe665;</i> {{trans('common.filter')}}
		</span>
		<span class="r">{!!sprintf(trans('common.total_count'), $total)!!}</span>
	</div>
	<table class="table table-border table-bordered table-striped table-hover table-bg table-sort mt-10">
		<thead>
		<tr class="text-c">
			<th>{{trans('common.order_id')}}</th>
			<th>{{trans('common.wash_product')}}</th>
			<th>{{trans('common.car_info')}}</th>
			<th>{{trans('common.wash_time')}}</th>
			<th>{{trans('common.create_at')}}</th>
			<th>{{trans('common.order_amount')}}</th>
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
				<td>{{$row['create_at']}}</td>
				<td>{{$row['total']}}</td>
				<td class="status_{{$row['status']}}">{{$row['status_text']}}</td>
				<td class="td-manage" style="width: 50px;">
					<a title="{{trans('common.edit')}}" href="javascript:;" class="ml-5"
					   onclick="layer_show_check_mobile($(this).attr('title'),'{{route('washOrderForm', $row['order_id'])}}','','630')">
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
		
		function layer_show_check_mobile(title, url, width, height) {
			
			if (is_mobile()) {
				window.open(url);
			} else {
				layer_show(title, url, width, height);
			}
		}
		
		$('#J_search').click(function () {
			var filter_order_id        = $('input[name="filter_order_id"]').val().trim(),
			    filter_wash_product_id = $('select[name="filter_wash_product_id"]').val(),
			    filter_status          = $('select[name="filter_status"]').val(),
			    filter_account         = $('input[name="filter_account"]').val().trim(),
			    filter_date_from       = $('input[name="filter_date_from"]').val().trim(),
			    filter_date_to         = $('input[name="filter_date_to"]').val().trim(),
			    filter_serve_by_me     = $('input[name="filter_serve_by_me"]:checked').val();
			
			var query_string = [], url = '{{route('washOrderList')}}';
			if (filter_order_id) query_string.push('filter_order_id='+filter_order_id);
			if (filter_wash_product_id) query_string.push('filter_wash_product_id='+filter_wash_product_id);
			if (filter_status) query_string.push('filter_status='+filter_status);
			if (filter_account) query_string.push('filter_account='+filter_account);
			if (filter_date_from) query_string.push('filter_date_from='+filter_date_from);
			if (filter_date_to) query_string.push('filter_date_to='+filter_date_to);
			if (filter_serve_by_me) query_string.push('filter_serve_by_me='+filter_serve_by_me);
			
			if (query_string.length > 0) url += '?'+query_string.join('&');
			location.href = url;
		});
		
		$('.J_serve_by_me_tip').mouseover(function () {
			if (!is_mobile()) {
				layer.tips($(this).attr('data-title'), $(this).prev(), {tips : [3, '#5A98DE']});
			}
		}).mouseout(function () {
			layer.close(layer.tips());
		}).click(function () {
			if (is_mobile()) layer.msg($(this).attr('data-title'), {time : 2000});
		});
	</script>
@endsection