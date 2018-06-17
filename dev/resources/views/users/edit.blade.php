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
				My profile
			</h2>
			@endslot
				@slot('body')

	<div class="row">
		<div class="col-md-8 offset-md-2">
            {!! Form::model($user,['route' => 'user.update']) !!}

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
			                    {{ Form::label('cell_phone','Cell Phone', ['class' => 'col-sm-4 col-form-label']) }}
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
			                    {{ Form::label('work_phone','Work Phone', ['class' => 'col-sm-4 col-form-label']) }}
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
			                  {{ Form::text('email',null,['class' => ($errors->has('email') ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'Enter your E - Mail here here...']) }}


			                    @if ($errors->has('email'))
			                        <span class="invalid-feedback">
			                            <strong>{{ $errors->first('email') }}</strong>
			                        </span>
			                    @endif
			                </div>
			            </div>
				@if(!$profileIsComplete)

              <div class="form-group row">
                <label for="organisation_id" class="col-sm-4 col-form-label">Your Organisation</label>


                <div class="col-sm-8">

                 
	                
					<toggle-input :show-if="true" name="not_listed" label="My Organisation is not listed" :checked="{{ old('not_listed') ? 'true' : 'false'}}">
						<div slot="standard">
						   <div class="form-group">

		                      {{ Form::select('organisation_id', [''=>'Select Organisation'] + $organisations, null,['class' => ($errors->has('organisation_id') ? 'form-control is-invalid' : 'form-control'),'id'=>'organisation_id'] ) }}

		                        @if ($errors->has('organisation_id'))
		                            <span class="invalid-feedback">
		                                <strong>{{ $errors->first('organisation_id') }}</strong>
		                            </span>
		                        @endif
		                    </div>
						</div>
						<div slot="alt">
							{{ Form::text('new_organisation',null,['class' => ($errors->has('new_organisation') ? 'form-control is-invalid' : 'form-control'),'id'=>'new_organisation','placeholder'=>'Enter your organisation here...']) }}
		                    
		                    @if ($errors->has('new_organisation'))
		                        <span class="invalid-feedback">
		                            <strong>{{ $errors->first('new_organisation') }}</strong>
		                        </span>
		                    @endif
						</div>
					
					</toggle-input>


                 {{ Form::hidden('new_organisation',null,['class' => ($errors->has('new_organisation') ? 'form-control is-invalid' : 'form-control'),'id'=>'new_organisation','placeholder'=>'Enter your organisation here...','disabled'=>true]) }}
                    
                    @if ($errors->has('new_organisation'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('new_organisation') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
			@else

				<div class="form-group row">
				        {{ Form::label('organisation_id','Organisation', ['class' => 'col-sm-4 col-form-label']) }}
				    <div class="col-sm-8">
				      {{ Form::select('organisation_id', [''=>'Select Organisation'] + $organisations, null,['disabled'=>true,'class'=>'form-control','id'=>'organisation_id'] ) }}
				    </div>
				</div>
              
			@endif

            
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