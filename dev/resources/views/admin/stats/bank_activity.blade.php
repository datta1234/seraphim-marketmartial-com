@extends('layouts.canvas_app')

@section('favicon')
	<link rel="shortcut icon" href="{{ asset('favicon_alt.ico') }}">
@endsection

@section('content')
	<div class="container">
		@include('partials.stats_navigation')

		{{-- Monthly Activity Card --}}
		@component('partials.content_card')
		    @slot('header')
		        <h2 class="mt-2 mb-2">Bank Activity</h2>
		    @endslot
		    @slot('title')
		    @endslot
		    @slot('decorator')
		    @endslot
		    @slot('body')
		        <bank-activity :market_data="{{ json_encode($graph_data) }}"></bank-activity>
		    @endslot
		@endcomponent
		
		<activity-year-tables :is_bank_level="true" :is_my_activity="false" :years="{{ json_encode($years) }}"></activity-year-tables>
	</div>
@endsection