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
	<link rel="stylesheet" type="text/css" href="{{URL::asset('H-ui.admin.page/static/h-ui.admin/css/style.css')}}" />
	<link rel="stylesheet" type="text/css" href="{{URL::asset('H-ui.admin.page/static/h-ui.admin/skin/default/skin.css')}}" id="skin" />
	@yield('css')
</head>
<body>
@include('admin.header')
@include('admin.menu')
<section class="Hui-article-box">
	<nav class="breadcrumb">
		<i class="Hui-iconfont">&#xe67f;</i>
		@foreach($breadcrumbs as $breadcrumb)
			<span class="c-gray en">{{$breadcrumb['text']}}</span>
		@endforeach
		<a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="{{trans('common.refresh')}}">
			<i class="Hui-iconfont">&#xe68f;</i>
		</a>
		<a class="btn btn-primary radius r mr-5" style="line-height:1.6em;margin-top:3px" href="javascript:history.back();" title="{{trans('common.back')}}">
			<i class="Hui-iconfont">&#xe66b;</i>
		</a>
	</nav>
	<div class="Hui-article">
		<article class="cl pd-20">
		@yield('body')
		</article>
	</div>
</section>
@include('admin.footer')
@yield('js')
</body>
</html>