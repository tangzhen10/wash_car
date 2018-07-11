@extends('admin.base')
@section('body')
	<article class="cl pd-20">
		<form action="" method="post" enctype="multipart/form-data" class="form form-horizontal" id="form-member">
			<input type="hidden" name="user_id" value="{{$detail['user_id']}}" />
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>用户名：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<input type="text" class="input-text" value="{{$detail['nickname']}}" placeholder="" id="nickname" name="nickname">
				</div>
			</div>
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3">性别：</label>
				<div class="formControls col-xs-8 col-sm-9 skin-minimal">
					<div class="radio-box">
						<input name="gender" type="radio" id="gender-1" value="1" @if ($detail['gender'] == 1) checked @endif>
						<label for="gender-1">男</label>
					</div>
					<div class="radio-box">
						<input name="gender" type="radio" id="gender-2" value="2" @if ($detail['gender'] == 2) checked @endif>
						<label for="gender-2">女</label>
					</div>
				</div>
			</div>
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3">手机：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<input type="text" class="input-text" style="width: 300px;" value="{{$detail['phone']}}"
					       id="phone" name="phone" @if (!empty($check['phone'])) disabled @endif>
					@if ($detail['phone'])
						@if (empty($check['phone']))
							<span><i class="Hui-iconfont c-warning" style="font-size: 20px;">&#xe6e0;</i> 此手机尚未验证，不可用于登录</span>
						@else
							<span><i class="Hui-iconfont c-success" style="font-size: 20px;">&#xe6a8;</i> 此手机已通过验证，可用于登录</span>
						@endif
					@endif
				</div>
			</div>
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3">邮箱：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<input type="text" class="input-text" style="width: 300px;" value="{{$detail['email']}}"
					       placeholder="@" name="email" id="email" @if (!empty($check['email'])) disabled @endif>
					@if ($detail['email'])
						@if (empty($check['email']))
							<span><i class="Hui-iconfont c-warning" style="font-size: 20px;">&#xe6e0;</i> 此邮箱尚未验证，不可用于登录</span>
						@else
							<span><i class="Hui-iconfont c-success" style="font-size: 20px;">&#xe6a8;</i> 此邮箱已通过验证，可用于登录</span>
						@endif
					@endif
				</div>
			</div>
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3">生日：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<input type="text" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'%y-%M-%d'})"
					       id="birthday" name="birthday" class="input-text Wdate" style="width:120px;"
					       value="{{$detail['birthday']}}">
				</div>
			</div>
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3">头像：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<span class="btn-upload form-group">
						<input class="input-text upload-url" type="text" name="uploadfile" value="{{$detail['avatar']}}"
						       id="uploadfile" readonly nullmsg="请添加附件！" style="width:200px">
						<a href="javascript:void();" class="btn btn-primary radius upload-btn">
							<i class="Hui-iconfont">&#xe642;</i> 浏览文件
						</a>
						<input type="file" multiple name="file" class="input-file">
					</span>
				</div>
			</div>
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3">注册时间：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<input class="input-text" value="{{$detail['create_at']}}" disabled>
				</div>
			</div>
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3">最近登录时间：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<input class="input-text" value="{{$detail['last_login_at']}}" disabled>
				</div>
			</div>
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3">最近登录IP：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<input class="input-text" value="{{$detail['last_login_ip']}}" disabled>
				</div>
			</div>
			<div class="row cl">
				<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
					<input class="btn btn-success radius" type="submit" value="&nbsp;&nbsp;保存&nbsp;&nbsp;">
				</div>
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
			
			$("#form-member").validate({
				rules         : {
					nickname : {
						required  : true,
						minlength : 1,
						maxlength : 16
					}
				},
				// todo lxt 表单验证
				onkeyup       : false,
//				focusCleanup  : true,
				success       : "valid",
				submitHandler : function (form) {handleAjaxForm(form)}
			});
		});
	</script>
@endsection