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
				E-Mail Settings
			</h2>
			@endslot
	
				@slot('body')
					<div class="row">
						<div class="col-md-8 offset-md-2">
								<email-settings dusk="email-settings-component" :profile-complete="{{ $profileIsComplete ? "true" : "false" }}" :default-labels="'{{ $defaultLabels }}'" :email-settings="'{{ $emails }}'" ></email-settings>
						</div>
					</div>
				@endslot
		@endcomponent
		</div>
	</div>
		
</div>
@endsection