@extends('admin.public_form')
@section('body')
	<article class="cl pd-20">
		<form class="form form-horizontal" id="form">
			<input type="hidden" name="id" value="{{$detail['id']}}" />
			<input type="hidden" id="J_level" value="{{$detail['level']}}" />
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>权限名称：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<input type="text" class="input-text" value="{{$detail['name']}}" placeholder="" id="name" name="name">
				</div>
			</div>
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>父节点：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<select class="select-box" name="pid">
						<option value="0" data-level="0" data-sort="0">Top</option>
						@foreach($permissions as $permission)
							<option style="font-weight: bold;" value="{{$permission['id']}}" data-sort="{{$permission['sort']}}"
							        data-level="{{$permission['level']}}" {{$permission['id'] == $detail['pid'] ? 'selected' : ''}}>
								{{$permission['name']}}
							</option>
							@if (!empty($permission['sub']))
								@foreach($permission['sub'] as $item)
									<option value="{{$item['id']}}" data-sort="{{$item['sort']}}"
									        data-level="{{$item['level']}}" {{$item['id'] == $detail['pid'] ? 'selected' : ''}}>
										@for($i = 1; $i < $item['level']; ++$i) &nbsp;&nbsp;&nbsp;&nbsp; @endfor
										{{$item['name']}}
									</option>
								@endforeach
							@endif
						@endforeach
					</select>
				</div>
			</div>
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3">路由：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<input type="text" class="input-text" value="{{$detail['route']}}" id="route" name="route">
				</div>
			</div>
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>排序：</label>
				<div class="formControls col-xs-8 col-sm-9">
					<input type="number" class="input-text" value="{{$detail['sort']}}" id="sort" name="sort">
				</div>
			</div>
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>状态：</label>
				<div class="formControls col-xs-8 col-sm-9 skin-minimal">
					<div class="radio-box">
						<input type="radio" id="status-1" value="1" name="status" @if ($detail['status'] == '1') checked @endif>
						<label for="status-1">启用</label>
					</div>
					<div class="radio-box">
						<input type="radio" id="status-0" value="0" name="status" @if ($detail['status'] == '0') checked @endif>
						<label for="status-0">停用</label>
					</div>
				</div>
			</div>
			<div class="row cl">
				<label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>显示：</label>
				<div class="formControls col-xs-8 col-sm-9 skin-minimal">
					<div class="radio-box">
						<input type="radio" id="show-1" value="1" name="show" @if ($detail['show'] == '1') checked @endif>
						<label for="show-1">显示</label>
					</div>
					<div class="radio-box">
						<input type="radio" id="show-0" value="0" name="show" @if ($detail['show'] == '0') checked @endif>
						<label for="show-0">不显示</label>
					</div>
				</div>
			</div>
			<div class="row cl">
				<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
					<span class="btn btn-primary radius J_submit">{{trans('common.submit')}}</span>
				</div>
			</div>
		</form>
	</article>
@endsection
@section('js')
	<script>
		
		function validate_form() {
			
			var level   = parseInt($('#J_level').val()),
			    p_level = parseInt($('select[name="pid"] option:selected').attr('data-level'));
			console.log(p_level);
			if (level > 0 && (level-p_level != 1)) {
				layer.msg('当前权限节点只能位于'+(level-1)+'级菜单下');
				return false;
			}
			
			return true;
		}
		
		$(function () {
			
			// 切换父目录，自动将父目录的sort值赋给该节点
			$('select[name="pid"]').change(function () {
				$('input[name="sort"]').val($('select[name="pid"] option:selected').attr('data-sort'));
			});
		});
	</script>
@endsection