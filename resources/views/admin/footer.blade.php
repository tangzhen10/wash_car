<!--_footer 作为公共模版分离出去-->
<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/jquery/1.9.1/jquery.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/layer/2.4/layer.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/static/h-ui/js/H-ui.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/static/h-ui.admin/js/H-ui.admin.page.js')}}"></script>
<script>
	
	$.ajaxSetup({headers : {vfrom : 'ajax', 'X-CSRF-TOKEN' : '{{ csrf_token() }}'}});
	
	$(function () {
		// 复选框
		$('.check-box input').iCheck({
			checkboxClass : 'icheckbox-blue',
			radioClass    : 'iradio-blue',
			increaseArea  : '20%'
		});
		
		// 菜单栏
		var current_url = '{{route(Request::route()->getName())}}';
		for (var x in $('.menu_item')) {
			var url = $('.menu_item').eq(x).attr('href');
			if (url) {
				if (current_url == url) {
					$('.menu_item').eq(x).parent('li').addClass('current');
					$('.menu_item').eq(x).parents('dd').show().siblings('dt').addClass('selected');
					break;
				}
			}
		}
	});
</script>
<!--/_footer 作为公共模版分离出去-->