@extends('layouts.canvas_app')

@section('content')
	<div class="container">
		{{-- Rebates Card --}}
		@component('partials.content_card')
		    @slot('header')
		        <h2 class="mt-2 mb-2">Rebates Assigned</h2>
		    @endslot
		    @slot('title')
		    @endslot
		    @slot('decorator')
		    @endslot
		    @slot('body')
		        <rebates-assigned :market_data="{{ json_encode($graph_data, JSON_FORCE_OBJECT) }}"
		        				:yearly_total="{{ $total_rebates }}">
		        </rebates-assigned>
		    @endslot
		@endcomponent
		
		<rebates-year-tables :is_bank_level="true" :years="{{ json_encode($years) }}"></rebates-year-tables>
	</div>
@endsection