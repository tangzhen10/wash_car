@extends('web.common.header')
@section('body')
	<body class="bggrey">
	<div class="container">
		<div class="banner">
			<img src="{{$detail['detail']['top_image']}}" alt="{{$detail['name']}}">
		</div>
		<div class="content">
			<div class="info-block">
				<div class="info-title">
					<h3>{{$detail['name']}}</h3>
				</div>
				<div class="info-detail">
					<p>{{$detail['detail']['introduction']}}</p>
				</div>
			</div>
			<div class="info-block">
				<div class="info-detail"><?= $detail['detail']['detail'] ?></div>
			</div>
		
		</div>
		<div class="fixed-bottom">
			<a href="{{$detail['detail']['link']}}" class="fixed-btn">前往投资</a>
		</div>
	</div>
	
	<script type="text/javascript">
		
		$('.tab-title li').click(function () {
			$(this).addClass('active').siblings().removeClass('active');
			$('.tab-content').children().eq($(this).index()).show().siblings('.queryContent').hide();
		});
	
	</script>
	</body>
@endsection