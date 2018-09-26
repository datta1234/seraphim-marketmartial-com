@extends('layouts.canvas_app')

@section('content')
	<div class="container">
		@include('partials.stats_navigation')
		
		<activity-year-tables :is_my_activity="false" :years="{{ json_encode($years) }}"></activity-year-tables>
	</div>
@endsection