@extends('app')

@section('content')
<div class="container-fluid">
	<div>
			<h3>Select Import Table</h3>
			{!! Form::open(array('id' => "contactForm" , 'url' => action('\Shivergard\SmartParse\SmartParseController@prepareJob'))) !!}
			{!! Form::hidden('from', $name ) !!}
			{!! Form::select('target_tables', $target_tables); !!}
			{!! Form::submit('Prepare') !!}
			{!! Form::close() !!}
	</div>


	<div class="row">
		<div class="col-md-8 col-md-offset-0">
			<table class="table">
			    <thead>
				@foreach($fields as $col)
					<th>{{substr($col , 0 , 6)}}</th>
				@endforeach
			    </thead>
			    <tbody id="item-list">
			    	@include('smart-parse::list' , array('list' => $list))	      	
			    </tbody>
			  </table>
		</div>
	</div>


</div>
@endsection