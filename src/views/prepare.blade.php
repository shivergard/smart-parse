@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
	{!! Form::open(array('id' => "contactForm" , 'url' => action('\Shivergard\SmartParse\SmartParseController@publishJob'))) !!}
	{!! Form::hidden('from', $from ) !!}
	{!! Form::hidden('target_tables', $target_tables) !!}
	@foreach($target_fields as $field)
		<div class="input-group">
			<span class="input-group-addon" id="basic-addon1">{{$field}}</span>
			{!! Form::select($field, $select , array('class' => 'form-control')) !!}
		</div>
	@endforeach
	{!! Form::submit('Create Job') !!}
	{!! Form::close() !!}
		</div>
	</div>
</div>
@endsection