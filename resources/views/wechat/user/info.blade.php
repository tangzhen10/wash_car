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

<p>
	<span>头像</span>
	<span class="f-r"><img class="radius" style="width: 66px;" src="{{$userInfo['avatar']}}" /></span>
</p>
<p>
	<span>名字</span>
	<span class="f-r">{{$userInfo['nickname']}}</span>
</p>
<p>
	<span>性别</span>
	<span class="f-r">{{$userInfo['gender_text']}}</span>
</p>
<p>
	<span>地区</span>
	<span class="f-r">{{$userInfo['country']}} {{$userInfo['province']}} {{$userInfo['city']}}</span>
</p>
<p>
	<span>注册时间</span>
	<span class="f-r">{{$userInfo['create_at']}}</span>
</p>
<p>
	<span>最近登录时间</span>
	<span class="f-r">{{$userInfo['last_login_at']}}</span>
</p>
<p>
	<span>最近登录IP</span>
	<span class="f-r">{{$userInfo['last_login_ip']}}</span>
</p>


<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/jquery/1.9.1/jquery.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/layer/2.4/layer.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/static/h-ui/js/H-ui.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/static/h-ui.admin/js/H-ui.admin.page.js')}}"></script>
@yield('js')
</body>
</html>
