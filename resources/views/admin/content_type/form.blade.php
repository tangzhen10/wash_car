@extends('admin.base')
@section('css')
	<style>
		.short_length {
			width: 12%;
		}
		.middle_length {
			width: 15%;
		}
		.row_operate {
			font-size: 25px;
			cursor: pointer;
		}
		.field_row:hover {
			background: #e0ebf1;
		}
		.note {
			color: #f00;
			font-style: italic;
			margin-left: 40px;
			font-size:12px;
			list-style: decimal;
		}
		.note li em {
			font-style: normal;
			color: green;
			
		}
	</style>
@endsection
@section('body')
	<article class="cl pd-20">
		<ul class="note">
			<span>注意事项</span><i class="Hui-iconfont"></i>
			<li>name：英文字母、数字和下划线组成，不能以纯数字开头，数组在后面加[]</li>
			<li>name：公共属性<em>【{{$keyFields}}】</em>不可以使用</li>
			<li>备选值：单选框和复选框的值，格式为【名1,值1|名2,值2|名3,值3...】</li>
			<li>类型：类型为时间时，备选值为【yyyy-MM-dd HH:mm:ss】表示时间格式，不填表示使用该值作为默认值</li>
		</ul>
		<form action="" method="post" class="form form-horizontal" id="form">
			<input type="hidden" name="id" value="{{$detail['id']}}" />
			<div class="row cl">
				<strong class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>文档类型名称：</strong>
				<div class="formControls col-xs-8 col-sm-10">
					<input type="text" class="input-text radius" value="{{$detail['name']}}" placeholder="" id="adminName" name="type_name">
				</div>
			</div>
			<div class="row cl">
				<strong class="form-label col-xs-4 col-sm-2">结构：</strong>
				<div class="J_structure">
					@foreach($detail['structure'] as $field)
						<div class="cl field_row">
							<label class="form-label col-xs-4 col-sm-2"></label>
							<div class="formControls col-xs-8 col-sm-10">
								<span>名称：</span>
								<input name="field_name_text[]" class="input-text radius short_length mr-10" value="{{$field['name_text']}}">
								<span>类型：</span>
								<select name="field_type[]" class="select-box radius short_length mr-10">
									@foreach($formElements as $item)
										<option value="{{$item['type']}}" @if ($item['type'] == $field['type']) selected @endif>{{$item['name']}}</option>
									@endforeach
								</select>
								<span>name：</span>
								<input name="field_name[]" class="input-text radius short_length mr-10" value="{{$field['name']}}">
								<span>备选值：</span>
								<input name="field_value[]" class="input-text radius mr-10 middle_length" value="{{$field['value']}}">
								<i class="Hui-iconfont c-red row_operate J_del_row">&#xe631;</i>
								<i class="Hui-iconfont c-green row_operate J_up_row">&#xe699;</i>
								<i class="Hui-iconfont c-primary row_operate J_down_row">&#xe698;</i>
							</div>
						</div>
					@endforeach
				</div>
			</div>
			<div class="row cl">
				<div class="col-xs-8 col-sm-10 col-xs-offset-4 col-sm-offset-2">
					<span class="btn btn-success radius J_add_row">添加字段</span>
					<input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;提交&nbsp;&nbsp;">
				</div>
			</div>
		</form>
	</article>
@endsection
@section('js')
	<script>
		
		$(function () {
			
			// 增加
			$('.J_add_row').click(function () {
				var row_html = '<div class="cl field_row">'+
					'<label class="form-label col-xs-4 col-sm-2"></label>'+
					'<div class="formControls col-xs-8 col-sm-10">'+
					'<span>名称：</span>'+
					'<input name="field_name_text[]" class="input-text radius short_length mr-10">'+
					''+
					'<span>类型：</span>'+
					'<select name="field_type[]" class="select-box radius middle_length mr-10">'+
					@foreach($formElements as $item)
						'<option value="{{$item['type']}}">{{$item['name']}}</option>'+
					@endforeach
						'</select>'+
					'<span>name：</span>'+
					'<input name="field_name[]" class="input-text radius short_length mr-10">'+
					'<span>备选值：</span>'+
					'<input name="field_value[]" class="input-text radius mr-10 middle_length">'+
					'<i class="Hui-iconfont c-red row_operate J_del_row">&#xe631;</i>'+
					'<i class="Hui-iconfont c-green row_operate J_up_row">&#xe699;</i>'+
					'<i class="Hui-iconfont c-primary row_operate J_down_row">&#xe698;</i>'+
					'</div>'+
					'</div>';
				$('.J_structure').append(row_html);
			});
			
			@if (!$detail['id']) $('.J_add_row').click(); @endif
			
			// 移除
			$(document).on('click', '.J_del_row', function () {
				$(this).parents('.field_row').remove();
			});
			
			// 上移
			$(document).on('click', '.J_up_row', function () {
				var row = $(this).parents('.field_row');
				row.prev('.field_row').before(row);
			});
			
			// 下移
			$(document).on('click', '.J_down_row', function () {
				var row = $(this).parents('.field_row');
				row.next('.field_row').after(row);
			});
			
			$("#form").validate({
				rules         : {
					name : {
						required  : true,
						minlength : 1,
						maxlength : 16
					}
				},
				onkeyup       : false,
//				focusCleanup  : true,
				success       : "valid",
				submitHandler : function (form) {handleAjaxForm(form)}
			});
		});
	</script>
@endsection