@extends('layouts.app')

@section('content')

{{-- Login Card --}}
@component('partials.content_card')
    @slot('header')
        <h2><span class="icon icon-profile"></span></h2>
    @endslot
    @slot('title')
        Login
    @endslot
    @slot('decorator')
        <hr class="title-decorator">
    @endslot
    @slot('body')
        <form id="loginPageLoginForm" class="form-horizontal" method="POST" action="{{ route('login') }}">
            {{ csrf_field() }}
            <div class="page-form mx-auto">
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus placeholder="Email address">

                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <input id="password" type="password" class="form-control" name="password" required placeholder="Password">

                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <div class="checkbox largeCheckBox">
                        <label>
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group row mb-0">
                    <div class="col-sm-12 col-md-4 offset-md-5 col-xl-3 offset-xl-7 mt-2">
                        <a class="btn mm-button float-right ml-2 w-100" href="{{ route('password.request') }}">
                            Forgot Your Password?
                        </a>
                    </div>
                    <div class="col-sm-12 col-md-3 col-xl-2 mt-2">
                        <button type="submit" class="btn mm-button float-right w-100">Login</button>
                    </div>
                </div>
        </form>
    @endslot
@endcomponent
<div class="login-reset-clear"></div>
@endsection
