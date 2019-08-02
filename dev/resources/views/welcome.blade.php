@extends('layouts.app')

@section('content')

<div class="row justify-content-md-end">
    <div class="col col-md-12">
        <p class="float-right active-markets">
            Active Market Makers Online: <strong><active-makers></active-makers></strong>
        </p>
    </div>
</div>

<div class="home-login-block">
<div class="row">
    <div class="col col-lg-8">
    <div class="home-page-title">
        <h1>The Inter-Bank Derivatives<br>Negotiation Platform</h1>
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
                        
                        <input id="password" type="password" class="form-control pb-0 {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="Password" required>

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

<div class="info-block">
    <div class="card-group flip-card-group">
        <div class="flip-container card text-center" ontouchstart="this.classList.toggle('hover');">
            <div class="flipper">
                <div class="front card-screen pt-3">
                    <h2><span class="icon icon-screen"></span></h2>
                    <h5 class="front-title text-center">
                        All markets on one screen
                    </h5>
                </div>
                <div class="back card-screen">
                    <h5 class="back-title text-center pl-2 pr-2">
                        Personalise the trading screen to see all markets that are relevant to you
                    </h5>
                </div>
            </div>
        </div>
        <div class="flip-container card text-center" ontouchstart="this.classList.toggle('hover');">
            <div class="flipper">
                <div class="front card-award pt-3">
                    <h2><span class="icon icon-award2"></span></h2>
                    <h5 class="front-title text-center">
                        Get rewarded for market<br>making
                    </h5>
                </div>
                <div class="back card-award">
                    <h5 class="back-title text-center pl-2 pr-2">
                        To improve liquidity, initial market makers are incentivised by receiving rebates, irrespective of whether the market maker trades
                    </h5>
                </div>
            </div>
        </div>
        <div class="flip-container card text-center" ontouchstart="this.classList.toggle('hover');">
            <div class="flipper">
                <div class="front card-graph pt-3">
                    <h2><span class="icon icon-graph"></span></h2>
                    <h5 class="front-title text-center">
                        Personalised trading stats
                    </h5>
                </div>
                <div class="back card-graph">
                    <h5 class="back-title text-center pl-2 pr-2">
                        Sort, filter and view both incomplete (doubles) and completed market trades as well as individual activity stats: trades, markets made, rebates, market share…
                    </h5>
                </div>
            </div>
        </div>
        <div class="flip-container card text-center" ontouchstart="this.classList.toggle('hover');">
            <div class="flipper">
                <div class="front card-robot pt-3">
                    <h2><span class="icon icon-robot"></span></h2>
                    <h5 class="front-title text-center">
                        Automated bookings
                    </h5>
                </div>
                <div class="back card-robot">
                    <h5 class="back-title text-center pl-2 pr-2">
                        Upon acceptance of automated confirmations, trades are instantaneously booked to pre-selected trading accounts, removing delays and human error
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <div class="card text-center">
      <div class="card-body">
        <h2 class="info-card">
            <span class="icon icon-info"></span>
        </h2>

        <h2 class="card-title text-center">
            What does Market Martial do?
        </h2>
        <hr class="title-decorator mm-info">
        <div class="row justify-content-md-center">
            <div class="col col-lg-10">
                <p class="card-text text-center">
                    Market Martial has been custom built exclusively for inter-bank derivatives traders with the intention of providing a seamless transition to an efficient electronic platform.
                </p>
                <p class="card-text text-center">
                    Market Martial moves the focus away from the broker and places the market maker back at centre stage. Instead of removing the broker, Market Martial keeps the broker to maintain accountability, drive markets and provide specialist support where necessary, but lets the machine streamline a fair and objective market making process.
                </p>    
            </div>
        </div>
      </div>
    </div>
</div>

