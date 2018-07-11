@extends('admin.base')
@section('css')
	<style>
		.public_attr {
			width: 49%;
			float: left;
		}
		.private_attr {
			width: 49%;
			float: right;
		}
		.form_filed {
			display: inline-block;
			text-align: right;
			width: 20%;
		}
		.form_value {
			display: inline-block;
			width: 70%;
		}
	</style>
@endsection
@section('body')
	<article class="cl pd-20">
		<form action="" method="post" class="form form-horizontal" id="form">
			<input type="hidden" name="id" value="{{$detail['id']}}" />
			<div class="public_attr">
				<h4>公共属性</h4>
				<p>
					<span class="form_filed"><span class="c-red">*</span>标题：</span>
					<input class="input-text radius form_value" value="{{$detail['name']}}" name="name">
				</p>
				<p>
					<span class="form_filed">副标题：</span>
					<input class="input-text radius form_value" value="{{$detail['sub_name']}}" name="sub_name">
				</p>
				<p>
					<span class="form_filed">开始时间：</span>
					<input class="input-text radius form_value Wdate" value="{{$detail['start_time']}}" name="start_time"
					       onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'#F{$dp.$D(\'datemax\')}',skin:'whyGreen'})" id="datemin">
				</p>
				<p>
					<span class="form_filed">结束时间：</span>
					<input class="input-text radius form_value Wdate" value="{{$detail['end_time']}}" name="end_time"
					       onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'datemin\')}',skin:'whyGreen'})" id="datemax">
				</p>
				<p>
					<span class="form_filed">启用：</span>
					<span class="skin-minimal form_value">
						<span class="radio-box">
							<label><input type="radio" id="status-1" value="1" name="status" @if ($detail['status'] == '1') checked @endif> 启用</label>
						</span>
						<span class="radio-box">
							<label><input type="radio" id="status-0" value="0" name="status" @if ($detail['status'] == '0') checked @endif> 停用</label>
						</span>
					</span>
				</p>
			</div>
			
			<div class="private_attr">
				<h4>私有属性</h4>
				<span>副标题：</span>
				<input type="text" class="input-text radius" value="{{$detail['name']}}" placeholder="" id="adminName" name="type_name">
			</div>
			<div style="clear: both;" class="text-c">
				<input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;提交&nbsp;&nbsp;">
			</div>
		</form>
	</article>
@endsection
@section('js')
	<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
	<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/jquery.validation/1.14.0/validate-methods.js')}}"></script>
	<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/jquery.validation/1.14.0/messages_zh.js')}}"></script>
	<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/My97DatePicker/4.8/WdatePicker.js')}}"></script>
	<script>
		
		$(function () {
			
			$('.skin-minimal input').iCheck({
				checkboxClass : 'icheckbox-blue',
				radioClass    : 'iradio-blue',
				increaseArea  : '20%'
			});
			
			$("#form").validate({
				rules         : {
					name : {
						required  : true,
						minlength : 1,
						maxlength : 16
					}
				},
				onkeyup       : false,
//				focusCleanup  : true,
				success       : "valid",
				submitHandler : function (form) {handleAjaxForm(form)}
			});
		});
	</script>
@endsection