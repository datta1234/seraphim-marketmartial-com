@extends('layouts.canvas_app')

@section('content')
	<div class="container">
		{{-- Brokerage Fees Management Card --}}
		@component('partials.content_card')
		    @slot('header')
		        <h2 class="mt-2 mb-2">Brokerage Fees Management</h2>
		    @endslot
		    @slot('title')
		    @endslot
		    @slot('decorator')
		    @endslot
		    @slot('body')    
		    	<brokerage-fee organisations="{{ $organisations }}"></brokerage-fee>
		    @endslot
		@endcomponent
	</div>
@endsection