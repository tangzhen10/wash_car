@extends('admin.base')
@section('js_ueditor')
	<!-- 配置文件 remind lxt 必须放在编辑器源码文件之前 -->
	<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/ueditor/1.4.3/ueditor.config.js')}}"></script>
	<!-- 编辑器源码文件 -->
	<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/ueditor/1.4.3/ueditor.all.js')}}"></script>
@endsection
@section('common_body')
	@yield('body')
@endsection
@section('common_js')
	<script>
		// 验证表单，form页面用真实的验证函数重写掉
		function validate_form() { return true }
		
		$(function () {
			
			// ajax提交表单 李小同 2018-7-11 14:49:54
			$(".J_submit").css({width : '100px'}).click(function () {
				if (validate_form()) {
					$('#form').ajaxSubmit({
						type       : 'post',
						dataType   : 'json',
						beforeSend : function () {layer.load(3)},
						success    : function (data) {
							layer.close(layer.load());
							if (data.code == 0 && data.msg == 'ok') {
								window.parent.location.reload();
								layer.msg('{{trans('common.action_success')}}');
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