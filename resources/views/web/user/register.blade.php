@extends('web.common.header')
@section('body')
	<style>
		label {
			display: block;
			height: 50px;
		}
		lable input {
			border: 1px solid #999;
			radius: 5px;
		}
	</style>
	<form>
		<label>
			<span>account</span>
			<input name="account" />
		</label>
		<label>
			<span>password</span>
			<input name="password" type="password" />
		</label>
		<label>
			<span>repassword</span>
			<input name="repassword" type="password" />
		</label>
		<a href="{{route('webLogin')}}">已有账号，去登录</a>
		<input type="submit" value="register" />
	</form>
@endsection