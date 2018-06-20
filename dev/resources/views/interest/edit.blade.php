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
					Tell Us More About Yourself
				@endslot
			@slot('decorator')
				<hr class="title-decorator">
			@endslot
				@slot('body')
					
            {!! Form::model($user,['route' => 'interest.update','method'=>'PUT']) !!}

				<div class="form-group row">

			        {{ Form::label('birthdate','Birthdate', ['class' => 'col-sm-12 col-md-4 col-form-label']) }}
				    <div class="col-sm-12 col-md-4">

					<datepicker input-class="{{ $errors->has('birthdate') ? 'form-control is-invalid' : 'form-control' }}" name="birthdate" placeholder="Birthdate" format="yyyy-MM-dd" value="{{ $user->birthdate }}"></datepicker>

				        @if ($errors->has('birthdate'))
				            <span class="text-danger">
				                <strong>{{ $errors->first('birthdate') }}</strong>
				            </span>
				        @endif
				    </div>
				</div>
				<div class="form-group row">
					<div class="col-md-4">
						{{ Form::label('email', 'Are you married?', ['class' => 'awesome']) }}
					<div class="form-check">
					 {{ Form::radio('is_married',1,null,['id'=>'is_married_yes']) }}
					  <label class="form-check-label" for="is_married_yes">
					   	Yes
					  </label>
					</div>
					<div class="form-check">
					 {{ Form::radio('is_married',0,null,['id'=>'is_married_no']) }}
					  <label class="form-check-label" for="is_married_no">
					    No
					  </label>
					</div>
					</div>
					<div class="col-md-4">
						{{ Form::label('has_children', 'Do you have children?', ['class' => 'awesome']) }}
					<div class="form-check">
					 {{ Form::radio('has_children',0,null,['id'=>'has_children_yes']) }}
					  <label class="form-check-label" for="has_children_yes">
					   	Yes
					  </label>
					</div>
					<div class="form-check">
					 {{ Form::radio('has_children',1,null,['id'=>'has_children_no']) }}
					  <label class="form-check-label" for="has_children_no">
					    No
					  </label>
					</div>
					</div>
				</div>
				
					@foreach($interests->chunk(2) as $chunk)
						<div class="form-group row">
							@foreach($chunk as $interest)
								<div class="col-md-6">
								        {{ Form::label('birthdate',$interest->title) }}

										<activate-input :active="{{ ($interest->pivot != null) ? 'true': 'false' }}" placeholder="{{ $interest->title }}" name="{{ 'interest['.$interest->id.'][value]' }}" value="{{ $interest->pivot ? $interest->pivot->value : null }}"></activate-input>
								</div>
							@endforeach
						</div>
					@endforeach

				<div class="form-group row">

				        {{ Form::label('hobbies','Hobbies', ['class' => 'col-sm-12 col-md-4 col-form-label']) }}
				    <div class="col-sm-12 col-md-4">
				      {{ Form::textArea('hobbies',null,['class' => ($errors->has('hobbies') ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'Hobbies']) }}


				        @if ($errors->has('hobbies'))
				            <span class="invalid-feedback">
				                <strong>{{ $errors->first('hobbies') }}</strong>
				            </span>
				        @endif
				    </div>
				</div>

				<div class="form-group row mb-0">
					<div class="col-md-12">
						{{ Form::submit($profileIsComplete?'Update':'next',['class'=>'btn mm-button float-right']) }}
					</div>
				</div>



	            {!! Form::close() !!}


				@endslot
		@endcomponent
		</div>
	</div>
		
</div>
@endsection