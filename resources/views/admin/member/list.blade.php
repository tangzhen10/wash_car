@extends('admin.public_list')
@section('body')
	<div class="cl pd-5 bg-1 bk-gray mt-10">
		ID：<input type="number" class="input-text" name="filter_user_id" value="{{$filter['filter_user_id']}}">
		{{trans('common.reg_at')}}：
		<input type="text" name="filter_date_from" class="input-text Wdate" style="width:120px;" value="{{$filter['filter_date_from']}}"
		       id="datemin" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'datemax\')||\'%y-%M-%d\'}',skin:'whyGreen'})"> -
		<input type="text" name="filter_date_to" class="input-text Wdate" style="width:120px;" value="{{$filter['filter_date_to']}}"
		       id="datemax" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'datemin\')}',maxDate:'%y-%M-%d',skin:'whyGreen'})">
		<input type="text" class="input-text" style="width:250px" placeholder="输入会员名称、电话、邮箱" name="filter_account"
		       value="{{$filter['filter_account']}}">
		<span class="btn btn-success radius" id="J_search">
			<i class="Hui-iconfont">&#xe665;</i> {{trans('common.filter')}}
		</span>
		<span class="r">{!!sprintf(trans('common.total_count'), $total)!!}</span>
	</div>
	<table class="table table-border table-bordered table-striped table-hover table-bg table-sort mt-10">
		<thead>
		<tr class="text-c">
			<th width="35">ID</th>
			<th width="150">{{trans('common.username')}}</th>
			<th width="40">{{trans('common.gender')}}</th>
			<th width="90">{{trans('common.phone')}}</th>
			<th width="150">{{trans('common.email')}}</th>
			<th width="150">{{trans('common.reg_at')}}</th>
			<th width="100">{{trans('common.action')}}</th>
		</tr>
		</thead>
		<tbody>
		@foreach($list as $row)
			<tr class="text-c">
				<td>{{$row['user_id']}}</td>
				<td>{{$row['nickname']}}</td>
				<td>{{$row['gender_text']}}</td>
				<td>{{$row['phone']}}</td>
				<td>{{$row['email']}}</td>
				<td>{{$row['create_at']}}</td>
				<td class="td-manage">
					<a title="{{trans('common.edit')}}" href="javascript:;" class="ml-5"
					   onclick="layer_show($(this).attr('title'),'{{route('memberForm', $row['user_id'])}}','','550')">
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
			var filter_user_id   = $('input[name="filter_user_id"]').val().trim(),
			    filter_account   = $('input[name="filter_account"]').val().trim(),
			    filter_date_from = $('input[name="filter_date_from"]').val().trim(),
			    filter_date_to   = $('input[name="filter_date_to"]').val().trim();
			
			if (filter_user_id || filter_account || filter_date_from || filter_date_to) {
				
				var query_string = [];
				if (filter_user_id) query_string.push('filter_user_id='+filter_user_id);
				if (filter_account) query_string.push('filter_account='+filter_account);
				if (filter_date_from) query_string.push('filter_date_from='+filter_date_from);
				if (filter_date_to) query_string.push('filter_date_to='+filter_date_to);
				
				location.href = '{{route('memberList')}}?'+query_string.join('&');
			} else {
				location.href = '{{route('memberList')}}';
			}
		});
	</script>
@endsection