@extends('admin.public')
@section('body')
	<section class="Hui-article-box">
		@include('admin.breadcrumb')
		<div class="Hui-article">
			<article class="cl pd-20">
				<div class="cl pd-5 bg-1 bk-gray">
					<span class="l">
						<a href="javascript:;" onclick="batch_delete('{{route('batchDeleteArticle')}}')" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a>
						<a href="javascript:;" onclick="layer_show('添加产品','{{route('articleForm')}}?content_type={{$filter_content_type}}','1000','600')" class="btn btn-primary radius">
							<i class="Hui-iconfont">&#xe600;</i> 添加产品
						</a>
					</span>
					</span>
					<span class="r">共有数据：<strong>{{$total}}</strong> 条</span>
				</div>
				<table class="table table-border table-bordered table-striped table-hover table-bg">
					<thead>
					<tr class="text-c">
						<th width="25"><input type="checkbox" name="" value=""></th>
						<th width="40">ID</th>
						<th width="150">{{trans('common.type')}}</th>
						<th width="200">{{trans('common.article_name')}}</th>
						<th width="150">{{trans('common.start_time')}}</th>
						<th width="150">{{trans('common.end_time')}}</th>
						{{--<th width="100">{{trans('common.create_by')}}</th>--}}
						{{--<th width="150">{{trans('common.create_at')}}</th>--}}
						{{--<th width="150">{{trans('common.update_at')}}</th>--}}
						<th width="100">{{trans('common.sort')}}</th>
						<th width="100">{{trans('common.status')}}</th>
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
							<td>{{$row['start_time']}}</td>
							<td>{{$row['end_time']}}</td>
							{{--<td>{{$row['create_by']}}</td>--}}
							{{--<td>{{$row['create_at']}}</td>--}}
							{{--<td>{{$row['update_at']}}</td>--}}
							<td>{{$row['sort']}}</td>
							<td class="td-status">
								<span class="label label-{{$row['status'] ? 'success' : 'danger'}} radius">{{$row['status_text']}}</span>
							</td>
							<td class="td-manage">
								
								@if ($row['status'] == '1')
									<a title="{{trans('common.disable')}}" style="text-decoration:none"
									   onClick="handleDataStop(this,'{{$row['id']}}', '{{route('articleChangeStatus')}}')" href="javascript:;">
										<i class="Hui-iconfont">&#xe631;</i>
									</a>
								@else
									<a title="{{trans('common.enable')}}" style="text-decoration:none"
									   onClick="handleDataStart(this,'{{$row['id']}}', '{{route('articleChangeStatus')}}')" href="javascript:;">
										<i class="Hui-iconfont">&#xe615;</i>
									</a>
								@endif
								
								<a title="{{trans('common.edit')}}" href="javascript:;" class="ml-5" style="text-decoration:none"
								   onclick="layer_show($(this).attr('title'), '{{route('articleForm', $row['id'])}}?content_type={{$filter_content_type}}','1000','600')">
									<i class="Hui-iconfont">&#xe6df;</i>
								</a>
								<a title="{{trans('common.delete')}}" onclick="handleDataDel(this,'{{$row['id']}}', '{{route('articleChangeStatus')}}')"
								   class="ml-5" style="text-decoration:none" href="javascript:;">
									<i class="Hui-iconfont">&#xe6e2;</i>
								</a>
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
				{{$pagination->render()}}
			</article>
		</div>
	</section>
@endsection
@section('js')
	<script>
		
		// 筛选
		$('select[name="filter_content_type"]').change(function () {
			var content_type = $('select[name="filter_content_type"]').val();
			if (content_type) {
				location.href = '{{route('productCategory')}}/?filter_content_type=' + content_type;
			} else {
				location.href = '{{route('productCategory')}}';
			}
		});
	</script>
@endsection
