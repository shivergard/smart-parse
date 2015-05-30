@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Shivergard\SmartParse</div>
				<div class="panel-body">
					        <div class="col-md-8 col-md-offset-2">
								<table class="table">
								    <thead>
									@foreach($fields as $col)
										<th>{{trans('smart-parse.'.$col)}}</th>
									@endforeach
								    </thead>
								    <tbody id="item-list">
								    	@include('smart-parse::list' , array('list' => $tables))	      	
								    </tbody>
								  </table>
							</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection