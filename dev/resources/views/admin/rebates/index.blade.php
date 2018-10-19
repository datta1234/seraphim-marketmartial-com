@extends('layouts.canvas_app')

@section('content')
	<div class="container-fluid">
		{{-- Rebates Management Card --}}
		@component('partials.content_card')
		    @slot('header')
		        <h2 class="mt-2 mb-2">Rebates Management</h2>
		    @endslot
		    @slot('title')
		    @endslot
		    @slot('decorator')
		    @endslot
		    @slot('body')
		        <rebates-table></rebates-table>
		    @endslot
		@endcomponent
	</div>
@endsection