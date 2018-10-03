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
				
				@if(!$user->tc_accepted)
            		{!! Form::model($user,['route' => 'tsandcs.update','method'=>'PUT']) !!}
					<p>
						After submitting this information, the Market Martial team will verify your profile.
					</p>
					<p>
						You can view your account details at any time.
					</p>

					<div class="form-check">
						{!!  Form::hidden('tc_accepted', '0') !!}
						{!!  Form::checkbox('tc_accepted',1,null,['id'=>'terms-of-use']) !!}
						<label class="form-check-label" for="terms-of-use">
							I accept the <a href="#">Privacy Policy</a> and <a href="#">Terms of use</a>
						</label>
					</div>
					
					@if ($errors->has('tc_accepted'))
					    <span class="text-danger">
					        <strong>{{ $errors->first('tc_accepted') }}</strong>
					    </span>
					@endif


					<div class="form-group row mb-0">
					    <div class="col-sm-12 col-md-3 offset-md-6 col-xl-2 offset-xl-8 mt-2">
					        <button type="submit" class="btn mm-button">Next</button>
					    </div>
					</div>

       				{!! Form::close() !!}
       			@else
					Please wait for your account to be verified.
       			@endif

				@endslot
		@endcomponent
		</div>
	</div>
		
</div>
@endsection