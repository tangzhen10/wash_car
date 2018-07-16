@extends('web.common.header')
@section('body')
	<body>
	<div class="container fixed-top">
		<div class="top">
			<ul class="tab-title">
				@foreach($article_group as $group)
					<li>{{$group['group_title']}}</li>
				@endforeach
			</ul>
		</div>
		<div class="content">
			<ul class="tab-content">
				@foreach($article_group as $group)
					<li class="queryContent">
					<div class="ad-list">
						@foreach($group['list'] as $item)
							<div class="col" onclick="window.location.href = '{{route('webInvestDetail', ['id' => $item['id']])}}'">
								<div class="ad-row1 ">
									<div class="ad-name flex">
										<div class="ad-logo flex-item">
											<img src="{{$item['detail']['logo']['value']}}" alt="{{$item['name']}}">
										</div>
										
										<div class="ad-icon flex flex-item">
											<span class="level levelA">{{$item['detail']['level']['value']}}</span>
											@if (!empty($item['detail']['label']))
												@foreach($item['detail']['label']['value'] as $label)
													<span class="label">{{$label}}</span>
												@endforeach
											@endif
										</div>
									</div>
									
									<div class="ad-des">{{$item['sub_name']}}</div>
								</div>
								<div class="ad-row2 flex">
									<div class="item-box rate-left">
										<div>
											<span class="highlight">{{$item['detail']['first_refund_max']['value']}}</span>元
										</div>
										<div>
											{{$item['detail']['first_refund_max']['text']}}
										</div>
									</div>
									<div class="item-box rate-center">
										<div>
											<span class="highlight ">{{$item['detail']['platform_rate']['value']}}</span>%
										</div>
										<div>
											{{$item['detail']['platform_rate']['text']}}
										</div>
									</div>
									<div class="item-box rate-right ">
										<div>
											<span class="highlight">{{$item['detail']['comprehensive_rate']['value']}}</span>%
										</div>
										<div>
											{{$item['detail']['comprehensive_rate']['text']}}
										</div>
									</div>
								</div>
							</div>
						@endforeach
					</div>
				</li>
				@endforeach
			</ul>
		</div>
	</div>
	
	<script type="text/javascript">
		
		$('.tab-title li').click(function () {
			$(this).addClass('active').siblings().removeClass('active');
			$('.tab-content').children().eq($(this).index()).show().siblings('.queryContent').hide();
		});
		$('.tab-title li:first').click();
	
	</script>
	</body>
@endsection