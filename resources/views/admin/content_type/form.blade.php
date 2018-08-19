@extends('admin.public_form')
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
			<li>name：基本属性<em>【{{$keyFields}}】</em>不可以使用</li>
			<li>备选值：单选框、复选框、下拉菜单的值，格式为【值1,值2,值3...】</li>
			<li>备选值：若类型不是单选框、复选框、下拉菜单，则作为提示语显示在输入框里</li>
			<li>类型：类型为时间时，备选值为【yyyy-MM-dd HH:mm:ss】表示时间格式，不填表示使用该值作为默认值</li>
		</ul>
		<form class="form form-horizontal" id="form">
			<input type="hidden" name="id" value="{{$detail['id']}}" />
			<div class="row cl">
				<strong class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>文档类型名称：</strong>
				<div class="formControls col-xs-8 col-sm-10">
					<input type="text" class="input-text radius" value="{{$detail['name']}}" name="name">
				</div>
			</div>
			<div class="row cl">
				<strong class="form-label col-xs-4 col-sm-2">
					<span class="c-red">*</span>{{trans('common.type')}}：
				</strong>
				<div class="formControls col-xs-8 col-sm-10">
					<span class="skin-minimal form_value">
						<span class="radio-box">
							<label><input type="radio" id="status-1" value="1" name="type" @if ($detail['type'] == 1) checked @endif > 产品分类</label>
						</span>
						<span class="radio-box">
							<label><input type="radio" id="status-0" value="2" name="type" @if ($detail['type'] == 2)  checked @endif> 产品</label>
						</span>
					</span>
					<span class="note">* 类型为产品的应由开发人员配置，业务人员请选择产品分类，并在结构里选择产品池，name填article_list</span>
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
					<span class="btn btn-primary radius J_submit">{{trans('common.submit')}}</span>
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
			
			@if (!$detail['id'])
				$('.J_add_row').click();
			if ($('input[name="type"]:checked').val() == '1') {
				$('tbody tr:first input[name="field_name_text[]"]').val('产品池');
				$('tbody tr:first select[name="field_type[]"]').val('articlepond');
				$('tbody tr:first input[name="field_name[]"]').val('article_list');
				$('tbody tr:first input[name="field_value[]"]').val('多篇文章用英文逗号隔开，如36,37,34,35，前台将按所填的文章id排序');
			}
			@endif
			
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
		});
		
		function validate_form() {
			
			var fields    = $('#form input[name="field_name[]"]'),
			    keyFields = '{{$keyFields}}'.split(',');
			for (var x in fields) {
				var field = fields.eq(x).val();
				if (field == '') {
					layer.tips('name不得为空', fields.eq(x));
					return false;
				}
				if ($.inArray(field, keyFields) > -1) {
					layer.tips('不得使用基本属性name', fields.eq(x));
					return false;
				}
			}
			return true;
		}
	</script>
@endsection