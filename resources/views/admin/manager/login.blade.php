@extends('admin.base')
@section('css')
	<style>
		body {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			min-width: 1000px;
			z-index: -10;
			zoom: 1;
			background: #fff url("{{URL::asset('src/wallpaper.jpg')}}") no-repeat center 0;
			background-size: cover;
			-webkit-background-size: cover;
			-o-background-size: cover;
			
		}
		.login-form {
			border: 1px solid #53aad2;
			width: 400px;
			margin: 120px 60%;
			padding: 20px;
			border-radius: 5px;
			box-shadow: 0 0 15px #333;
		}
		form {
			background: #fdfdfd;
			padding: 20px;
			border-radius: 5px;
		}
	</style>
@endsection
@section('body')
	<div class="login-form">
		<h2 class="text-c c-white pt-5 pb-20">{{env('PROJECT_NAME')}}后台管理系统</h2>
		<form id="form-login">
			<div class="cl mb-10">
				<label class="form-label"><strong>ACCOUNT</strong></label>
				<div class="formControls">
					<input type="text" class="input-text radius" value="" placeholder="" id="name" name="name" datatype="*4-16" nullmsg="用户账户不能为空">
				</div>
			</div>
			<div class="cl">
				<label class="form-label"><strong>PASSWORD</strong></label>
				<div class="formControls">
					<input type="password" class="input-text radius" value="" placeholder="" id="password" name="password">
				</div>
			</div>
			<input type="submit" class="btn btn-primary radius mt-10 J_login" value="&nbsp;&nbsp;登录&nbsp;&nbsp;" />
		</form>
	</div>

@endsection
@section('js')
	<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
	<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/jquery.validation/1.14.0/messages_zh.js')}}"></script>
	<script>
		$('#form-login').validate({
			
			rules         : {
				name     : {
					required : true
				},
				password : {
					required : true
				}
			},
			onkeyup       : false,
			focusCleanup  : true,
			success       : "valid",
			submitHandler : function (form) {
				$(form).ajaxSubmit({
					type       : 'post',
					dataType   : 'json',
					beforeSend : function () {layer.load(3)},
					success    : function (data) {
						layer.close(layer.load());
						if (data.code == 0 && data.msg == 'ok') {
							layer.msg('登录成功');
							location.href = '{{route('adminIndex')}}';
						} else {
							layer.msg(data.error, function () {});
						}
					}
				});
			}
		});
	</script>
@endsection