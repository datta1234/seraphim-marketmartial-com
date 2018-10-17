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
		        <rebates-earned :users="{{ json_encode($users) }}"
		        				:authed_user="{{ json_encode(Auth::user()->full_name) }}"
		        				:market_data="{{ json_encode($date_grouped_rebates) }}"
		        				:yearly_total="{{ $total_rebates }}">
		        </rebates-earned>
		    @endslot
		@endcomponent
		
		YEARS HERE
	</div>
@endsection