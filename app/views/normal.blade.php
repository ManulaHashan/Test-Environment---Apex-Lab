@extends('template')

@section('title')
	Add Recepts
@stop
	
@section('content')
	<ul>	
		@if(empty($people))
			There is no people...
		@else
			There people named...
		@endif	
		@foreach ($people as $person)
			<li>{{ $person }}</li>
		@endforeach
	</ul>

	<ul>	
		@unless(empty($people))
			There people named...
		@endunless	
		@foreach ($people as $person)
			<li>{{ $person }}</li>
		@endforeach
	</ul>

@stop

