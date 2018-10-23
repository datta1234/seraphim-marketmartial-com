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
		        <rebates-table rebate_data="{{ $rebates }}"></rebates-table>
		        <div class="row">
					<div class="col-2 offset-10 mt-2 mb-2">
						<download-csv button_text="Download Rebates CSV" end_point="/admin/rebates-csv" :is_rebate="true"></download-csv>
					</div>
				</div>
		    @endslot
		@endcomponent
	</div>
@endsection