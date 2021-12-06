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
					<p>
		                After submitting this information, the Market Martial team will verify your profile.
		            </p>
		            <p>
		                You can view your account details at any time.
		            </p>
					<terms-and-conditions :tc-accepted="{{ $user->tc_accepted ? "true" : "false" }}" 
						privacy-policy-link="{{ action('PDFController@privacyPolicy') }}" 
						terms-of-use-link="{{ action('PDFController@termsAndConditions') }}"></terms-and-conditions>
				@endslot

		@endcomponent
		</div>
	</div>
		
</div>
@endsection