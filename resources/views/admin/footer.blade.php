<!--_footer 作为公共模版分离出去-->
<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/jquery/1.9.1/jquery.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/layer/2.4/layer.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/static/h-ui/js/H-ui.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/static/h-ui.admin/js/H-ui.admin.page.js')}}"></script>

<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/My97DatePicker/4.8/WdatePicker.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/datatables/1.10.0/jquery.dataTables.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/laypage/1.2/laypage.js')}}"></script>
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
	
	// 列表页启用数据
	function handleDataStart(obj, id, url) {
		layer.confirm('确认要启用吗？', function () {
			$.ajax({
				url        : url,
				data       : {
					id     : id,
					status : '1'
				},
				type       : 'post',
				dataType   : 'json',
				beforeSend : function () {layer.load(2)},
				success    : function (data) {
					layer.close(layer.load());
					if (data.code == 0 && data.msg == 'ok') {
						$(obj).parents("tr").find(".td-manage").prepend('<a onClick="handleDataStop(this,'+id+',\''+url+'\')" href="javascript:;" title="{{trans('common.disable')}}" style="text-decoration:none"><i class="Hui-iconfont">&#xe631;</i></a>');
						$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">{{trans('common.enable')}}</span>');
						$(obj).remove();
						layer.msg('{{trans('common.enable')}}！', {icon : 6, time : 1000});
					} else {
						layer.msg(data.error, function () {});
					}
				}
			});
		});
	}
	
	// 列表页停用数据
	function handleDataStop(obj, id, url) {
		layer.confirm('确认要停用吗？', function () {
			$.ajax({
				url        : url,
				data       : {
					id     : id,
					status : '0'
				},
				type       : 'post',
				dataType   : 'json',
				beforeSend : function () {layer.load(2)},
				success    : function (data) {
					layer.close(layer.load());
					if (data.code == 0 && data.msg == 'ok') {
						var enable_btn = '<a onClick="handleDataStart(this,'+id+',\''+url+'\')" ' +
							'href="javascript:;" title="{{trans('common.enable')}}" style="text-decoration:none">' +
							'<i class="Hui-iconfont">&#xe615;</i></a>';
						$(obj).parents("tr").find(".td-manage").prepend(enable_btn);
						$(obj).parents("tr").find(".td-status").html('<span class="label label-danger radius">{{trans('common.disable')}}</span>');
						$(obj).remove();
						layer.msg('{{trans('common.disable')}}！', {icon : 5, time : 1000});
					} else {
						layer.msg(data.error, function () {});
					}
				}
			});
		});
	}
	
	// 列表页删除数据
	function handleDataDel(obj, id, url) {
		layer.confirm('确认要删除吗？', function () {
			$.ajax({
				url        : url,
				data       : {
					id     : id,
					status : '-1'
				},
				type       : 'post',
				dataType   : 'json',
				beforeSend : function () {layer.load(2)},
				success    : function (data) {
					layer.close(layer.load());
					if (data.code == 0 && data.msg == 'ok') {
						$(obj).parents("tr").remove();
						layer.msg('已删除!', {icon : 1, time : 1000});
					} else {
						layer.msg(data.error, function () {});
					}
				}
			});
		});
	}
</script>
<!--/_footer 作为公共模版分离出去-->