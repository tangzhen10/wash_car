@extends('admin.base')
@section('css')
	<link rel="stylesheet" type="text/css" href="{{URL::asset('css/form.css')}}" />
	<style>
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
		.log_item {
			padding: 5px 20px;
			border: 1px solid #ccc;
			border-radius: 4px;
			margin: 10px 25px;
		}
		.log_item span.create_at {
			color: #333;
		}
		.log_item span.operator {
			padding: 0 10px;
		}
		.log_item span.action {
			color: #5A98DD;
			padding-left: 10px;
			display: inline-block;
		}
	
	</style>
@endsection
@section('body')
	<div id="tab_area" class="HuiTab">
		<div class="tabBar clearfix">
			<span>订单详情</span>
			<span>订单日志</span>
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
						<span>{{$detail['status_text']}}</span>
						@if ($detail['status'] == '2')
							<span class="btn btn-secondary-outline radius ml-10 take_order">{{trans('common.take_order')}}</span>
						@elseif ($detail['status'] == '3')
							
						@endif
					</p>
					<p>
						<span class="form_filed_row">{{trans('common.wash_product')}}：</span>
						<span>
						<a href="{{route('productList')}}?filter_wash_product_id={{$detail['wash_product_id']}}" target="_blank">
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
							{{$detail['plate_number']}}
							| {{$detail['brand']}} - {{$detail['model']}}
							| {{$detail['color']}}
						</span>
					</p>
					<?= $html ?>
					<p>
						<span class="form_filed_row">{{trans('common.wash_time')}}：</span>
						<select class="select-box radius" style="width: 75%;position: relative;top: 0px;" name="wash_time">
							@foreach($wash_time_list as $item)
								<option value="{{$item}}" @if ($item == $detail['wash_time']) selected @endif>{{$item}}</option>
							@endforeach
						</select>
					</p>
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
					<div class="row cl">
						<div class="mt-5 text-c">
							<span class="btn btn-success radius J_submit">{{trans('common.save')}}</span>
						</div>
					</div>
				</form>
			</article>
		</div>
		<div class="tabCon">
			@foreach($detail['logs'] as $log)
				<p class="log_item">
					<span class="create_at">{{$log['create_at']}}</span>
					<span class="operator">{{$log['operator']}}</span>
					<span class="action">{{$log['action']}}</span>
				</p>
			@endforeach
		</div>
	</div>
@endsection
@section('js')
	<script>
		
		$(function () {
			$.Huitab("#tab_area .tabBar span", "#tab_area .tabCon", "current", "click", "0")
			
			// 手动确认支付
			$('.J_confirm_pay').click(function () {
				var order_id = $(this).attr('data_order_id');
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
			
			// 接单
			$('.take_order').click(function () {
				
				$.ajax({
					url        : '{{route('washOrderChangeStatus')}}',
					data       : {
						order_id : '{{$detail['order_id']}}',
						status   : 2,
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