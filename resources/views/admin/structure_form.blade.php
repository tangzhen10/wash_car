@extends('admin.public_form')
@section('css')
	<link rel="stylesheet" type="text/css" href="{{URL::asset('css/form.css')}}" />
@endsection
@section('body')
	<article class="cl pd-20">
		<form enctype="multipart/form-data" class="form form-horizontal" id="form">
			@yield('extra')
			{!! $html !!}
			@yield('extra_bottom')
			<div class="row cl">
				<div class="mt-5 text-c">
					<span class="btn btn-primary radius J_submit">{{trans('common.submit')}}</span>
				</div>
			</div>
		</form>
	</article>
@endsection