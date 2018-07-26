@extends('admin.structure_form')
@section('js')
	<script>
		var preview = '<p>'+
			'<span class="form_filed_row">预览：</span>'+
			'<span class="J_preview ml-5 radius" style="position:relative;top:5px;display:inline-block;'+
			'width: 75%;height: 30px;background: #{{$detail['code']}};"></span>'+
			'</p>';
		$('input[name="code"]').after(preview);
		$('input[name="code"]').bind('input propertychange', function () {
			var code = $(this).val().trim();
			if (code.length == 6) {
				$('.J_preview').css('background', '#'+code);
			}
		});
	</script>
@endsection