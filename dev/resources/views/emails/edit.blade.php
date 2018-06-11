@extends('layouts.trade_app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-3">	
			@include('partials.user_navigation')
		</div>
		<div class="col-9">
			@component('partials.content_card')
			@slot('header')
			<h2 class="mt-1 mb-1"><span class="icon icon-addprofile"></span></h2>
			@endslot
				@slot('title')
					E-Mail Settings
				@endslot
			@slot('decorator')
				<hr class="title-decorator">
			@endslot
				@slot('body')

				<email-settings :default-labels-data="{{ $defaultLabels->toJson() }}" :email-settings-data="{{ $emails->toJson() }}" ></email-settings>

				@endslot
		@endcomponent
		</div>
	</div>
		
</div>
@endsection