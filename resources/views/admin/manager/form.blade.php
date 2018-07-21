@extends('admin.base')
@section('body')
	<article class="cl pd-20">
		<form action="" method="post" class="form form-horizontal" id="form">
			<input type="hidden" name="id" value="{{$detail['id']}}" />
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>管理员：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<input type="text" class="input-text" value="{{$detail['name']}}" name="name">
				</div>
			</div>
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>初始密码：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<input type="password" class="input-text" autocomplete="off" value="" placeholder="密码" name="password">
				</div>
			</div>
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>确认密码：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<input type="password" class="input-text" autocomplete="off" placeholder="确认密码" name="password_repeat">
				</div>
			</div>
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>状态：</label>
				<div class="formControls col-xs-8 col-sm-9 skin-minimal">
					<div class="radio-box">
						<input type="radio" id="status-1" value="1" name="status" @if ($detail['status'] == '1') checked @endif>
						<label for="status-1">启用</label>
					</div>
					<div class="radio-box">
						<input type="radio" id="status-0" value="0" name="status" @if ($detail['status'] == '0') checked @endif>
						<label for="status-0">停用</label>
					</div>
				</div>
			</div>
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>角色：</label>
				<div class="formControls col-xs-8 col-sm-9 skin-minimal">
					@foreach($roles as $role)
						<label title="{{$role['description']}}" class="mr-10 check-box">
							<input type="checkbox" name="roles[]" value="{{$role['id']}}" @if ($role['checked']) checked @endif />{{$role['name']}}
						</label>
					@endforeach
				</div>
			</div>
			<div class="row cl">
				<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
					<span class="btn btn-primary radius J_submit">{{trans('common.submit')}}</span>
				</div>
			</div>
		</form>
	</article>
@endsection
@section('js')
	<script>
		
		function validate_form() {
			
			var name            = $('input[name=name]').val().trim(),
			    password        = $('input[name=password]').val().trim(),
			    password_repeat = $('input[name=password_repeat]').val().trim(),
			    roles           = $('input[name="roles[]"]:checked');
			if (name == '') {
				var error = '{{trans('validation.required', ['attr' => trans('common.name')])}}';
				layer.tips(error, 'input[name="name"]', {tips : 1});
				$('input[name="name"]').focus();
				return false;
			}
			
			if (password.length < 6) {
				var error = '{{trans('validation.min.string', ['attr' => trans('common.password'), 'min' => '6'])}}';
				layer.tips(error, 'input[name="password"]', {tips : 1});
				$('input[name="password"]').focus();
				return false;
			}
			
			if (password_repeat != password) {
				layer.tips('{{trans('error.different_twice_pwd')}}', 'input[name="password_repeat"]', {tips : 1});
				$('input[name="password_repeat"]').focus();
				return false;
			}
			
			if (roles.length == 0) {
				layer.msg('{{trans('validation.required', ['attr' => trans('common.role')])}}');
				return false;
			}
			return true;
		}
	</script>
@endsection