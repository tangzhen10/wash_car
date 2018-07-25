@extends('admin.structure_form')
@section('extra')
	<p>
		<span class="form_filed_row">{{trans('common.brand')}}：</span>
		<span class="form_value_row">{{$brand['name']}}</span>
	</p>
@endsection