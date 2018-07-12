@extends('admin.base')
@section('body')
	<article class="cl pd-20">
		<form action="" method="post" class="form form-horizontal" id="form">
			<input type="hidden" name="id" value="{{$detail['id']}}" />
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>角色名称：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<input type="text" class="input-text" value="{{$detail['name']}}" placeholder="" id="roleName" name="name" datatype="*4-16" nullmsg="用户账户不能为空">
				</div>
			</div>
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3">备注：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<input type="text" class="input-text" value="{{$detail['description']}}" placeholder="" id="" name="description">
				</div>
			</div>
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3">权限：</label>
				<div class="formControls col-xs-8 col-sm-9">
					@foreach($permissions as $item1)
						<dl class="permission-list">
							<dt>
								<label>
									<input type="checkbox" value="{{$item1['id']}}"
									       name="permissions[]" @if ($item1['checked']) checked @endif />{{$item1['name']}}
								</label>
							</dt>
							<dd>
								@if (!empty($item1['sub']))
									@foreach($item1['sub'] as $item2)
										<dl class="cl permission-list2">
											<dt>
												<label class="">
													<input type="checkbox" value="{{$item2['id']}}"
													       name="permissions[]" @if ($item2['checked']) checked @endif />{{$item2['name']}}
												</label>
											</dt>
											<dd>
												@if (!empty($item2['sub']))
													@foreach($item2['sub'] as $item3)
														<label class="">
															<input type="checkbox" value="{{$item3['id']}}"
															       name="permissions[]" @if ($item3['checked']) checked @endif />{{$item3['name']}}
														</label>
													@endforeach
												@endif
											</dd>
										</dl>
									@endforeach
								@endif
							</dd>
						</dl>
					@endforeach
				</div>
			</div>
			<div class="row cl">
				<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
					<input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;提交&nbsp;&nbsp;">
				</div>
			</div>
		</form>
	</article>
@endsection
@section('js')
	<script>
		
		$(function () {
			$(".permission-list dt input:checkbox").click(function () {
				$(this).closest("dl").find("dd input:checkbox").prop("checked", $(this).prop("checked"));
			});
			$(".permission-list2 dd input:checkbox").click(function () {
				var l  = $(this).parent().parent().find("input:checked").length;
				var l2 = $(this).parents(".permission-list").find(".permission-list2 dd").find("input:checked").length;
				if ($(this).prop("checked")) {
					$(this).closest("dl").find("dt input:checkbox").prop("checked", true);
					$(this).parents(".permission-list").find("dt").first().find("input:checkbox").prop("checked", true);
				}
				else {
					if (l == 0) {
						$(this).closest("dl").find("dt input:checkbox").prop("checked", false);
					}
					if (l2 == 0) {
						$(this).parents(".permission-list").find("dt").first().find("input:checkbox").prop("checked", false);
					}
				}
			});
			
			$("#form").validate({
				rules         : {
					name : {
						required : true,
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