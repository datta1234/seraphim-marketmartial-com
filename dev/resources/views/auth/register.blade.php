@extends('layouts.app')

@section('content')

{{-- Register Card --}}
@component('partials.content_card')
    @slot('header')
        <h2 class="mt-1 mb-1"><span class="icon icon-addprofile"></span></h2>
    @endslot
    @slot('title')
    Sign up to join the Inter-Bank Derivatives Trading Platform
    @endslot
    @slot('decorator')
        <hr class="title-decorator">
    @endslot
    @slot('body')
        <div>
            <p class="card-text text-center">
            <strong>New Member Enquiry</strong><br>
            It's easy to become a member. Complete the details below, and the you will receive an emails to log in to complete your profile.<br> 
            Once you submit your profile, we'll get back to you asap with your verification to start using Market Martial.
            </p>
            <p class="card-text text-center"><em>"Fieldsmarked with * are required fields</em></p>

            <form method="POST" action="{{ route('register') }}">
                         {{ csrf_field() }}
                        
                        <div class="form-group row">
                            <label for="email" class="col-md-4 offset-1 col-form-label text-md-right">E-Mail Address*</label>

                            <div class="col-md-4">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter your email here...">

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-md-4 offset-1 col-form-label text-md-right">Full Name*</label>

                            <div class="col-md-4">
                                <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" required placeholder="Enter your full name here...">

                                @if ($errors->has('name'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        {{-- NEEDS CHANGE --}}
                        <div class="form-group row">
                            <label for="work_phone" class="col-md-4 offset-1 col-form-label text-md-right">Work Phone*</label>

                            <div class="col-md-4">
                                <input id="work_phone" type="text" class="form-control{{ $errors->has('work_phone') ? ' is-invalid' : '' }}" name="work_phone" value="{{ old('work_phone') }}" required placeholder="Enter your work phone number here...">

                                @if ($errors->has('work_phone'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('work_phone') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="cell_phone" class="col-md-4 offset-1 col-form-label text-md-right">Cell Phone</label>

                            <div class="col-md-4">
                                <input id="cell_phone" type="text" class="form-control{{ $errors->has('cell_phone') ? ' is-invalid' : '' }}" name="cell_phone" value="{{ old('cell_phone') }}" placeholder="Enter your cell phone number here...">

                                @if ($errors->has('cell_phone'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('cell_phone') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="role" class="col-md-4 offset-1 col-form-label text-md-right">Select Role*</label>

                            <div class="col-md-4">
                                <div class="form-group">
                                  <select value="{{ old('role') }}" class="form-control {{ $errors->has('role') ? ' is-invalid' : '' }}" id="role" required>
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
                            <label for="name" class="col-md-4 offset-1 col-form-label text-md-right">Markets that you will be trading*</label>

                            <div class="form-group col-md-4 mt-2">
                                <div class="checkbox">
                                    <label>
                                        <input name="index" type="checkbox" value="index"> Index Option
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="single_stock" type="checkbox" value="single_stock"> Single Stock Option
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="delta_one" type="checkbox" value="delta_one"> Delta One (EFPs, Rolls and EFP Switches)
                                    </label>
                                </div>


                                @if ($errors->has('name'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="role" class="col-md-4 offset-1 col-form-label text-md-right">Your Organisation*</label>

                            <div class="col-md-4">
                                <div class="form-group">
                                  <select value="{{ old('role') }}" class="form-control {{ $errors->has('role') ? ' is-invalid' : '' }}" id="organisation" required>
                                    <option value="trader">Bank 1</option>
                                    <option value="viewer">Bank 2</option>
                                    <option value="viewer">Bank 3</option>
                                    <option value="viewer">Bank 4</option>
                                  </select>
                                </div>

                                

                                @if ($errors->has('role'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('role') }}</strong>
                                    </span>
                                @endif
                                
                                <div class="checkbox">
                                    <label>
                                        <input name="not_listed" type="checkbox" value="not_listed" data-not-listed-check> My organisation is not listed
                                    </label>
                                </div>

                                <input type="hidden" id="new_organistation" type="text" class="form-control{{ $errors->has('new_organistation') ? ' is-invalid' : '' }}" name="new_organistation" value="{{ old('new_organistation') }}" placeholder="Enter your organisation here...">
                                
                                @if ($errors->has('new_organistation'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('new_organistation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        {{-- NEEDS CHANGE END --}}
                        <div class="form-group row">
                            <label for="password" class="col-md-4 offset-1 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-4">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required placeholder="Enter your password here...">

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 offset-1 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-4">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required placeholder="Repeat your password here...">
                            </div>
                        </div>
                        
                        <div class="form-group row mt-5">
                            <div class="col-md-4 offset-md-4">
                                <div class="row register-info-group">
                                    <div class="col col-md-1">
                                        <span class="icon icon-info"></span>
                                    </div>
                                    <div class="col col-md-11">
                                        <p class="mb-1">Why do I need to wait for verification?</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-12 ">
                                <a class="btn mm-button float-right ml-2" href="{{ url('/') }}">Cancel</a>
                                <button type="submit" class="btn mm-button float-right">Sign Me Up</button>
                            </div>
                        </div>
                    </form>
        </div>
    @endslot
@endcomponent

@endsection
