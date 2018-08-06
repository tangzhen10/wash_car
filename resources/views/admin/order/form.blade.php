@extends('admin.base')
@section('css')
	<link rel="stylesheet" type="text/css" href="{{URL::asset('css/form.css')}}" />
	<style>
		/*选项卡*/
		.tabBar {
			border-bottom: 1px solid #5A98DD;
		}
		.tabBar span {
			font-size: 14px;
			padding: 5px 25px;
		}
		.tabBar span:hover {
			color: #fff;
			background: #67b0ff;
		}
		.tabBar span.current {
			color: #fff;
			background: #5A98DD;
		}
		/*日志*/
		.log_item {
			
			text-indent: -18px;
			border-left: 1px solid #5A98DD;
			padding: 15px 10px;
			margin: 0px 25px;
		}
		.log_item span {
			margin-right: 10px;
		}
		.log_item span.create_at {
			color: #333;
		}
		.log_item span.action {
			font-weight: bold;
			color: #5A98DD;
		}
		/*清洗照片*/
		.images_html {
			padding: 20px;
		}
	</style>
@endsection
@section('body')
	<div id="tab_area" class="HuiTab">
		<div class="tabBar clearfix">
			<span>订单信息</span>
			<span>订单进度</span>
			<span>服务前照片</span>
			<span>服务后照片</span>
		</div>
		<div class="tabCon">
			<article class="cl pd-20">
				<form enctype="multipart/form-data" class="form form-horizontal" id="form">
					<p>
						<span class="form_filed_row">{{trans('common.order_id')}}：</span>
						<span>{{$detail['order_id']}}</span>
					</p>
					<p>
						<span class="form_filed_row">{{trans('common.order_status')}}：</span>
						<span class="mr-10">{{$detail['status_text']}}</span>
						@switch ($detail['status'])
						@case(1)
						<span class="note">{{$detail['order_status_msg']}}</span>
						@break
						@case(2)
						<span class="btn btn-secondary-outline radius J_action" data-status="3">{{trans('common.take_order')}}</span>
						@break
						@case(3)
						<span class="btn btn-secondary-outline radius J_action" data-status="4">{{trans('common.serve_start')}}</span>
						@break
						@case(4)
						<span class="btn btn-secondary-outline radius J_action" data-status="5">{{trans('common.serve_finish')}}</span>
						@break
						@endswitch
					</p>
					<p>
						<span class="form_filed_row">{{trans('common.wash_product')}}：</span>
						<span>
						<a href="{{route('productList')}}?filter_id={{$detail['wash_product_id']}}" target="_blank">
							{{$detail['wash_product']}}
						</a>
					</span>
					</p>
					<p>
						<span class="form_filed_row">{{trans('common.user')}}：</span>
						<span>
							<a href="{{route('memberList')}}?filter_user_id={{$detail['user_id']}}" target="_blank">
								【{{$detail['username']}}】{{$detail['phone']}}
							</a>
						</span>
					</p>
					<p>
						<span class="form_filed_row">{{trans('common.car_info')}}：</span>
						<span>
							{{$detail['plate_number']}} | {{$detail['brand']}} {{$detail['model']}} | {{$detail['color']}}
						</span>
					</p>
					@if ($detail['status'] < 3)
						{!! $html !!}
						<p>
							<span class="form_filed_row">{{trans('common.wash_time')}}：</span>
							<select class="select-box radius" style="width: 75%;position: relative;top: 0px;" name="wash_time">
								@foreach($wash_time_list as $item)
									<option value="{{$item}}" @if ($item == $detail['wash_time']) selected @endif>{{$item}}</option>
								@endforeach
							</select>
						</p>
					@else
						<p>
							<span class="form_filed_row">{{trans('common.address')}}：</span>
							<span>{{$detail['address']}}</span>
						</p>
						<p>
							<span class="form_filed_row">{{trans('common.contact_user')}}：</span>
							<span>{{$detail['contact_user']}}</span>
						</p>
						<p>
							<span class="form_filed_row">{{trans('common.contact_phone')}}：</span>
							<span>{{$detail['contact_phone']}}</span>
						</p>
						<p>
							<span class="form_filed_row">{{trans('common.wash_time')}}：</span>
							<span>{{$detail['wash_time']}}</span>
						</p>
					@endif
					<p>
						<span class="form_filed_row">{{trans('common.payment_status')}}：</span>
						<span>{{trans('common.payment_status_'.$detail['payment_status'])}}</span>
						@if ($detail['payment_status'] == '0')
							<span class="btn btn-warning-outline radius ml-20 J_confirm_pay">手动确认支付</span>
						@endif
					</p>
					<p>
						<span class="form_filed_row">{{trans('common.create_at')}}：</span>
						<span>{{$detail['create_at']}}</span>
					</p>
					@if ($detail['status'] < 3)
						<div class="row cl">
							<div class="mt-5 text-c">
								<span class="btn btn-success radius J_submit">{{trans('common.save')}}</span>
							</div>
						</div>
					@endif
				</form>
			</article>
		</div>
		<div class="tabCon">
			@foreach($detail['logs'] as $log)
				<p class="log_item">
					<span class="create_at">
						<i class="Hui-iconfont" style="color: #5A98DD;">&#xe619;</i> {{$log['create_at']}}
					</span>
					<span class="operator">{{$log['operator']}}</span>
					<span class="action">{{$log['action']}}</span>
					<span class="label label-secondary radius" style="display: inline;">{{$log['order_status']}}</span>
				</p>
			@endforeach
		</div>
		<div class="tabCon images_html">
			<form enctype="multipart/form-data" class="form form-horizontal">
				{!! $detail['wash_images_html']['before'] !!}
				{{--接单后才可以上传清洗前照片--}}
				@if ($detail['status'] == 3)
					<p class="text-c">
						<span class="btn btn-primary radius J_upload">{{trans('common.upload')}}</span>
					</p>
				@endif
			</form>
		</div>
		<div class="tabCon images_html">
			<form enctype="multipart/form-data" class="form form-horizontal">
				{!! $detail['wash_images_html']['after'] !!}
				{{--开始服务后才可以上传清洗后照片--}}
				@if ($detail['status'] == 4)
					<p class="text-c">
						<span class="btn btn-primary radius J_upload">{{trans('common.upload')}}</span>
					</p>
				@endif
			</form>
		</div>
	</div>
