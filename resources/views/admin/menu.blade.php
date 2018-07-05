<!--_menu 作为公共模版分离出去-->
<aside class="Hui-aside">
	
	<div class="menu_dropdown bk_2">
		@foreach($menu as $item1)
			<dl>
				<dt>{{$item1['name']}}<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
				<dd @if (!empty($item11['display'])) style="display: block;" @endif>
					@if (!empty($item1['sub']))
						<ul>
							@foreach($item1['sub'] as $item2)
								<li @if ($item2['selected']) class="current" @endif>
									<a href="{{route('adminIndex').'/'.$item2['route']}}" title="{{$item2['name']}}">{{$item2['name']}}</a>
								</li>
							@endforeach
						</ul>
					@endif
				</dd>
			</dl>
		@endforeach
	</div>
</aside>
<div class="dislpayArrow hidden-xs"><a class="pngfix" href="javascript:void(0);" onClick="displaynavbar(this)"></a>
</div>
<!--/_menu 作为公共模版分离出去-->