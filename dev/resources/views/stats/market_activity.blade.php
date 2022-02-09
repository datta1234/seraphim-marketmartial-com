@extends('layouts.canvas_app')

@section('favicon')
	<link rel="shortcut icon" href="{{ asset('favicon_alt.ico') }}">
@endsection

@section('content')
	<div class="container">
		@include('partials.stats_navigation')
		<all-market-activity :years="{{ json_encode($years) }}"></all-market-activity>
	</div>
@endsection