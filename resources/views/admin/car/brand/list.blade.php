@extends('admin.public')
@section('body')
	<div class="text-c mb-15">
		<span>ID：</span>
		<input type="number" name="filter_id" class="input-text" style="width:120px;" value="{{$filter['filter_id']}}">
		<span>{{trans('common.first_letter')}}：</span>
		<select class="select-box" name="filter_first_letter" style="width:120px;">
			<option></option>
			@foreach(explode(',', config('project.FIRST_LETTER')) as $letter)
				<option value="{{$letter}}" @if ($letter == $filter['filter_first_letter']) selected @endif>{{$letter}}</option>
			@endforeach
		</select>
		<span>{{trans('common.name')}}：</span>
		<input type="text" class="input-text" style="width:250px" placeholder="中英文名皆可" name="filter_name" value="{{$filter['filter_name']}}">
		<span>{{trans('common.per_page')}}：</span>
		<select class="select-box" name="perPage" style="width:120px;">
			<option></option>
			@foreach([15, 20, 50, 100, 500] as $perQty)
				<option value="{{$perQty}}" @if ($perQty == $filter['perPage']) selected @endif>{{$perQty}}</option>
			@endforeach
		</select>
		<button type="submit" class="btn btn-success radius" id="J_search" name="">
			<i class="Hui-iconfont">&#xe665;</i> {{trans('common.search')}}
		</button>
	</div>
	<div class="cl pd-5 bg-1 bk-gray">
		<span class="l">
			<a href="javascript:;" onclick="layer_show('添加品牌','{{route('brandForm')}}','1000','600')" class="btn btn-primary radius">
				<i class="Hui-iconfont">&#xe600;</i> 添加品牌
			</a>
		</span>
		<span class="r">共有数据：<strong>{{$total}}</strong> 条</span>
	</div>
	<table class="table table-border table-bordered table-striped table-hover table-bg table-sort">
		<thead>
		<tr class="text-c">
			{{--<th width="25"><input type="checkbox"></th>--}}
			<th width="40">ID</th>
			<th width="80">{{trans('common.first_letter')}}</th>
			<th width="100">{{trans('common.logo')}}</th>
			<th width="200">{{trans('common.name')}}</th>
			<th width="150">{{trans('common.name_en')}}</th>
			<th width="100">{{trans('common.hot_value')}}</th>
			<th width="100">{{trans('common.status')}}</th>
			<th width="100">{{trans('common.action')}}</th>
		</tr>
		</thead>
		<tbody>
		@foreach($list as $row)
			<tr class="text-c">
				{{--<td><input type="checkbox"></td>--}}
				<td>{{$row['id']}}</td>
				<td>{{$row['first_letter']}}</td>
				<td>
					@if (!empty($row['logo']))
						<img src="{{URL::asset($row['logo'])}}" class="avatar J_car_brand" alt="{{$row['name']}}">
					@endif
				</td>
				<td><a href="{{route('modelList', ['brand_id' => $row['id']])}}">{{$row['name']}}</a></td>
				<td>{{$row['name_en']}}</td>
				<td>
					@if ($row['hot'] > 0)
						<i class="Hui-iconfont c-red f-20">&#xe6c1;</i> × {{$row['hot']}}
					@endif
				</td>
				<td class="td-status">
					<span class="label label-{{$row['status'] ? 'success' : 'danger'}} radius">{{$row['status_text']}}</span>
				</td>
				<td class="td-manage">
					
					@if ($row['status'] == '1')
						<a title="{{trans('common.disable')}}" style="text-decoration:none"
						   onClick="handleDataStop(this,'{{$row['id']}}','{{route('brandChangeStatus')}}')" href="javascript:;">
							<i class="Hui-iconfont">&#xe631;</i>
						</a>
					@else
						<a title="{{trans('common.enable')}}" style="text-decoration:none"
						   onClick="handleDataStart(this,'{{$row['id']}}','{{route('brandChangeStatus')}}')" href="javascript:;">
							<i class="Hui-iconfont">&#xe615;</i>
						</a>
					@endif
					
					<a title="{{trans('common.edit')}}" onclick="layer_show($(this).attr('title'),'{{route('brandForm', $row['id'])}}','1000','600')"
					   href="javascript:;" class="ml-5" style="text-decoration:none">
						<i class="Hui-iconfont">&#xe6df;</i>
					</a>
					<a title="{{trans('common.delete')}}" onclick="handleDataDel(this,'{{$row['id']}}','{{route('brandChangeStatus')}}')"
					   class="ml-5" style="text-decoration:none" href="javascript:;">
						<i class="Hui-iconfont">&#xe6e2;</i>
					</a>
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
	{{$pagination->render()}}
@endsection
@section('js')
	<script>
		// 点击查看logo大图
		$('.J_car_brand').css({cursor : 'pointer'}).click(function () {
			layer.open({
				type       : 1,
				title      : $(this).attr('alt'),
				shade      : 0.5,
				closeBtn   : 0,
				shadeClose : true,
				content    : '<img class="pd-20" src="'+$(this).attr('src')+'">'
			});
		});
		
		$('#J_search').click(function () {
			var filter_id           = $('input[name="filter_id"]').val().trim(),
				filter_first_letter = $('select[name="filter_first_letter"]').val(),
				perPage             = $('select[name="perPage"]').val(),
				filter_name         = $('input[name="filter_name"]').val().trim();
			if (filter_id || filter_first_letter || perPage || filter_name) {
				
				var query_string = [];
				if (filter_id) query_string.push('filter_id='+filter_id);
				if (filter_first_letter) query_string.push('filter_first_letter='+filter_first_letter);
				if (perPage) query_string.push('perPage='+perPage);
				if (filter_name) query_string.push('filter_name='+filter_name);
				
				location.href = '{{route('brandList')}}?'+query_string.join('&');
			} else {
				location.href = '{{route('brandList')}}';
			}
		});
	</script>
@endsection