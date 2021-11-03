@extends('layouts.app')

@section('content')

<div class="home-login-block">
<div class="row">
    <div class="col col-lg-8">
    <div class="home-page-title">
        {{-- <h1>The Inter-Bank Derivatives<br>Negotiation Platform</h1> --}}
    </div>
    </div>
    @if (Auth::guest())
    <div class="col col-lg-4">
    <div class="home-login float-md-right">
        <form id="homePageLoginForm" method="POST" action="{{ route('login') }}">
             {{ csrf_field() }}
                <div class="w-100 mb-3">
                    <div class="in-line-input email-input">
                        
                        <input id="email" type="email" class="form-control pb-0 {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" placeholder="Email address" required autofocus>
                        
                        @if ($errors->has('email'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('email') }}</strong>
                            </div>
                        @endif
                    </div>

                </div>

                <div class="w-100 mb-3">
                    <div class="in-line-input pass-input">
                        
                        <input id="password" type="password" class="form-control pb-0 {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="Password" required autocomplete="off">

                        @if ($errors->has('password'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('password') }}</strong>
                            </div> 
                        @endif
                    </div>

                </div>

                @if(Session::has('include_recaptcha') && Session::get('include_recaptcha'))
                    <div class="w-100 mb-3">
                        <div class="in-line-input {{ $errors->has('g-recaptcha-response') ? ' is-invalid' : '' }}">
                            {!! ReCaptcha::htmlFormSnippet() !!}
                            
                        </div>
                        @if($errors->has('g-recaptcha-response'))
                            <div class="text-danger">
                                <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                            </div> 
                        @endif
                    </div>
                @endif

                <div class="w-100 mb-3">
                    <button type="submit" class="btn mm-login-button w-100 pb-1 pt-1">
                        {{ __('Login') }}
                    </button>
                </div>
                <div class="m-0">
                    <a class="btn btn-link m-0 p-0" href="{{ route('password.request') }}">
                        {{ __('Forgot Password') }}
                    </a>
                </div>
        </form>
    </div>
    </div>
    @endif
</div>
</div>

@endsection
