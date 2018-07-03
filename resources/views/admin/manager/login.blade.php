<!DOCTYPE>
<html>
<head>
	<meta charset="utf-8">
	<title>{{empty($pageTitle) ? env('PROJECT_NAME').' - admin' : $pageTitle}}</title>
	<meta name="renderer" content="webkit|ie-comp|ie-stand">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
	<link rel="stylesheet" type="text/css" href="{{URL::asset('H-ui.admin.page/static/h-ui/css/H-ui.min.css')}}" />
	<style>
		form {
			background: #fdfdfd;
			border: 1px solid #53aad2;
			width: 400px;
			margin: 20px auto;
			padding: 20px;
			border-radius: 5px;
			box-shadow: 0 0 5px #aaa;
		}
	</style>
</head>
<body>

<h1 class="text-c">后台系统登录</h1>
<form>
	
	<label>ACCOUNT <input name="manager_name" class="input-text radius mb-10"></label>
	<label>PASSWORD <input name="password" type="password" class="input-text radius"></label>
	<span class="btn btn-primary radius mt-10 J_login">登录</span>
</form>

<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/jquery/1.9.1/jquery.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/layer/2.4/layer.js')}}"></script>
<script>
	$('.J_login').click(function () {
		// todo lxt validate form
		
		$.ajax({
			url        : '{{route('managerLogin')}}',
			data       : {
				manager_name : $('input[name=manager_name]').val(),
				password     : $('input[name=password]').val(),
			},
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
	});
</script>
</body>
</html>
