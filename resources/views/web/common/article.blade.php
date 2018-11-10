<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
	<meta content="yes" name="apple-mobile-web-app-capable">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta content="black" name="apple-mobile-web-app-status-bar-style">
	<title>{{empty($name) ? env('PROJECT_NAME') : $name}}</title>
</head>

<body>
{!! $detail['content'] !!}
</body>

</html>