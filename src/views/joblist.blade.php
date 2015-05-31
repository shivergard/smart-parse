@extends('smart-parse::app')

@section('content')
<div class="container-fluid">

	<div class="row">
		<div class="col-md-8 col-md-offset-0">
			<table class="table">
			    <thead>
					<th>id</th>
					<th>name</th>
			    </thead>
			    <tbody id="item-list">
			    	@include('smart-parse::list' , array('list' => $list))	      	
			    </tbody>
			  </table>
		</div>
	</div>


</div>
@endsection