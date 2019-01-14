@extends('layouts.canvas_app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-3">	
			@include('partials.user_navigation')
		</div>
		<div class="col-9">
			@component('partials.content_card')
			@slot('header')
				<h2 class="mt-1 mb-1">
						You're done. Thank you!
				</h2>
			@endslot
				@slot('body')
					<terms-and-conditions :tc-accepted="{{ $user->tc_accepted ? "true" : "false" }}"></terms-and-conditions>
				@endslot

		@endcomponent
		</div>
	</div>
		
</div>
@endsection