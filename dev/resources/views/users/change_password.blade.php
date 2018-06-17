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
				Change Password
			</h2>
			@endslot
	
			@slot('body')


		<div class="row">
			<div class="col-md-8 offset-md-2">
		    {!! Form::open(['route' => 'user.change_password','method'=>'PUT']) !!}


		<div class="form-group row">

				        {{ Form::label('old_password','Old Password', ['class' => 'col-sm-4 col-form-label']) }}
				    <div class="col-sm-8">
				      {{ Form::password('old_password',['class' => ($errors->has('old_password') ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'Old Password']) }}


				        @if ($errors->has('old_password'))
				            <span class="invalid-feedback">
				                <strong>{{ $errors->first('old_password') }}</strong>
				            </span>
				        @endif
				    </div>
				</div>


				<div class="form-group row">

				        {{ Form::label('password','Password', ['class' => 'col-sm-4 col-form-label']) }}
				    <div class="col-sm-8">
				      {{ Form::password('password',['class' => ($errors->has('password') ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'Must be at least 8 characters long']) }}


				        @if ($errors->has('password'))
				            <span class="invalid-feedback">
				                <strong>{{ $errors->first('password') }}</strong>
				            </span>
				        @endif
				    </div>
				</div>


				<div class="form-group row">
				      {{ Form::label('password_confirmation','Confirm Password', ['class' => 'col-sm-4 col-form-label']) }}

				    <div class="col-sm-8">
				     {{ Form::password('password_confirmation',['class' => 'form-control','placeholder'=>'Repeat your password here...']) }}
				    </div>
				</div>

				
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