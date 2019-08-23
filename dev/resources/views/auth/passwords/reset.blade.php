@extends('layouts.app')

@section('content')

{{-- Reset Password Card --}}
@component('partials.content_card')
    @slot('header')
        <h2><span class="icon icon-lock"></span></h2>
    @endslot
    @slot('title')
        Reset Password
    @endslot
    @slot('decorator')
        <hr class="title-decorator">
    @endslot
    @slot('body')
        <form class="form-horizontal" method="POST" action="{{ route('password.request') }}">
            {{ csrf_field() }}

            <input type="hidden" name="token" value="{{ $token }}">
            
            <div class="mx-auto">
                <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }} row">
                    <label for="email" class="col-md-3 offset-md-2 control-label">E-Mail Address</label>

                    <div class="col-md-5">
                        <input id="email" type="email" class="form-control" name="email" value="{{ $email or old('email') }}" required autofocus>

                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }} row">
                    <label for="password" class="col-md-3 offset-md-2 control-label">Password</label>

                    <div class="col-md-5">
                        <input id="password" type="password" class="form-control" name="password" required autocomplete="off">

                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group {{ $errors->has('password_confirmation') ? ' has-error' : '' }} row">
                    <label for="password-confirm" class="col-md-3 offset-md-2 control-label">Confirm Password</label>
                    <div class="col-md-5">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>

                        @if ($errors->has('password_confirmation'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <div class="row">
                    <div class="col col-sm-12 col-md-6 offset-md-6 col-lg-4 offset-lg-8 mt-5">
                        <button type="submit" class="btn mm-button w-100">
                            Reset Password
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @endslot
@endcomponent
@endsection
