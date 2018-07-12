<!DOCTYPE>
<html>
<head>
	<meta charset="utf-8">
	<title>{{empty($pageTitle) ? env('PROJECT_NAME').' - admin' : $pageTitle}}</title>
	<meta name="renderer" content="webkit|ie-comp|ie-stand">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
	<link rel="stylesheet" type="text/css" href="{{URL::asset('H-ui.admin.page/static/h-ui/css/H-ui.min.css')}}" />
	<link rel="stylesheet" type="text/css" href="{{URL::asset('H-ui.admin.page/static/h-ui.admin/css/H-ui.admin.css')}}" />
	<link rel="stylesheet" type="text/css" href="{{URL::asset('H-ui.admin.page/lib/Hui-iconfont/1.0.8/iconfont.css')}}" />
	@yield('css')
</head>
<body>
@yield('body')
<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/jquery/1.9.1/jquery.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/layer/2.4/layer.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/static/h-ui/js/H-ui.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/static/h-ui.admin/js/H-ui.admin.page.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/jquery.validation/1.14.0/validate-methods.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/jquery.validation/1.14.0/messages_zh.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/My97DatePicker/4.8/WdatePicker.js')}}"></script>
<script>
	
	$(function () {
		// iCkeck 单选框
		$('.skin-minimal input').iCheck({
			checkboxClass : 'icheckbox-blue',
			radioClass    : 'iradio-blue',
			increaseArea  : '20%'
		});
	});
	
	// ajax提交表单 李小同 2018-7-11 14:49:54
	function handleAjaxForm(form) {
		$(form).ajaxSubmit({
			type       : 'post',
			dataType   : 'json',
			beforeSend : function () {layer.load(3)},
			success    : function (data) {
				layer.close(layer.load());
				if (data.code == 0 && data.msg == 'ok') {
					var index = parent.layer.getFrameIndex(window.name);
					window.parent.location.reload();
					layer.msg('{{trans('common.action_success')}}');
//					parent.layer.close(index);
				} else {
					layer.msg(data.error, function () {});
				}
			}
		});
	}
</script>
@yield('js')
</body>
</html>
