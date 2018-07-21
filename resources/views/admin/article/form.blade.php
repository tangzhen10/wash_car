@extends('admin.base')
@section('css')
	<style>
		.form_filed {
			display: inline-block;
			text-align: right;
			width: 10%;
		}
		.form_value {
			display: inline-block;
			width: 35%;
		}
		.form_filed_row {
			display: inline-block;
			text-align: right;
			width: 15%;
		}
		.form_value_row {
			display: inline-block;
			width: 75%;
		}
		.select-box {
			position: relative;
			top: 8px;
		}
		h3 {
			padding: 5px 0 15px 0;
		}
	</style>
@endsection
@section('body')
	<article class="cl pd-20">
		<form enctype="multipart/form-data" class="form form-horizontal" id="form">
			<input type="hidden" name="id" value="{{$detail['id']}}" />
			<div class="public_attr">
				<h3>公共属性</h3>
				<p>
					<span class="form_filed">{{trans('common.article_name')}}：</span>
					<input class="input-text radius form_value" value="{{$detail['name']}}" name="name">
					
					<span class="form_filed">副标题：</span>
					<input class="input-text radius form_value" value="{{$detail['sub_name']}}" name="sub_name">
				</p>
				<p>
					<span class="form_filed">{{trans('common.start_time')}}：</span>
					<input class="input-text radius form_value Wdate" value="{{$detail['start_time']}}" name="start_time"
					       onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'#F{$dp.$D(\'datemax\')}',skin:'whyGreen'})" id="datemin">
					
					<span class="form_filed">{{trans('common.end_time')}}：</span>
					<input class="input-text radius form_value Wdate" value="{{$detail['end_time']}}" name="end_time"
					       onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'datemin\')}',skin:'whyGreen'})" id="datemax">
				</p>
				<p>
					<span class="form_filed">{{trans('common.status')}}：</span>
					<span class="skin-minimal form_value">
						<span class="radio-box">
							<label><input type="radio" id="status-1" value="1" name="status" @if ($detail['status'] == '1') checked @endif> {{trans('common.enable')}}</label>
						</span>
						<span class="radio-box">
							<label><input type="radio" id="status-0" value="0" name="status" @if ($detail['status'] == '0') checked @endif> {{trans('common.disable')}}</label>
						</span>
					</span>
					
					<span class="form_filed fv_up"><span class="c-red">*</span>{{trans('common.content_type')}}：</span>
					<select class="select-box radius mb-20 form_value J_content_type" name="content_type">
						<option></option>
						@foreach($typeList as $type)
							<option value="{{$type['id']}}">{{$type['name']}}</option>
						@endforeach
					</select>
				</p>
			</div>
			
			<div class="private_attr">
				<h3>私有属性</h3>
				
				<div id="J_private_attr_area"></div>
			</div>
			<input class="btn btn-success radius J_submit" value="{{trans('common.save')}}">
		</form>
	</article>
@endsection
@section('js')
	
	<!-- 配置文件 remind lxt 必须放在编辑器源码文件之前 -->
	<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/ueditor/1.4.3/ueditor.config.js')}}"></script>
	<!-- 编辑器源码文件 -->
	<script type="text/javascript" src="{{URL::asset('H-ui.admin.page/lib/ueditor/1.4.3/ueditor.all.js')}}"></script>
	<script>
		
		$(function () {
			
			// 切换文档类型时，自动展现对应的文档结构
			$('.J_content_type').change(function () {
				var content_type = $(this).val();
				if (!content_type) return false;
				$.ajax({
					url        : '{{route('contentTypeFormHtml')}}',
					data       : {
						content_type : content_type,
						article_id   : $('input[name="id"]').val(),
					},
					beforeSend : function () {layer.load(3)},
					success    : function (data) {
						layer.close(layer.load());
						$('#J_private_attr_area').html(data);
					}
				});
			});
			
			$('.J_content_type').val('{{$detail['content_type']}}').trigger('change');
			
		});
	</script>
@endsection