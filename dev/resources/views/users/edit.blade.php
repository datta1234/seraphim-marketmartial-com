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
				My Profile
			</h2>
			@endslot
				@slot('body')

	<div class="row">
		<div class="col-md-10 offset-md-1">
			@if($is_admin_update)
				{!! Form::model($user,['route' => ['admin.user.profile.update', $user->id]]) !!}
			@else
            	{!! Form::model($user,['route' => 'user.update']) !!}
			@endif

			         <div class="form-group row">
			                    {{ Form::label('full_name','Full Name', ['class' => 'col-sm-4 col-form-label']) }}
			                <div class="col-sm-8">
			                  {{ Form::text('full_name',null,['class' => ($errors->has('full_name') ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'Enter your full name here...']) }}


			                    @if ($errors->has('full_name'))
			                        <span class="invalid-feedback">
			                            <strong>{{ $errors->first('full_name') }}</strong>
			                        </span>
			                    @endif
			                </div>
			            </div>

			              <div class="form-group row">
			                    {{ Form::label('cell_phone','Phone Number', ['class' => 'col-sm-4 col-form-label']) }}
			                <div class="col-sm-8">
			                  {{ Form::text('cell_phone',null,['class' => ($errors->has('cell_phone') ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'Enter your Cell Phone here here...']) }}


			                    @if ($errors->has('cell_phone'))
			                        <span class="invalid-feedback">
			                            <strong>{{ $errors->first('cell_phone') }}</strong>
			                        </span>
			                    @endif
			                </div>
			            </div>

			             <div class="form-group row">
			                    {{ Form::label('work_phone','Alternative Phone Number', ['class' => 'col-sm-4 col-form-label']) }}
			                <div class="col-sm-8">
			                  {{ Form::text('work_phone',null,['class' => ($errors->has('work_phone') ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'Enter your Work Phone here here...']) }}


			                    @if ($errors->has('work_phone'))
			                        <span class="invalid-feedback">
			                            <strong>{{ $errors->first('work_phone') }}</strong>
			                        </span>
			                    @endif
			                </div>
			            </div>

			              <div class="form-group row">
			                    {{ Form::label('email','E - Mail', ['class' => 'col-sm-4 col-form-label']) }}
			                <div class="col-sm-8">
			                  {{ Form::text('email',null,['readonly'=>true,'class' => 'form-control']) }}
			                </div>
			            </div>

				<div class="form-group row">
				        {{ Form::label('organisation_id','Organisation', ['class' => 'col-sm-4 col-form-label']) }}
				    <div class="col-sm-8">
		    			{{ Form::text('organisation',$user->organisation->title,['readonly'=>true,'class' => 'form-control']) }}
				    </div>
				</div>


            
				<div class="form-group row mb-0">
					<div class="col col-sm-12 col-md-6 offset-md-6 col-lg-3 offset-lg-9">
						{{ Form::submit($profileIsComplete?'Update':'Next',['class'=>'btn mm-button w-100 float-right']) }}
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