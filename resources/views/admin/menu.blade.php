<!--_menu 作为公共模版分离出去-->
<aside class="Hui-aside">
	
	<div class="menu_dropdown bk_2">
		@foreach($menus as $item1)
			<dl>
				<dt>{{$item1['name']}}<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
				<dd>
					@if (!empty($item1['sub']))
						<ul>
							@foreach($item1['sub'] as $item2)
								<li>
									<a class="menu_item" href="{{route('adminIndex').'/'.$item2['route']}}" title="{{$item2['name']}}">{{$item2['name']}}</a>
								</li>
							@endforeach
						</ul>
					@endif
				</dd>
			</dl>
		@endforeach
	</div>
</aside>
<div class="dislpayArrow hidden-xs"><a class="pngfix" href="javascript:;" onClick="displaynavbar(this)"></a></div>
<!--/_menu 作为公共模版分离出去-->