<nav class="breadcrumb">
	<i class="Hui-iconfont">&#xe67f;</i>
	@foreach($breadcrumbs as $breadcrumb)
		<span class="c-gray en">{{$breadcrumb['text']}}</span>
	@endforeach
	<a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新">
		<i class="Hui-iconfont">&#xe68f;</i>
	</a>
</nav>