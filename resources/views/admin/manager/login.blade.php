@extends('admin.base')
@section('css')
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
@endsection
@section('body')
	
	<h1 class="text-c">后台系统登录</h1>
	<form>
		<label>ACCOUNT <input name="name" class="input-text radius mb-10"></label>
		<label>PASSWORD <input name="password" type="password" class="input-text radius"></label>
		<span class="btn btn-primary radius mt-10 J_login">登录</span>
	</form>
@endsection
@section('js')
	<script>
		$('.J_login').click(function () {
			// todo lxt validate form
			
			$.ajax({
				url        : '{{route('managerLogin')}}',
				data       : {
					name : $('input[name=name]').val(),
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
@endsection