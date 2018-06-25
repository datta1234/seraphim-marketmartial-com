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
					Trading Account Settings
				</h2>
			@endslot
		
				@slot('body')

			<div class="row">
				<div class="col-md-8 offset-md-2">
					{!! Form::model($user,['route' => 'trade_settings.edit','method'=>'PUT']) !!}


							@foreach ($trading_accounts as $index => $trading_account)
								@include('trading_account.partials.trading_account', ['trading_account' => $trading_account])
							@endforeach

							<h2>
								Auto Emails
							</h2>
							
							<p>
								Trade confirmations will be sent to the following emails(to register a new email, go to the Email tab):
							</p>
								
							@include('emails.partials.select', ['emails' => $emails])

							
						<div class="form-group row mb-0">
							<div class="col-md-12">
								{{ Form::submit($profileIsComplete?'Update':'next',['class'=>'btn mm-button float-right']) }}
							</div>
						</div>


				    {!! Form::close() !!}
			</div>
		</div>
			

				@endslot
		@endcomponent
		</div>
	</div>
		
</div>
@endsection