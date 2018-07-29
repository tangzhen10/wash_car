@extends('admin.structure_form')
@section('extra')
	<p>
		<span class="form_filed_row">{{trans('common.brand')}}：</span>
		<select class="select-box radius" style="width: 75%;position: relative;top: 0px;" name="brand_id">
			<option></option>
			@foreach($brandList as $item)
				<option value="{{$item['id']}}" @if ($item['id'] == $detail['brand_id']) selected @endif>
					{{$item['name']}}
				</option>
			@endforeach
		</select>
	</p>
@endsection