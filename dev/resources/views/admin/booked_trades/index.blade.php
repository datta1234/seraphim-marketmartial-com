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
		        <div class="row">
					<div class="col-2 offset-10 mt-2 mb-2">
						<download-csv button_text="Download Booked Trades CSV" end_point="/admin/booked-trades-csv" :is_rebate="false"></download-csv>
					</div>
				</div>
		    @endslot
		@endcomponent
	</div>
@endsection