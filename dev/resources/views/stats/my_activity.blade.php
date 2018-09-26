@extends('layouts.canvas_app')

@section('content')
	<div class="container">
		@include('partials.stats_navigation')

		{{-- Monthly Activity Card --}}
		@component('partials.content_card')
		    @slot('header')
		        <h2 class="mt-2 mb-2">Monthly Activity</span></h2>
		    @endslot
		    @slot('title')
		    @endslot
		    @slot('decorator')
		    @endslot
		    @slot('body')
		        <monthly-activity :market_data="{{ json_encode($graph_data) }}"></monthly-activity>
		    @endslot
		@endcomponent
		
		<activity-year-tables :is_my_activity="true" :years="{{ json_encode($years) }}"></activity-year-tables>
	</div>
@endsection