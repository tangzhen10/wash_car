@extends('admin.base')
@section('css')
	<style>
		.short_length {
			width: 20%;
		}
		.middle_length {
			width: 30%;
		}
		.plus_minus {
			font-weight: bold;
			font-size: 25px;
			cursor: pointer;
		}
	
	</style>
@endsection
@section('body')
	<article class="cl pd-20">
		<form action="" method="post" class="form form-horizontal" id="form-manager">
			<input type="hidden" name="id" value="{{$detail['id']}}" />
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>文档类型名称：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<input type="text" class="input-text" value="{{$detail['name']}}" placeholder="" id="adminName" name="name">
				</div>
			</div>
			<div class="row cl J_fields">
				<label class="form-label col-xs-4 col-sm-3">字段：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<span>名称：</span>
					<input name="name_text" class="input-text short_length mr-10">
					
					<span>类型：</span>
					<select name="field" class="select-box short_length mr-10">
						@foreach($form_elements as $item)
							<option value="{{$item['id']}}">{{$item['name']}}</option>
						@endforeach
					</select>
					
					<span>name：</span>
					<input name="name" class="input-text middle_length" placeholder="纯英文字母，数组在后面加[]">
					
					<i class="Hui-iconfont c-green plus_minus J_add_row">&#xe604;</i>
					<i class="Hui-iconfont c-red plus_minus J_rem_row" style="display: none;">&#xe631;</i>
				</div>
			</div>
			
			{{--<div class="row cl">
					<label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>确认密码：</label>
					<div class="formControls col-xs-8 col-sm-9">
						<input type="password" class="input-text" autocomplete="off" placeholder="确认新密码" id="password_repeat" name="password_repeat">
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
					<label class="form-label col-xs-4 col-sm-3">角色：</label>
					<div class="formControls col-xs-8 col-sm-9">
						@foreach($roles as $role)
							<label title="{{$role['description']}}" class="mr-10">
								<input type="checkbox" name="roles[]" value="{{$role['id']}}" @if ($role['checked']) checked @endif />{{$role['name']}}
							</label>
						@endforeach
					</div>
				</div>--}}
			<div class="row cl">
				<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
					<input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;提交&nbsp;&nbsp;">
				</div>
			</div>
		</form>
	</article>
@endsection
@section('js')
	<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
	<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/jquery.validation/1.14.0/validate-methods.js')}}"></script>
	<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/jquery.validation/1.14.0/messages_zh.js')}}"></script>
	<script>
		
		$(function () {
			
			$('.J_add_row').click(function () {
				var row_html = '<label class="form-label col-xs-4 col-sm-3">字段：</label>'+
					'<div class="formControls col-xs-8 col-sm-9">'+
					'<span>名称：</span>'+
					'<input name="name_text" class="input-text short_length mr-10">'+
					''+
					'<span>类型：</span>'+
					'<select name="field" class="select-box short_length mr-10">'+
						@foreach($form_elements as $item)
							'<option value="{{$item['id']}}">{{$item['name']}}</option>'+
						@endforeach
							'</select>'+
					'<span>name：</span>'+
					'<input name="name" class="input-text middle_length" placeholder="纯英文字母，数组在后面加[]">'+
					'<i class="Hui-iconfont c-green plus_minus J_add_row">&#xe604;</i>'+
					'<i class="Hui-iconfont c-red plus_minus J_rem_row" style="display: none;">&#xe631;</i>'+
					'</div>';
				$('.J_fields').after(row_html);
			});
			
			$('.skin-minimal input').iCheck({
				checkboxClass : 'icheckbox-blue',
				radioClass    : 'iradio-blue',
				increaseArea  : '20%'
			});
			
			$("#form-manager").validate({
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
				submitHandler : function (form) {
					$(form).ajaxSubmit({
						type       : 'post',
						dataType   : 'json',
						beforeSend : function () {layer.load(3)},
						success    : function (data) {
							layer.close(layer.load());
							if (data.code == 0 && data.msg == 'ok') {
								var index = parent.layer.getFrameIndex(window.name);
								window.parent.location.reload();
								parent.layer.close(index);
							} else {
								layer.msg(data.error, function () {});
							}
						}
					});
				}
			});
		});
	</script>
@endsection