@extends('admin.public_list')
@section('body')
	<div class="text-c mb-10">
		<span>{{trans('common.article_name')}}：
			<input class="input-text filter_input" name="filter_article_name" value="{{$filter_article_name}}" />
		</span>
		<span>{{trans('common.content_type')}}：
						<select class="select-box filter_input" name="filter_content_type">
							<option></option>
							@foreach($typeList as $contentType)
								<option value="{{$contentType['id']}}" @if ($filter_content_type == $contentType['id']) selected @endif>
									{{$contentType['name']}}</option>
							@endforeach
						</select>
					</span>
		<button type="submit" class="btn btn-success radius J_filter" name="">
			<i class="Hui-iconfont">&#xe665;</i> {{trans('common.filter')}}
		</button>
	</div>
	<div class="cl pd-5 bg-1 bk-gray">
		<span class="l">
			<a href="javascript:;" onclick="batch_delete('{{route('batchDeleteArticle')}}')" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> {{trans('common.batch_delete')}}</a>
			<a href="javascript:;" onclick="layer_show('添加文章','{{route('articleForm')}}','1000','600')" class="btn btn-primary radius">
				<i class="Hui-iconfont">&#xe600;</i> 添加文章
			</a>
		</span>
		<span class="r">{!!sprintf(trans('common.total_count'), $total)!!}</span>
	</div>
	<table class="table table-border table-bordered table-striped table-hover table-bg mt-10">
		<thead>
		<tr class="text-c">
			<th width="25"><input type="checkbox" name="" value=""></th>
			<th width="40">ID</th>
			<th width="150">{{trans('common.content_type')}}</th>
			<th width="200">{{trans('common.article_name')}}</th>
			{{--<th width="150">{{trans('common.start_time')}}</th>--}}
			{{--<th width="150">{{trans('common.end_time')}}</th>--}}
			<th width="100">{{trans('common.create_by')}}</th>
			<th width="150">{{trans('common.create_at')}}</th>
			<th width="150">{{trans('common.update_at')}}</th>
			<th width="100">{{trans('common.sort')}}</th>
			<th width="100">{{trans('common.action')}}</th>
			<th width="100">{{trans('common.action')}}</th>
		</tr>
		</thead>
		<tbody>
		@foreach($list as $row)
			<tr class="text-c">
				<td><input type="checkbox" value="{{$row['id']}}" name=""></td>
				<td>{{$row['id']}}</td>
				<td>{{$row['content_type']}}</td>
				<td>{{$row['name']}}</td>
				{{--<td>{{$row['start_time']}}</td>--}}
				{{--<td>{{$row['end_time']}}</td>--}}
				<td>{{$row['create_by']}}</td>
				<td>{{$row['create_at']}}</td>
				<td>{{$row['update_at']}}</td>
				<td>{{$row['sort']}}</td>
				<td class="td-status">
					<span class="label label-{{$row['status'] ? 'success' : 'danger'}} radius">{{$row['status_text']}}</span>
				</td>
				<td class="td-manage">
					
					@if ($row['status'] == '1')
						<a title="{{trans('common.disable')}}" href="javascript:;"
						   onClick="handleDataStop(this,'{{$row['id']}}', '{{route('articleChangeStatus')}}')">
							<i class="Hui-iconfont">&#xe631;</i>
						</a>
					@else
						<a title="{{trans('common.enable')}}" href="javascript:;"
						   onClick="handleDataStart(this,'{{$row['id']}}', '{{route('articleChangeStatus')}}')">
							<i class="Hui-iconfont">&#xe615;</i>
						</a>
					@endif
					
					<a title="{{trans('common.edit')}}" href="javascript:;" class="ml-5"
					   onclick="layer_show($(this).attr('title'), '{{route('articleForm', $row['id'])}}','1000','600')">
						<i class="Hui-iconfont">&#xe6df;</i>
					</a>
					<a title="{{trans('common.delete')}}" class="ml-5" href="javascript:;"
					   onclick="handleDataDel(this,'{{$row['id']}}', '{{route('articleChangeStatus')}}')">
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
		
		// 筛选
		$('.J_filter').click(function () {
			var content_type = $('select[name="filter_content_type"]').val(),
			    article_name = $('input[name="filter_article_name"]').val();
			if (content_type || article_name) {
				
				var query_string = [];
				if (content_type) query_string.push('filter_content_type='+content_type);
				if (article_name) query_string.push('filter_article_name='+article_name);
				location.href = '{{route('articleList')}}?'+query_string.join('&');
			} else {
				location.href = '{{route('articleList')}}';
			}
		});
	</script>
@endsection
