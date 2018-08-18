@extends('web.common.header')
@section('body')
	<body>
	<div class="container fixed-top">
		<div class="top">
			<ul class="tab-title">
				@forelse($groups as $group)
					<li>{{$group['title']}}</li>
				@empty
					<li>{{trans('common.no_data')}}</li>
				@endforelse
			</ul>
		</div>
		<div class="content">
			<ul class="tab-content">
				@forelse($groups as $group)
					<li class="queryContent">
						<div class="ad-list">
							@if (isset($group['list']))
								@foreach($group['list'] as $item)
									<div class="col" onclick="window.location.href = '{{route('webInvestDetail', ['id' => $item['id']])}}'">
										<div class="ad-row1 ">
											<div class="ad-name flex">
												@if (!empty($item['detail']['logo']['value']))
													<div class="ad-logo flex-item">
														<img src="{{$item['detail']['logo']['value']}}" alt="{{$item['name']}}">
													</div>
												@endif
												
												<div class="ad-icon flex flex-item">
													@if (!empty($item['detail']['level']['value']))
														<span class="level levelA">{{$item['detail']['level']['value']}}</span>
													@endif
													@if (!empty($item['detail']['label']))
														@foreach($item['detail']['label']['value'] as $label)
															<span class="label">{{$label}}</span>
														@endforeach
													@endif
												</div>
											</div>
											
											@if (!empty($item['sub_name']))
												<div class="ad-des">{{$item['sub_name']}}</div>
											@endif
										</div>
										<div class="ad-row2 flex">
											<div class="item-box rate-left">
												@if (!empty($item['detail']['first_refund_max']))
													<div>
														<span class="highlight">{{$item['detail']['first_refund_max']['value']}}</span>元
													</div>
													<div>{{$item['detail']['first_refund_max']['text']}}</div>
												@endif
											</div>
											<div class="item-box rate-center">
												@if (!empty($item['detail']['platform_rate']))
													<div>
														<span class="highlight">{{$item['detail']['platform_rate']['value']}}</span>%
													</div>
													<div>{{$item['detail']['platform_rate']['text']}}</div>
												@endif
											</div>
											<div class="item-box rate-right">
												@if (!empty($item['detail']['comprehensive_rate']))
													<div>
														<span class="highlight">{{$item['detail']['comprehensive_rate']['value']}}</span>%
													</div>
													<div>{{$item['detail']['comprehensive_rate']['text']}}</div>
												@endif
											</div>
										</div>
									</div>
								@endforeach
							@else
								<div style="margin-top: 10px;">{{trans('common.no_data')}}</div>
							@endif
						</div>
					</li>
				@empty
					<li style="margin-top: 10px;">{{trans('common.no_data')}}</li>
				@endforelse
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