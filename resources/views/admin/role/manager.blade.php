@extends('admin.base')
@section('body')
	<div class="text-c">
		<h2>{{$detail['name']}}</h2>
		<p>{{$detail['description']}}</p>
		
		<table class="table table-border table-bordered table-bg table-hover table-sort" style="width: 300px;margin: 0 auto;">
			@foreach($managers as $manager)
				<tr>
					<td>{{$manager}}</td>
				</tr>
			@endforeach
		</table>
	</div>
@endsection