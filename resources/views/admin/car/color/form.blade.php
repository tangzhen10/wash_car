@extends('admin.structure_form')
@section('js')
	<script>
		var preview = '<span class="J_image_preview" style="padding: 5px 0 0 15%;display: block;width: 80px;height: 80px;background: #{{$detail['color_code']}};box-shadow: #ccc 1px 1px 5px;margin : 0 5px"></span>';
		$('input[name="color_code"]').after(preview);
		$('input[name="color_code"]').change(function () {
			var color_code = $(this).val().trim();
			if (color_code.length == 6) {
				$('input[name="color_code"]').after(preview);
			}
		});
	</script>
@endsection