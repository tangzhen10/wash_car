<!--_header 作为公共模版分离出去-->
<header class="navbar-wrapper">
	<div class="navbar navbar-fixed-top">
		<div class="container-fluid cl"><a class="logo navbar-logo f-l mr-10 hidden-xs" href="{{route('adminIndex')}}">{{$pageTitle}}</a>
			<a class="logo navbar-logo-m f-l mr-10 visible-xs" href="{{route('adminIndex')}}">{{$pageTitle}}</a>
			<span class="logo navbar-slogan f-l mr-10 hidden-xs">v1.0</span>
			<a aria-hidden="false" class="nav-toggle Hui-iconfont visible-xs" href="javascript:;">&#xe667;</a>
			<nav id="Hui-userbar" class="nav navbar-nav navbar-userbar hidden-xs">
				<ul class="cl">
					<li><a href="{{route('home')}}" target="_blank" title="去前台"><i class="Hui-iconfont">&#xe603;</i></a></li>
					<li class="dropDown dropDown_hover"><a href="#" class="dropDown_A">{{$manager['name']}} <i class="Hui-iconfont">&#xe6d5;</i></a>
						<ul class="dropDown-menu menu radius box-shadow">
							<li><a href="{{route('managerLogout')}}"><i class="Hui-iconfont">&#xe726;</i> 退出</a></li>
						</ul>
					</li>
					<li id="Hui-skin" class="dropDown right dropDown_hover">
						<a href="javascript:;" class="dropDown_A" title="换肤"><i class="Hui-iconfont" style="font-size:18px">&#xe62a;</i></a>
						<ul class="dropDown-menu menu radius box-shadow">
							<li><a href="javascript:;" data-val="default" title="默认（黑色）">默认（黑色）</a></li>
							<li><a href="javascript:;" data-val="blue" title="蓝色">蓝色</a></li>
							<li><a href="javascript:;" data-val="green" title="绿色">绿色</a></li>
							<li><a href="javascript:;" data-val="red" title="红色">红色</a></li>
							<li><a href="javascript:;" data-val="yellow" title="黄色">黄色</a></li>
							<li><a href="javascript:;" data-val="orange" title="橙色">橙色</a></li>
						</ul>
					</li>
					<li class="admin_notice_count">0</li>
				</ul>
			</nav>
		</div>
	</div>
</header>
<!--/_header 作为公共模版分离出去-->
<script>
	
	try {
		var ws = new WebSocket("ws://www.washcar.com:12346");//连接服务器

		ws.onopen = function (event) {
			console.log("已经与服务器建立了连接\r\n当前连接状态："+this.readyState);
		};
		
		ws.onmessage = function (event) {
			var msg = JSON.parse(event.data);
			$('.admin_notice_count').text(msg.count);
		};
		
		ws.onclose = function (event) {
			console.log("已经与服务器断开连接\r\n当前连接状态："+this.readyState);
		};
		ws.onerror = function (event) {
			console.log("WebSocket异常！");
		};
	} catch (ex) {
		console.log(ex.message);
	}

</script>