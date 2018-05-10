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
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        <form class="form-horizontal" method="POST" action="{{ route('password.email') }}">
            {{ csrf_field() }}
            
            <div class="page-form mx-auto">
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required placeholder="Enter your email here...">

                        @if ($errors->has('email'))
                            <div class="alert alert-danger">
                                <strong>{{ $errors->first('email') }}</strong>
                            </div>
                        @endif
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col col-sm-12 col-md-6 offset-md-6 col-lg-4 offset-lg-8 mt-5">
                        <button type="submit" class="btn mm-button w-100">
                            Send Password Reset Link
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @endslot
@endcomponent
<div class="email-reset-clear"></div>

@endsection