@endsection
@section('js')
	<script>
		
		$(function () {
			$.Huitab("#tab_area .tabBar span", "#tab_area .tabCon", "current", "click", "0")
			
			// 手动确认支付
			$('.J_confirm_pay').click(function () {
				layer.confirm('<strong>确认用户已付款？</strong><br>当用户支付出现问题并线下支付后操作', {
					title : '谨慎操作',
				}, function () {
					$.ajax({
						url        : '{{route('confirmPay')}}',
						data       : {order_id : '{{$detail['order_id']}}'},
						type       : 'post',
						dataType   : 'json',
						beforeSend : function () { layer.load(3) },
						success    : function (data) {
							layer.close(layer.load());
							if (data.code == 0) {
								layer.msg('{{trans('common.action_success')}}');
								location.reload();//刷新父页面
							} else {
								layer.msg(data.error, function () {})
							}
						}
					});
				})
			});
			
			// 订单操作
			$('.J_action').click(function () {
				var action_text = $(this).text(),
					new_status  = $(this).attr('data-status');
				layer.confirm('<strong>确认'+action_text+'？', {
					title : action_text,
				}, function () {
					if (new_status == 4) {
						@if (empty($detail['wash_images']['before']))
						layer.msg('请先上传3张清洗前照片后再开始服务！');
						$('#tab_area .tabBar span').eq(2).click();
						return false;
						@endif
					} else if (new_status == 5) {
						@if (empty($detail['wash_images']['after']))
						layer.msg('请先上传3张清洗后照片后再完成服务！');
						$('#tab_area .tabBar span').eq(3).click();
						return false;
						@endif
					}
					$.ajax({
						url        : '{{route('washOrderChangeStatus')}}',
						data       : {
							order_id : '{{$detail['order_id']}}',
							status   : new_status,
						},
						type       : 'post',
						dataType   : 'json',
						beforeSend : function () { layer.load(3) },
						success    : function (data) {
							layer.close(layer.load());
							if (data.code == 0) {
								layer.msg('{{trans('common.action_success')}}');
								location.reload();//刷新父页面
							} else {
								layer.msg(data.error, function () {})
							}
						}
					});
				});
			});
			
			// 上传图片
			$('.J_upload').css({width : '100px'}).click(function () {
				
				$(this).parents('form').ajaxSubmit({
					url        : '{{route('uploadWashImages')}}',
					type       : 'post',
					dataType   : 'json',
					beforeSend : function () {layer.load(3)},
					success    : function (data) {
						layer.close(layer.load());
						if (data.code == 0 && data.msg == 'ok') {
							location.reload();
							layer.msg('{{trans('common.action_success')}}');
						} else {
							layer.msg(data.error, function () {});
						}
					},
					error      : function () {
						layer.close(layer.load());
						layer.msg('请检查图片是否尺寸过大或手动修改过原图格式！', function () {});
					}
				});
			});
		});
		
		$.Huitab = function (tabBar, tabCon, class_name, tabEvent, i) {
			var $tab_menu = $(tabBar);
			// 初始化操作
			$tab_menu.removeClass(class_name);
			$(tabBar).eq(i).addClass(class_name);
			$(tabCon).hide();
			$(tabCon).eq(i).show();
			
			$tab_menu.bind(tabEvent, function () {
				$tab_menu.removeClass(class_name);
				$(this).addClass(class_name);
				var index = $tab_menu.index(this);
				$(tabCon).hide();
				$(tabCon).eq(index).show()
			})
		}
	</script>
@endsection