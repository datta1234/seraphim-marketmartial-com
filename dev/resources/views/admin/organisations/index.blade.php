@extends('layouts.canvas_app')

@section('content')
	<div class="container-fluid">
		{{-- User Managment Card --}}
		@component('partials.content_card')
		    @slot('header')
		        <h2 class="mt-2 mb-2">Organisation Management</h2>
		    @endslot
		    @slot('title')
		    @endslot
		    @slot('decorator')
		    @endslot
		    @slot('body')
		        <organisations-table organisation_data="{{ $organisationData }}"></organisations-table>
		    @endslot
		@endcomponent
	</div>
@endsection