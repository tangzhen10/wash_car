@extends('admin.public')
@section('body')
	<section class="Hui-article-box">
		@include('admin.breadcrumb')
		<div class="Hui-article">
			<article class="cl pd-20">
				<div class="text-c"> 日期范围：
					<input type="text" name="date_from" class="input-text Wdate" style="width:120px;" value="{{$filter['date_from']}}"
						   id="datemin" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'datemax\')||\'%y-%M-%d\'}',skin:'whyGreen'})">
									 -
					<input type="text" name="date_to" class="input-text Wdate" style="width:120px;" value="{{$filter['date_to']}}"
						   id="datemax" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'datemin\')}',maxDate:'%y-%M-%d',skin:'whyGreen'})">
					<input type="text" class="input-text" style="width:250px" placeholder="输入会员名称、电话、邮箱" id="" name="account" value="{{$filter['account']}}">
					<button type="submit" class="btn btn-success radius" id="J_search" name="">
						<i class="Hui-iconfont">&#xe665;</i> 搜用户
					</button>
				</div>
				<div class="cl pd-5 bg-1 bk-gray mt-20">
					<span class="l"><a href="javascript:;" onclick="datadel()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a> <a href="javascript:;" onclick="layer_show('添加用户','member-add.html','','510')" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 添加用户</a></span>
					<span class="r">共有数据：<strong>{{$total}}</strong> 条</span></div>
				<div class="mt-20">
					<table class="table table-border table-bordered table-hover table-bg table-sort">
						<thead>
						<tr class="text-c">
							<th width="25"><input type="checkbox" name="" value=""></th>
							<th width="35">ID</th>
							<th width="150">用户名</th>
							<th width="40">性别</th>
							<th width="90">手机</th>
							<th width="150">邮箱</th>
							<th width="150">注册时间</th>
							<th width="100">操作</th>
						</tr>
						</thead>
						<tbody>
						@foreach($members as $member)
							<tr class="text-c">
								<td><input type="checkbox" value="{{$member['user_id']}}" name=""></td>
								<td>{{$member['user_id']}}</td>
								<td>{{$member['nickname']}}</td>
								<td>{{$member['gender_text']}}</td>
								<td>{{$member['phone']}}</td>
								<td>{{$member['email']}}</td>
								<td>{{$member['create_at']}}</td>
								<td class="td-manage">
									<a title="编辑" href="javascript:;" onclick="layer_show($(this).attr('title'),'{{route('memberForm', $member['user_id'])}}','','550')" class="ml-5" style="text-decoration:none">
										<i class="Hui-iconfont">&#xe6df;</i>
									</a>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
					{{$pagination->links()}}
				</div>
			</article>
		</div>
	</section>
@endsection
@section('js')
	<script>
		
		$('#J_search').click(function () {
			var account   = $('input[name="account"]').val().trim(),
				date_from = $('input[name="date_from"]').val().trim(),
				date_to   = $('input[name="date_to"]').val().trim();
			if (account || date_from || date_to) {
				
				var query_string = [];
				if (account) query_string.push('filter_account='+account);
				if (date_from) query_string.push('filter_date_from='+date_from);
				if (date_to) query_string.push('filter_date_to='+date_to);
				
				location.href = '{{route('memberList')}}?'+query_string.join('&');
			} else {
				location.reload();
			}
		});
	</script>
@endsection