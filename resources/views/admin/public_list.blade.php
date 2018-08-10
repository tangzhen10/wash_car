@extends('admin.base')
@section('header_and_menu')
	@include('admin.header')
	@include('admin.menu')
	<section class="Hui-article-box" style="position: absolute;">
		<nav class="breadcrumb">
			<i class="Hui-iconfont">&#xe67f;</i>
			@foreach($breadcrumbs as $breadcrumb)
				@if ($breadcrumb['url'])
					<a href="{{$breadcrumb['url']}}">{{$breadcrumb['text']}}</a>
				@else
					<span class="c-gray en">{{$breadcrumb['text']}}</span>
				@endif
			@endforeach
			<a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="{{trans('common.refresh')}}">
				<i class="Hui-iconfont">&#xe68f;</i>
			</a>
			<a class="btn btn-primary radius r mr-5" style="line-height:1.6em;margin-top:3px" href="javascript:history.back();" title="{{trans('common.back')}}">
				<i class="Hui-iconfont">&#xe66b;</i>
			</a>
		</nav>
		<div class="Hui-article">
			<article class="cl pd-20">
				@yield('body')
			</article>
		</div>
	</section>
@endsection
@section('common_js')
	<script>
		$(function () {
			
			// 菜单栏
			var real_url = location.href,
			    active   = false;
			if (!active) { // 优先定位在与权限菜单一致的url上
				for (var x in $('.menu_item')) {
					var href = $('.menu_item').eq(x).attr('href');
					if (href && href == real_url) {
						$('.menu_item').eq(x).parent('li').addClass('current');
						$('.menu_item').eq(x).parents('dd').show().siblings('dt').addClass('selected');
						active = true;
						break;
					}
				}
			}
			if (!active) { // 再匹配过滤参数的相似格式
				for (var x in $('.menu_item')) {
					var href = $('.menu_item').eq(x).attr('href');
					if (href && href == real_url.substring(0, href.length)) {
						$('.menu_item').eq(x).parent('li').addClass('current');
						$('.menu_item').eq(x).parents('dd').show().siblings('dt').addClass('selected');
						active = true;
						break;
					}
				}
			}
		});
		
		// 列表页启用数据
		function handleDataStart(obj, id, url) {
//		layer.confirm('确认要启用吗？', function () {
			$.ajax({
				url        : url,
				data       : {
					id     : id,
					status : '1'
				},
				type       : 'post',
				dataType   : 'json',
				beforeSend : function () {layer.load(2)},
				success    : function (data) {
					layer.close(layer.load());
					if (data.code == 0 && data.msg == 'ok') {
						$(obj).parents("tr").find(".td-manage").prepend('<a onClick="handleDataStop(this,'+id+',\''+url+'\')" href="javascript:;" title="{{trans('common.disable')}}" style="text-decoration:none"><i class="Hui-iconfont">&#xe631;</i></a>');
						$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">{{trans('common.enable')}}</span>');
						$(obj).remove();
						layer.msg('{{trans('common.enable')}}！', {icon : 6, time : 1000});
					} else {
						layer.msg(data.error, function () {});
					}
				}
			});
//		});
		}
		
		// 列表页停用数据
		function handleDataStop(obj, id, url) {
//		layer.confirm('确认要停用吗？', function () {
			$.ajax({
				url        : url,
				data       : {
					id     : id,
					status : '0'
				},
				type       : 'post',
				dataType   : 'json',
				beforeSend : function () {layer.load(2)},
				success    : function (data) {
					layer.close(layer.load());
					if (data.code == 0 && data.msg == 'ok') {
						var enable_btn = '<a onClick="handleDataStart(this,'+id+',\''+url+'\')" '+
							'href="javascript:;" title="{{trans('common.enable')}}" style="text-decoration:none">'+
							'<i class="Hui-iconfont">&#xe615;</i></a>';
						$(obj).parents("tr").find(".td-manage").prepend(enable_btn);
						$(obj).parents("tr").find(".td-status").html('<span class="label label-danger radius">{{trans('common.disable')}}</span>');
						$(obj).remove();
						layer.msg('{{trans('common.disable')}}！', {icon : 5, time : 1000});
					} else {
						layer.msg(data.error, function () {});
					}
				}
			});
//		});
		}
		
		// 列表页删除数据
		function handleDataDel(obj, id, url) {
			layer.confirm('确认要删除吗？', function () {
				$.ajax({
					url        : url,
					data       : {
						id     : id,
						status : '-1'
					},
					type       : 'post',
					dataType   : 'json',
					beforeSend : function () {layer.load(2)},
					success    : function (data) {
						layer.close(layer.load());
						if (data.code == 0 && data.msg == 'ok') {
							$(obj).parents("tr").remove();
							layer.msg('已删除!', {icon : 1, time : 1000});
						} else {
							layer.msg(data.error, function () {});
						}
					}
				});
			});
		}
		
		// 列表页批量删除
		function batch_delete(url) {
			
			var select_items = $('tbody input[type=checkbox]:checked'),
			    ids          = [];
			for (var i = 0; i < select_items.length; i++) {
				ids.push($(select_items[i]).val());
			}
			if (ids.length == 0) {
				layer.msg('{{trans('validation.no_one_selected')}}', {time : 1000});
				return;
			}
			
			// 危险操作，再次确认
			layer.confirm('{{trans('common.dangerous_action_confirm')}}', function () {
				
				layer.prompt({
					title    : '{{trans('common.input_manager_password')}}',
					formType : 1, // 密码
				}, function (val, index) {
					
					// 验证密码
					$.ajax({
						url        : '{{route('checkManagerPwd')}}',
						type       : 'post',
						data       : {password : val},
						beforeSend : function () { layer.load(3) },
						success    : function (data) {
							layer.close(layer.load());
							if (data.code == 0 && data.msg == 'ok') {
								
								$.ajax({
									url        : url,
									type       : 'post',
									data       : {ids : ids},
									beforeSend : function () { layer.load(3) },
									success    : function (data) {
										layer.close(layer.load());
										if (data.code == 0) {
											layer.msg('{{trans('common.action_success')}}');
											location.reload();
										} else {
											layer.msg(data.error, function () {});
										}
									}
								});
							} else {
								layer.msg(data.error, function () {});
							}
						}
					});
				});
			});
		}
	</script>
@endsection