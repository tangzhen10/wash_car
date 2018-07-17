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
			font-size: 12px;
			list-style: decimal;
		}
		.note li em {
			font-style: normal;
			color: green;
			
		}
		.note_nav {
			border: 1px solid #ccc;
			border-radius: 5px;
			padding: 5px 10px;
			background: #FFEEBC;
		}
		.note_nav i.c-black {
			cursor: pointer;
			font-size: 24px;
			position: relative;
			top: -8px;
		}
	</style>
@endsection
@section('body')
	<article class="cl pd-20">
		<p class="note_nav">
			<i class="Hui-iconfont">&#xe6e0;</i> 注意事项
			<i class="Hui-iconfont c-black r J_show_note">&#xe698;</i>
			<i class="Hui-iconfont c-black r J_hide_note" style="display: none">&#xe699;</i>
		</p>
		<ul class="note" style="display: none;">
			<li>name：英文字母、数字和下划线组成，不能以纯数字开头</li>
			<li>name：公共属性<em>【{{$keyFields}}】</em>不可以使用</li>
			<li>备选值：单选框、复选框、下拉菜单的值，格式为【值1,值2,值3...】</li>
			<li>备选值：若类型不是单选框、复选框、下拉菜单，则作为提示语显示在输入框里</li>
			<li>类型：类型为时间时，备选值为【yyyy-MM-dd HH:mm:ss】表示时间格式，不填表示使用该值作为默认值</li>
		</ul>
		<form action="" method="post" class="form form-horizontal" id="form">
			<input type="hidden" name="id" value="{{$detail['id']}}" />
			<div class="row cl">
				<strong class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>文档类型名称：</strong>
				<div class="formControls col-xs-8 col-sm-10">
					<input type="text" class="input-text radius" value="{{$detail['name']}}" name="name">
				</div>
			</div>
			<div class="row cl">
				<strong class="form-label col-xs-4 col-sm-2">结构：</strong>
				<div class="formControls col-xs-8 col-sm-10">
					<table class="table table-border table-striped table-bordered table-bg table-hover">
						<thead>
						<tr class="text-c">
							<th>{{trans('common.name')}}</th>
							<th>{{trans('common.type')}}</th>
							<th>name属性</th>
							<th>备选值、提示语</th>
							<th>{{trans('common.action')}}</th>
						</tr>
						</thead>
						<tbody>
						@foreach($detail['structure'] as $field)
							<tr>
								<td>
									<input name="field_name_text[]" class="input-text radius" value="{{$field['name_text']}}">
								</td>
								<td>
									<select name="field_type[]" class="select-box radius">
										@foreach($formElements as $item)
											<option value="{{$item['type']}}" @if ($item['type'] == $field['type']) selected @endif>{{$item['name']}}</option>
										@endforeach
									</select>
								</td>
								<td>
									<input name="field_name[]" class="input-text radius" value="{{$field['name']}}">
								</td>
								<td>
									<input name="field_value[]" class="input-text radius" value="{{$field['value']}}">
								</td>
								<td class="text-c">
									<i class="Hui-iconfont c-red row_operate J_del_row">&#xe631;</i>
									<i class="Hui-iconfont c-green row_operate J_up_row">&#xe699;</i>
									<i class="Hui-iconfont c-primary row_operate J_down_row">&#xe698;</i>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
			</div>
			<div class="row cl">
				<div class="col-xs-8 col-sm-10 col-xs-offset-4 col-sm-offset-2">
					<span class="btn btn-success radius J_add_row">添加字段</span>
					<input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;提交&nbsp;&nbsp;">
					{{--<input class="btn btn-primary radius J_submit" value="提交">--}}
				</div>
			</div>
		</form>
	</article>
@endsection
@section('js')
	<script>
		
		$(function () {
			
			// 展示注意事项
			$('.J_show_note').click(function () {
				$(this).hide();
				$('.J_hide_note').show();
				$('.note').slideDown();
			});
			
			// 隐藏注意事项
			$('.J_hide_note').click(function () {
				$(this).hide();
				$('.J_show_note').show();
				$('.note').slideUp();
			});
			
			// 增加
			$('.J_add_row').click(function () {
				var row_html = '<tr>'+
					'<td>'+
					'<input name="field_name_text[]" class="input-text radius">'+
					'</td>'+
					'<td>'+
					'<select name="field_type[]" class="select-box radius">'+
						@foreach($formElements as $item)
							'<option value="{{$item['type']}}">{{$item['name']}}</option>'+
						@endforeach
							'</select>'+
					'</td>'+
					'<td>'+
					'<input name="field_name[]" class="input-text radius">'+
					'</td>'+
					'<td>'+
					'<input name="field_value[]" class="input-text radius">'+
					'</td>'+
					'<td class="text-c">'+
					'<i class="Hui-iconfont c-red row_operate J_del_row">&#xe631;</i>'+
					'<i class="Hui-iconfont c-green row_operate J_up_row">&#xe699;</i>'+
					'<i class="Hui-iconfont c-primary row_operate J_down_row">&#xe698;</i>'+
					'</td>'+
					'</tr>';
				$('tbody').append(row_html);
			});
			
			@if (!$detail['id']) $('.J_add_row').click(); @endif
			
			// 移除
			$(document).on('click', '.J_del_row', function () {
				$(this).parents('tr').remove();
			});
			
			// 上移
			$(document).on('click', '.J_up_row', function () {
				var row = $(this).parents('tr');
				row.prev().before(row);
			});
			
			// 下移
			$(document).on('click', '.J_down_row', function () {
				var row = $(this).parents('tr');
				row.next().after(row);
			});
			
			$("#form").validate({
				rules         : {
					"name"              : {
						required  : true,
						maxlength : 16
					},
					"field_name_text[]" : {required : true},
					"field_name[]"      : {required : true},
				},
				onkeyup       : false,
//				focusCleanup  : true,
				success       : "valid",
				submitHandler : function (form) {handleAjaxForm(form)}
			});
		});
	</script>
@endsection