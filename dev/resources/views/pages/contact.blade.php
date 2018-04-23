@extends('layouts.app')

@section('content')

{{-- Contact Card --}}
@card()
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
        <form action="{{ route('contact') }}" method="POST">
            @csrf
            <div class="form-group">
                <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') }}" id="name" name="name" placeholder="Enter your name here...">

                @if ($errors->has('name'))
                    <div class="alert alert-danger">
                        <strong>{{ $errors->first('name') }}</strong>
                    </div>
                @endif
            </div>
            <div class="form-group">
                <input type="text" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" id="email" name="email" placeholder="Enter your email here...">
                @if ($errors->has('email'))
                    <div class="alert alert-danger">
                        <strong>{{ $errors->first('email') }}</strong>
                    </div>
                @endif
            </div>
            <div class="form-group">
                <textarea class="form-control {{ $errors->has('message') ? ' is-invalid' : '' }}" name="message" value="{{ old('message') }}" rows="10" placeholder="Enter your message here..."></textarea>
                @if ($errors->has('message'))
                    <div class="alert alert-danger">
                        <strong>{{ $errors->first('message') }}</strong>
                    </div>
                @endif
            </div>
            <div class="form-group">
                <button type="submit" class="btn mm-button float-right w-25">Send</button>
            </div>
        </form>
    @endslot
@endcard

@endsection

