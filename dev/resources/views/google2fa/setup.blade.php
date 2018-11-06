@extends('layouts.canvas_app')

@section('content')
	<div class="container">
		{{-- Rebates Card --}}
		@component('partials.content_card')
		    @slot('header')
		        <h2 class="mt-2 mb-2">MFA Setup</h2>
		    @endslot
		    @slot('title')
		    @endslot
		    @slot('decorator')
		    @endslot
		    @slot('body')
                <div class="card-text text-center">
                    <p>
                        Set up your two factor authentication by scanning the barcode below.
                        <br/>
                        Alternatively, you can use the code {{ $secret }}
                    </p>
                    <div>
                        <img src="{{ $QR_Image }}">
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-sm-12 col-md-3 offset-md-6 col-xl-2 offset-xl-8 mt-2">
                            <a href="{{ route('admin.mfa.finish_setup') }}">
                                <button class="btn mm-button float-right w-100">
                                    Continue
                                </button>
                            </a>
                        </div>
                        <div class="col-sm-12 col-md-3 col-xl-2 mt-2">
                            <a class="btn mm-button float-right ml-2 w-100" href="{{ route('admin.user.index') }}">Cancel</a>
                        </div>
                    </div>                    
                </div>
            @endslot
		@endcomponent                
    </div>
@endsection