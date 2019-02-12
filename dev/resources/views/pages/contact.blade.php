@extends('layouts.app')

@section('content')

{{-- Contact Card --}}
@component('partials.content_card')
    @slot('header')
        <h2><span class="icon icon-man-circle"></span></h2>
    @endslot
    @slot('title')
        Contact Us
    @endslot
    @slot('decorator')
        <hr class="title-decorator">
    @endslot
    @slot('body')
        <p class="card-text text-center mb-0">
            <a href="mailto:TheMartial@MarketMartial.com">TheMartial@MarketMartial.com</a>
        </p>
        <p class="card-text text-center mb-5">
            +27 82 784 6004
        </p>
        <form id="ContactUsForm" action="{{ route('contact_send') }}" method="POST">
             {{ csrf_field() }}
            <div class="page-form mx-auto">
                <div class="form-group">
                    <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') }}" id="name" name="name" placeholder="Enter your name here...">

                    @if ($errors->has('name'))
                        <div class="alert alert-danger">
                            <strong>{{ $errors->first('name') }}</strong>
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <input type="text" class="form-control {{ $errors->has('contact_email') ? ' is-invalid' : '' }}" value="{{ old('contact_email') }}" id="contactEmail" name="contact_email" placeholder="Enter your email here...">
                    @if ($errors->has('contact_email'))
                        <div class="alert alert-danger">
                            <strong>{{ $errors->first('contact_email') }}</strong>
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <textarea class="form-control {{ $errors->has('contact_message') ? ' is-invalid' : '' }}" id="contact_message" name="contact_message" value="{{ old('contact_message') }}" rows="10" placeholder="Enter your message here..."></textarea>
                    @if ($errors->has('contact_message'))
                        <div class="alert alert-danger">
                            <strong>{{ $errors->first('contact_message') }}</strong>
                        </div>
                    @endif
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col col-sm-12 col-md-6 offset-md-6 col-lg-3 offset-lg-9 mt-5">
                        <button disabled type="submit" class="btn mm-button w-100">Send</button>
                    </div>
                </div>
            </div>
        </form>
    @endslot
@endcomponent
<!-- <div class="contact-us-clear"></div> -->

@endsection

