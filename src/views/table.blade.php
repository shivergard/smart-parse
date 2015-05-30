@extends('app')

@section('content')
<div class="container-fluid">
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