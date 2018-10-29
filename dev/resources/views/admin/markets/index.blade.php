@extends('layouts.canvas_app')

@section('content')
	<div class="container">
		{{-- Markets Management Card --}}
		@component('partials.content_card')
		    @slot('header')
		        <h2 class="mt-2 mb-2">Markets Management</h2>
		    @endslot
		    @slot('title')
		    @endslot
		    @slot('decorator')
		    @endslot
		    @slot('body')    
		    	{!! Form::open(['route' => 'admin.markets.update','method'=>'PUT']) !!}
			        @foreach($markets as $index => $market)
			        	@if($index !== 0)
							<div class="row mt-4">
			        	@else
			        		<div class="row">
			        	@endif
			        		<div class="col-md-12 text-center">
			        			<h5>{{$market->title}}</h5>
			        		</div>
			        	</div>
						<div class="form-group row">
		                    {{ Form::label("spot_price_ref[{$market->id}]","Spot Price", ['class' => 'col-sm-12 col-md-4 col-form-label text-md-right']) }}
			                <div class="col-sm-12 col-md-4">
			                  	{{ Form::text("spot_price_ref[{$market->id}]",$market->spot_price_ref,['class' => ($errors->has("spot_price_ref.".$market->id) ? 'form-control is-invalid' : 'form-control')]) }}
			                    @if ($errors->has("spot_price_ref.".$market->id))
			                        <span class="invalid-feedback">
			                            <strong>{{ $errors->first("spot_price_ref.".$market->id) }}</strong>
			                        </span>
			                    @endif
			                </div>
			            </div>
	  				@endforeach()
	  				<div class="form-group row mb-0">
						<div class="col-md-2 offset-md-10">
							{!! Form::submit('Save',['class'=>'btn mm-generic-trade-button float-right w-100']) !!}
						</div>
					</div>
  				{!! Form::close() !!}
		    @endslot
		@endcomponent
	</div>
@endsection