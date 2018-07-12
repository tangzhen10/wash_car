@extends('admin.base')
@section('css')
	<style>
		/*.public_attr {
			width: 49%;
			float: left;
		}
		.private_attr {
			width: 49%;
			float: right;
		}*/
		.form_filed {
			display: inline-block;
			text-align: right;
			width: 10%;
		}
		.form_value {
			display: inline-block;
			width: 35%;
		}
	</style>
@endsection
@section('body')
	<article class="cl pd-20">
		<form action="" method="post" class="form form-horizontal" id="form">
			<input type="hidden" name="id" value="{{$detail['id']}}" />
			<input class="btn btn-success radius" type="submit" value="&nbsp;&nbsp;保存&nbsp;&nbsp;">
			<div class="public_attr">
				<h3>公共属性</h3>
				<p>
					<span class="form_filed"><span class="c-red">*</span>标题：</span>
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
				</p>
			</div>
			
			<div class="private_attr">
				<h3>私有属性</h3>
				
				<span>文档类型：</span>
				<select class="select-box radius mb-20 J_content_type">
					<option></option>
					@foreach($typeList as $type)
						<option value="{{$type['id']}}">{{$type['name']}}</option>
					@endforeach
				</select>
				<div id="J_private_attr_area"></div>
			</div>
		</form>
	</article>
@endsection
@section('js')
	<script>
		
		$(function () {
			
			// 切换文档类型时，自动展现对应的文档结构
			$('.J_content_type').change(function () {
				var content_type = $(this).val();
				$.ajax({
					url        : '{{route('contentTypeFormHtml')}}',
					data       : {
						content_type : content_type
					},
					beforeSend : function () {layer.load(3)},
					success    : function (data) {
						layer.close(layer.load());
						$('.J_private_attr_area').html(data);
					}
				});
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