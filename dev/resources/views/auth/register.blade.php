@extends('layouts.app')

@section('content')

{{-- Register Card --}}
@component('partials.content_card')
    @slot('header')
        <h2 class="mt-1 mb-1"><span class="icon icon-addprofile"></span></h2>
    @endslot
    @slot('title')
    New Member Enquiry
    @endslot
    @slot('decorator')
        <hr class="title-decorator">
    @endslot
    @slot('body')
    
        <div>
            <p class="card-text text-center">
            Fill in your details below and we will email you a login to complete your profile.<br>
            Once your credentials have been verified you will be able to view and use the Market Martial trading platform.
            </p>

            <form method="POST" action="{{ route('register') }}">
                         {{ csrf_field() }}
                        
                        <div class="form-group row">
                            <label for="email" class="col-sm-12 col-md-4 offset-md-1 col-form-label text-md-right">E-Mail Address</label>

                            <div class="col-sm-12 col-md-4">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" autofocus placeholder="Enter your email here...">

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="full_name" class="col-sm-12 col-md-4 offset-md-1 col-form-label text-md-right">Full Name</label>

                            <div class="col-sm-12 col-md-4">
                                <input id="full_name" type="text" class="form-control{{ $errors->has('full_name') ? ' is-invalid' : '' }}" name="full_name" value="{{ old('full_name') }}" placeholder="Enter your full name here...">

                                @if ($errors->has('full_name'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('full_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="cell_phone" class="col-sm-12 col-md-4 offset-md-1 col-form-label text-md-right">Phone</label>

                            <div class="col-sm-12 col-md-4">
                                <input id="cell_phone" type="tel" class="form-control{{ $errors->has('cell_phone') ? ' is-invalid' : '' }}" name="cell_phone" value="{{ old('cell_phone') }}" placeholder="Enter your work cell_phone number here...">

                                @if ($errors->has('cell_phone'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('cell_phone') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="role" class="col-sm-12 col-md-4 offset-md-1 col-form-label text-md-right">Select Role</label>

                            <div class="col-sm-12 col-md-4">
                                <div class="form-group">
                                  <select name="role" value="{{ old('role') }}" class="form-control {{ $errors->has('role') ? ' is-invalid' : '' }}" id="role">
                                    <option value="trader">I am a Trader</option>
                                    <option value="viewer">I am a Viewer</option>
                                  </select>
                                </div>

                                @if ($errors->has('role'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('role') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="markets" class="col-sm-12 col-md-4 offset-md-1 col-form-label text-md-right">Markets that you will be trading</label>

                            <div class="form-group col-sm-12 col-md-4 mt-2">
                                <div class="form-control {{ $errors->has('markets') ? ' is-invalid' : '' }}" id="markets">
                                @foreach ($markets as $key=>$market)
                                    <div class="form-check checkbox largeCheckBox">
                                        <label lass="form-check-label">
                                            <input class="form-check-input" name="markets[]" type="checkbox" value="{{$key}}"> {{ $market }}
                                        </label>
                                    </div>
                                @endforeach
                                </div>

                                @if ($errors->has('markets'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('markets') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="organisation" class="col-sm-12 col-md-4 offset-md-1 col-form-label text-md-right">Your Organisation</label>

                            <div class="col-sm-12 col-md-4">
                                <div class="form-group">
                                  <select name="organisation" value="{{ old('organisation') }}" class="form-control {{ $errors->has('organisation') ? ' is-invalid' : '' }}" id="organisation">
                                    @foreach ($organisations as $key=>$organisation)
                                        <option value="{{$key}}">{{ $organisation }}</option>
                                    @endforeach
                                  </select>
                                </div>

                                

                                @if ($errors->has('organisation'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('organisation') }}</strong>
                                    </span>
                                @endif
                                
                                <div class="checkbox largeCheckBox">
                                    <label>
                                        <input class="largeCheckBox" name="not_listed" type="checkbox" value="not_listed" data-not-listed-check> My organisation is not listed
                                    </label>
                                </div>

                                <input type="hidden" id="new_organistation" type="text" class="form-control{{ $errors->has('new_organistation') ? ' is-invalid' : '' }}" name="new_organistation" value="{{ old('new_organistation') }}" placeholder="Enter your organisation here..." disabled>
                                
                                @if ($errors->has('new_organistation'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('new_organistation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        {{-- NEEDS CHANGE END --}}
                        <div class="form-group row">
                            <label for="password" class="col-sm-12 col-md-4 offset-md-1 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-sm-12 col-md-4">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="Must be at least 8 characters long">

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-sm-12 col-md-4 offset-md-1 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-sm-12 col-md-4">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Repeat your password here...">
                            </div>
                        </div>
                        
                        <div class="form-group row mt-5">
                            <div class="col col-sm-12 col-md-6 offset-md-3 col-lg-5 offset-lg-4 col-xl-4 offset-xl-4">
                                <div class="row register-info-group" data-toggle="tooltip" data-trigger="click" data-placement="top" title="Security is extremely important to us and our users. We have therefore added a verification process to ensure that each user is legitimate. We always strive to achieve a fast turn-around time, so you should expect to hear back from us shortly.">
                                    <div class="col-12 col-md-2 col-xl-1 text-center">
                                        <span class="icon icon-info"></span>
                                    </div>
                                    <div class="col-12 col-md-10 col-xl-11 register-info-text">
                                        <p class="mb-1">Why do I need to wait for verification?</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-sm-12 col-md-3 offset-md-6 col-xl-2 offset-xl-8 mt-2">
                                <button type="submit" class="btn mm-button float-right w-100">Sign Me Up</button>
                            </div>
                            <div class="col-sm-12 col-md-3 col-xl-2 mt-2">
                                <a class="btn mm-button float-right ml-2 w-100" href="{{ url('/') }}">Cancel</a>
                            </div>
                        </div>
                    </form>
        </div>
    @endslot
@endcomponent

@endsection
