@extends('admin.base')
@section('css')
	<style>
		.width-400 {
			width: 400px;
		}
		.tip_icon {
			font-size: 20px;
		}
	</style>
@endsection
@section('body')
	<article class="cl pd-20">
		<form enctype="multipart/form-data" class="form form-horizontal" id="form">
			<input type="hidden" name="user_id" value="{{$detail['user_id']}}" />
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3">用户名：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<input class="input-text width-400" value="{{$detail['nickname']}}" name="nickname">
					@if (!empty($detail['avatar']))
						<img src="{{$detail['avatar']}}" style="width: 132px;height: 132px;position: absolute;left: 445px;" />
					@endif
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
					<input type="text" class="input-text width-400" value="{{$detail['phone']}}"
					       name="phone" @if (!empty($check['phone'])) disabled @endif>
					@if ($detail['phone'])
						@if (empty($check['phone']))
							<i class="Hui-iconfont c-warning tip_icon" title="此手机尚未验证，不可用于登录">&#xe6e0;</i>
						@else
							<i class="Hui-iconfont c-success tip_icon" title="此手机已通过验证，可用于登录">&#xe6a8;</i>
						@endif
					@endif
				</div>
			</div>
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3">邮箱：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<input class="input-text width-400" value="{{$detail['email']}}"
					       placeholder="@" name="email" @if (!empty($check['email'])) disabled @endif>
					@if ($detail['email'])
						@if (empty($check['email']))
							<i class="Hui-iconfont c-warning tip_icon" title="此邮箱尚未验证，不可用于登录">&#xe6e0;</i>
						@else
							<i class="Hui-iconfont c-success tip_icon" title="此邮箱已通过验证，可用于登录">&#xe6a8;</i>
						@endif
					@endif
				</div>
			</div>
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3">生日：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<input onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'%y-%M-%d'})"
					       name="birthday" class="input-text Wdate" style="width:120px;" value="{{$detail['birthday']}}">
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
					<input class="btn btn-success radius J_submit" value="{{trans('common.save')}}">
				</div>
			</div>
		</form>
	</article>
@endsection
@section('js')
	<script>
		
		// 验证手机号
		function validate_form() {
			
			var phone    = $('input[name=phone]').val().trim(),
			    email    = $('input[name=email]').val().trim(),
			    birthday = $('input[name=birthday]').val().trim();
			
			var phonePattern = {{config('project.PATTERN.PHONE')}};
			if (phone && !phonePattern.test(phone)) {
				var errorMsg = '{{trans('validation.invalid', ['attr' => trans('common.phone')])}}';
				layer.tips(errorMsg, 'input[name=phone]');
				$('input[name=phone]').focus();
				return false;
			}
			var emailPattern = {{config('project.PATTERN.EMAIL')}};
			if (email && !emailPattern.test(email)) {
				var errorMsg = '{{trans('validation.invalid', ['attr' => trans('common.email')])}}';
				layer.tips(errorMsg, 'input[name=email]');
				$('input[name=email]').focus();
				return false;
			}
			var birthdayPattern = {{config('project.PATTERN.DATE')}};
			if (birthday && !birthdayPattern.test(birthday)) {
				var errorMsg = '{{trans('validation.invalid', ['attr' => trans('common.birthday')])}}';
				layer.tips(errorMsg, 'input[name=birthday]');
				$('input[name=birthday]').focus();
				return false;
			}
			
			return true;
		}
		
		$(function () {
			
			// 点击查看头像
			$('.avatar').click(function () {
				layer.open({
					type       : 1,
					title      : false,
					closeBtn   : 0,
					shadeClose : true,
					content    : '<img src="{{$detail['avatar']}}" />'
				});
			});
		});
	</script>
@endsection