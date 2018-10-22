@extends('layouts.canvas_app')

@section('content')
	<div class="container-fluid">
		{{-- Booked Trades Card --}}
		@component('partials.content_card')
		    @slot('header')
		        <h2 class="mt-2 mb-2">Booked Trades</h2>
		    @endslot
		    @slot('title')
		    @endslot
		    @slot('decorator')
		    @endslot
		    @slot('body')
		        <booked-trades-table booked_trade_data="{{ $booked_trades }}"></booked-trades-table>
		    @endslot
		@endcomponent
	</div>
@endsection