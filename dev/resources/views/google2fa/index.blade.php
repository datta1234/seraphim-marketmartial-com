@extends('layouts.canvas_app')

@section('content')
	<div class="container">
		{{-- Rebates Card --}}
		@component('partials.content_card')
		    @slot('header')
		        <h2 class="mt-2 mb-2">MFA</h2>
		    @endslot
		    @slot('title')
		    @endslot
		    @slot('decorator')
		    @endslot
		    @slot('body')
                <div class="card-text">
                    <form class="form-horizontal" method="POST" action="{{ route('2fa') }}">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="one_time_password" class="col-md-4 control-label">One Time Password</label>

                            <div class="col-md-6">
                                <input id="one_time_password" type="number" class="form-control {{ $errors->any() ? 'is-invalid' : ''  }}" name="one_time_password" required autofocus>
                                @if ($errors->any())
                                    @foreach ($errors->all() as $error)
                                        <span class="invalid-feedback">
                                                <strong>{{ $error }}</strong>
                                        </span>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn  mm-button">
                                    Login
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endslot
        @endcomponent
    </div>
@endsection