@extends('layouts.canvas_app')

@section('content')
	<div class="container">
		{{-- Rebates Card --}}
		@component('partials.content_card')
		    @slot('header')
		        <h2 class="mt-2 mb-2">Rebates Earned</h2>
		    @endslot
		    @slot('title')
		    @endslot
		    @slot('decorator')
		    @endslot
		    @slot('body')
		        
		    @endslot
		@endcomponent
		
		YEARS HERE
	</div>
@endsection