{{-- Liquidity Card --}}
@component('partials.content_card')
    @slot('header')
        <h2 class="mt-1 mb-1"><span class="icon icon-drop"></span></h2>
    @endslot
    @slot('title')
        We improve liquidity
    @endslot
    @slot('decorator')
        <hr class="title-decorator">
    @endslot
    @slot('body')
        <div class="row justify-content-md-center">
            <div class="col col-lg-9 col-md-12">
                <img class="liquidity-img img-fluid" src="{{asset('img/liquidity_v3.svg')}}">
            </div>
        </div>
    @endslot
@endcomponent

{{-- Electronic efficiency Card --}}
@component('partials.content_card')
    @slot('header')
        <h2 class="mt-1 mb-1"><span class="icon icon-award"></span></h2>
    @endslot
    @slot('title')
        Electronic efficiency
    @endslot
    @slot('decorator')
        <hr class="title-decorator">
    @endslot
    @slot('body')
            <div class="row">
                <div class="col-md-9 offset-md-3">
                    
                    <ul class="elec-eff-card-list">
                        <li>All markets in one place, on one screen.</li>
                        <li>No broker bias.</li>
                        <li>Automated execution, confirmation and booking.</li>
                        <li>Personalised activity stats.</li>
                        <li>Record of market trades, including market doubles (incomplete trades).</li>
                        <li>Secure and anonymous trading.</li>
                    </ul>
                </div>
            </div>
    @endslot
@endcomponent

{{-- Feel and flow Card --}}
@component('partials.content_card')
    @slot('header')
        <h2 class="mt-1 mb-1"><span class="icon icon-fluid"></span></h2>
    @endslot
    @slot('title')
        We maintain the feel and flow
    @endslot
    @slot('decorator')
        <hr class="title-decorator mm-info">
    @endslot
    @slot('body')   
        <div class="row justify-content-md-center">
            <div class="col col-lg-9 col-md-12">
                <img class="feelflow-img img-fluid" src="{{asset('img/maintain_flow_v3.svg')}}">
            </div>
        </div>
    @endslot
@endcomponent

{{-- Bridge the Gap Card --}}
{{--@component('partials.content_card')
    @slot('header')
        <h2 class="mt-1 mb-1"><span class="icon icon-star-circle"></span></h2>
    @endslot
    @slot('title')
        We bridge the gap: implied volatility vs premium
    @endslot
    @slot('decorator')
        <hr class="title-decorator mm-info">
    @endslot
    @slot('body')
        <div class="row justify-content-md-center">
            <div class="col col-lg-10">
                <p class="card-text text-center">
                    International volatility traders negotiate trades by quoting premium, while South African volatility traders quote implied volatility. Market Martial simultaneously displays both, catering to respective traders’ market conventions. 
                </p>
            </div>
        </div>
    @endslot
@endcomponent --}}

{{-- Priority Card --}}
@component('partials.content_card')
    @slot('header')
        <h2 class="mt-1 mb-1"><span class="icon icon-man-circle"></span></h2>
    @endslot
    @slot('title')
        You are our priority
    @endslot
    @slot('decorator')
        <hr class="title-decorator">
    @endslot
    @slot('body')
        <div class="row justify-content-md-center">
            <div class="col col-lg-10">
                <p class="card-text text-center">
                    In order to maintain objective, client-centric and user-friendly functionality Market Martial not only accepts comments and critiques, we encourage them. Please let us know how we can further improve the platform and serve you better.
                </p>
            </div>
        </div>
    @endslot
@endcomponent

<div class="row justify-content-md-center">
    <div class="signup-block col col-lg-4 col-md-6  col-sm-12">
        <a class="btn mm-login-button w-100" href="{{ route('register') }}">Sign up now</a>
    </div>
</div>
{{-- Sign Up Card --}}
{{--@if (Auth::guest())
    @component('partials.content_card')
        @slot('header')
            <h2 class="mt-1 mb-1"><span class="icon icon-addprofile"></span></h2>
        @endslot
        @slot('title')
        @endslot
        @slot('decorator')
        @endslot
        @slot('body')
            <div class="text-center">
                <p class="card-text text-center">
                Derivatives trading is now seamless and fast. Request, make, trade and view markets with efficiency and ease. Click the sign up button to get more information.
                </p>
                <a class="btn mm-button w-25" href="{{ route('register') }}">Sign up now</a>
            </div>
        @endslot
    @endcomponent
@endif --}}

