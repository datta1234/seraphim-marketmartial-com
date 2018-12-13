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
			<h2 class="mt-1 mb-1">Tell Us More About Yourself</h2>
			@endslot
			
				@slot('body')
					
		<div class="row">
			<div class="col-md-8 offset-md-2">
			@if($is_admin_update)
				{!! Form::model($user,['route' => ['admin.user.interest.update', $user->id],'method'=>'PUT']) !!}
			@else
            	{!! Form::model($user,['route' => 'interest.update','method'=>'PUT']) !!}
			@endif

				<div class="form-group row">

			        {{ Form::label('birthdate','Birthdate', ['class' => 'col-sm-4 col-form-label']) }}
				    <div class="col-sm-8">
						<day-month-picker name="birthdate" value="{{ $user->birthdate }}" ></day-month-picker>
				        @if ($errors->has('birthdate'))
				            <span class="text-danger">
				                <strong>{{ $errors->first('birthdate') }}</strong>
				            </span>
				        @endif
				    </div>
				</div>
				<div class="form-group row">
					
					<div class="col-md-6">
						{{ Form::label('email', 'Are you married?') }}
						
						<div class="row">
							<div class="col-md-12">
									<div class="form-check form-check-inline">
						 {{ Form::radio('is_married',1,null,['id'=>'is_married_yes','class'=>'form-check-input']) }}
						  <label class="form-check-label" for="is_married_yes">
						   	Yes
						  </label>
						</div>
						<div class="form-check form-check-inline">
						 {{ Form::radio('is_married',0,null,['id'=>'is_married_no','class'=>'form-check-input']) }}
						  <label class="form-check-label" for="is_married_no">
						    No
						  </label>
						</div>
							</div>
						</div>
						@if ($errors->has("is_married"))
								<span class="text-danger">
									<strong>{{ $errors->first("is_married") }}</strong>
								</span>
							@endif
					</div>

					<div class="col-md-6">
						{{ Form::label('has_children', 'Do you have children?') }}

						<div class="row">
							<div class="col-md-12">
								<div class="form-check form-check-inline">
								 {{ Form::radio('has_children',0,null,['id'=>'has_children_yes','class'=>'form-check-input']) }}
								  <label class="form-check-label" for="has_children_yes">
								   	Yes
								  </label>
								</div>
								<div class="form-check form-check-inline">
								 {{ Form::radio('has_children',1,null,['id'=>'has_children_no','class'=>'form-check-input']) }}
								  <label class="form-check-label" for="has_children_no">
								    No
								  </label>
								</div>
							</div>
						</div>
							@if ($errors->has("has_children"))
								<span class="text-danger">
									<strong>{{ $errors->first("has_children") }}</strong>
								</span>
							@endif

					</div>


				</div>
					<?php $index = 0; ?>
					@foreach($interests->chunk(2) as $chunk)
						<div class="row">
							@foreach($chunk as $interest)
								<?php $index++; ?>
								<div class="col-md-6">
									<div class="form-group row">
										<div class="col-md-12">
											{{ Form::label($interest->id,$interest->title, ['class' => 'col-form-label']) }}
												<activate-input :active="{{ ($interest->pivot != null) || is_array(old('interest')) && array_key_exists($index,old('interest'))? 'true': 'false' }}" placeholder="Details..." name="{{ 'interest['.$interest->id.'][value]' }}" value="{{ $interest->pivot ? $interest->pivot->value : null }}">
												</activate-input>

												@if ($errors->has("interest.{$index}.value"))
													<span class="text-danger">
														<strong>{{ $errors->first("interest.{$index}.value") }}</strong>
													</span>
												@endif

										</div>   
									</div>
								</div>

							@endforeach
						</div>

					@endforeach

				<div class="form-group row">
				    <div class="col-sm-12">
			        	{{ Form::label('hobbies') }}
				      {{ Form::textArea('hobbies',null,['class' => ($errors->has('hobbies') ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'Hobbies']) }}


				        @if ($errors->has('hobbies'))
				            <span class="invalid-feedback">
				                <strong>{{ $errors->first('hobbies') }}</strong>
				            </span>
				        @endif
				    </div>
				</div>

				<div class="form-group row mb-0">
					<div class="col col-sm-12 col-md-6 offset-md-6 col-lg-3 offset-lg-9">
						{{ Form::submit($profileIsComplete?'Update':'Next',['class'=>'btn mm-button float-right w-100']) }}
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