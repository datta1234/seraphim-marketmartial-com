@extends('layouts.canvas_app')

@section('content')
	<div class="container">
		@include('partials.stats_navigation')
		{{-- Open Interest Card --}}
		@component('partials.content_card')
		    @slot('header')
		        <h2 class="mt-2 mb-2">GRAPH CARD TITLE</span></h2>
		    @endslot
		    @slot('title')
		    @endslot
		    @slot('decorator')
		    @endslot
		    @slot('body')
		        <open-interests :data="{{ json_encode($grouped_open_interests) }}"></open-interests>
		    @endslot
		@endcomponent
	</div>
@endsection