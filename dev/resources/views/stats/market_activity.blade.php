@extends('layouts.canvas_app')

@section('content')
	<div class="container">
		@include('partials.stats_navigation')
		<all-market-activity :years="{{ json_encode($years) }}"></all-market-activity>
	</div>
@endsection