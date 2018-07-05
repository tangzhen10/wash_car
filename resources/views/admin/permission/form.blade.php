@extends('admin.base')
@section('css')
	<style>
	
	</style>
@endsection
@section('body')
	<article class="cl pd-20">
		<form class="form form-horizontal" id="form-permission">
			<input type="hidden" name="id" value="{{$detail['id']}}" />
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>权限名称：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<input type="text" class="input-text" value="{{$detail['name']}}" placeholder="" id="name" name="name">
				</div>
			</div>
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>父节点：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<select class="select-box" name="pid">
						<option value="0">Top</option>
						@foreach($permissions as $permission)
							<option style="font-weight: bold;" {{$permission['selected']}} value="{{$permission['id']}}">{{$permission['name']}}</option>
							@if (!empty($permission['sub']))
								@foreach($permission['sub'] as $item)
									<option value="{{$item['id']}}" {{$item['selected']}}>
										@for($i = 1; $i < $item['level']; ++$i) &nbsp;&nbsp;&nbsp;&nbsp; @endfor
										{{$item['name']}}
									</option>
								@endforeach
							@endif
						@endforeach
					</select>
				</div>
			</div>
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3">路由：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<input type="text" class="input-text" value="{{$detail['route']}}" id="route" name="route">
				</div>
			</div>
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>排序：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<input type="number" class="input-text" value="{{$detail['sort']}}" id="sort" name="sort">
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
				<label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>显示：</label>
				<div class="formControls col-xs-8 col-sm-9 skin-minimal">
					<div class="radio-box">
						<input type="radio" id="show-1" value="1" name="show" @if ($detail['show'] == '1') checked @endif>
						<label for="show-1">显示</label>
					</div>
					<div class="radio-box">
						<input type="radio" id="show-0" value="0" name="show" @if ($detail['show'] == '0') checked @endif>
						<label for="show-0">不显示</label>
					</div>
				</div>
			</div>
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
			$('.skin-minimal input').iCheck({
				checkboxClass : 'icheckbox-blue',
				radioClass    : 'iradio-blue',
				increaseArea  : '20%'
			});
			
			$("#form-permission").validate({
				rules         : {
					name   : {
						required  : true,
						minlength : 4,
						maxlength : 16
					},
					route  : {
						required : false,
					},
					status : {
						required : true,
					}
				},
				onkeyup       : false,
				focusCleanup  : true,
				success       : "valid",
				submitHandler : function (form) {
					$(form).ajaxSubmit({
						url        : '{{route('permissionForm')}}',
						type       : 'post',
						dataType   : 'json',
						beforeSend : function () {layer.load(3)},
						success    : function (data) {
							layer.close(layer.load());
							if (data.code == 0 && data.msg == 'ok') {
								layer.msg('ok');
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