<!-- <div class="traders-quote-block mt-5">
    <div class="text-center d-flex justify-content-center">
        <h1 class="quote-start">“</h1>
        <h1 class="traders-quote">We're traders.<br>It needs to do what we want, without fail.</h1>
    </div>
    <div>
        <p class="quote-by text-center">- Francis James CEO</p>
    </div>
</div> -->

@endsection


@section('sub-footer')

<div class="home-sub-footer">
    <div class="container">
        <div class="row">
            <div class="col col-12 col-md-6 col-lg-4 offset-lg-1 footer-sections pt-3 pb-3">
                <h3>Send us a message</h3>
                <form id="ContactUsForm" action="{{ route('contact_send') }}" method="POST">
                     {{ csrf_field() }}
                    <div class="form-group mb-2">
                        <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') }}" id="name" name="name" placeholder="Enter your name here...">

                        @if ($errors->has('name'))
                            <div class="alert alert-danger">
                                <strong>{{ $errors->first('name') }}</strong>
                            </div>
                        @endif
                    </div>
                    <div class="form-group mb-2">
                        <input type="text" class="form-control {{ $errors->has('contact_email') ? ' is-invalid' : '' }}" value="{{ old('contact_email') }}" id="contactEmail" name="contact_email" placeholder="Enter your email here...">
                        @if ($errors->has('contact_email'))
                            <div class="alert alert-danger">
                                <strong>{{ $errors->first('contact_email') }}</strong>
                            </div>
                        @endif
                    </div>
                    <div class="form-group mb-2">
                        <textarea class="form-control {{ $errors->has('contact_message') ? ' is-invalid' : '' }}" id="contactMessage" name="contact_message" rows="10" placeholder="Enter your message here...">{{ old('contact_message') }}</textarea>
                        @if ($errors->has('contact_message'))
                            <div class="alert alert-danger">
                                <strong>{{ $errors->first('contact_message') }}</strong>
                            </div>
                        @endif
                    </div>
                    <div class="form-group mb-2">
                        <button disabled type="submit" class="btn mm-footer-button float-right">Submit message</button>
                    </div>
                </form>
            </div>
            <!-- <div class="col col-lg-4 footer-sections">
                <h3>About Market Martial</h3>
                <p>
                    With as many brokers as there are banks, the question has always stood: How does a broker create edge, or how does a broker provide a service that truly is superior to all the rest?
                </p>
                <p>
                    After 7 years of managing Investec Bank’s Index and Single Stock Options trading books, with the necessary experience and expertise gained in this very niche market, I believe Market Martial is the answer to that question, and more.
                </p>
            </div> -->
            <div class="col col-12 col-md-6 col-lg-5 offset-lg-2 benefits-block pt-3 pb-3">
                <h3>Benefits of using Market Martial</h3>
                <div class="row">
                    <div class="col col-2 col-lg-1 col-md-2">
                        <span class="icon icon-globe"></span>
                    </div>
                    <div class="col col-10 col-lg-11 col-md-10">
                        <p class="mb-3">easy to view markets, from anywhere in the world.</p>
                    </div>

                    <div class="col col-2 col-lg-1 col-md-2">
                        <span class="icon icon-graph"></span>
                    </div>
                    <div class="col col-10 col-lg-11 col-md-10">
                        <p class="mb-3">trade stats and market info at your fingertips.</p>
                    </div>

                    <div class="col col-2 col-lg-1 col-md-2">
                        <span class="icon icon-man"></span>
                    </div>
                    <div class="col col-10 col-lg-11 col-md-10">
                        <p class="mb-3">become a game-changer.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection