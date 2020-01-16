@extends('layouts.canvas_app')

@section('content')
	<div class="container">
		{{-- Structure Brokerage Fees Management Card --}}
		@component('partials.content_card')
		    @slot('header')
		        <h2 class="mt-2 mb-2">TradeStructure Brokerage Fees Management</h2>
		    @endslot
		    @slot('title')
		    @endslot
		    @slot('decorator')
		    @endslot
		    @slot('body')    
		    	<trade-structure-brokerage-fee trade-structures="{{ $trade_structures }}"></trade-structure-brokerage-fee>
		    @endslot
		@endcomponent

		{{-- Organisation Brokerage Fees Management Card --}}
		@component('partials.content_card')
		    @slot('header')
		        <h2 class="mt-2 mb-2">Organisation Brokerage Fees Management</h2>
		    @endslot
		    @slot('title')
		    @endslot
		    @slot('decorator')
		    @endslot
		    @slot('body')    
		    	<organisation-brokerage-fee organisations="{{ $organisations }}"></organisation-brokerage-fee>
		    @endslot
		@endcomponent
	</div>
@endsection