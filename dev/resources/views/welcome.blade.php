@extends('layouts.app')

@section('content')
<div class="row justify-content-md-end">
    <div class="col col-md-12">
        <p class="float-right active-markets">
            Active Market Makers Online: <strong>7</strong>
        </p>
    </div>
</div>

<div class="home-login-block">
    <div class="home-page-title">
        <h1>The Inter-Bank Derivatives<br>Trading Platform</h1>
    </div>
    @if (Auth::guest())
    <div class="home-login float-right">
        <form method="POST" action="{{ route('login') }}">
             {{ csrf_field() }}

                <div class="w-100 mb-3">
                    <div class="in-line-input email-input">
                        
                        <input id="email" type="email" class="form-control pb-0 {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" placeholder="Email address" required autofocus>
                    </div>

                    @if ($errors->has('email'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="w-100 mb-3">
                    <div class="in-line-input pass-input">
                        
                        <input id="password" type="password" class="form-control pb-0 {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="Password" required>
                    </div>

                    @if ($errors->has('password'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>

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
    @endif
</div>

<!-- <div class="three-d">
    <div class="card-body">
            <h2>
                <span class="icon icon-screen"></span>
            </h2>
            <h5 class="card-title text-center">
                All markets on one screen
            </h5>
        </div>
    <div aria-hidden="true" class="card text-center three-d-box">
        <div class="card-body front">
            <h2>
                <span class="icon icon-screen"></span>
            </h2>
            <h5 class="card-title text-center">
                All markets on one screen
            </h5>
        </div>
        <div class="card-body back">
            <h5 class="card-title text-center">
                Personalise the trading screen to see all markets that are relevant to you
            </h5>
        </div>
    </div>
</div> -->

<div class="info-block">
    <div class="card-group pull-up-card">
        <div class="card text-center" data-slide-block>
            <div class="card-screen">
          <div class="card-body" data-slide-title>
            <h2><span class="icon icon-screen"></span></h2>
          
            <h5 class="card-title text-center">
                All markets on one screen
            </h5>
          </div>
          <div class="card-body" data-slide-content >
            <h5 class="card-title text-center pt-3">
                Personalise the trading screen to see all markets that are relevant to you
            </h5>
          </div>

            

          </div>
        </div>

        <div class="card text-center" data-slide-block>
            <div class="card-award">
          <div class="card-body" data-slide-title>
            <h2><span class="icon icon-award2"></span></h2>

            <h5 class="card-title text-center">
                Get rewarded for market making
            </h5>
          </div>
          <div class="card-body" data-slide-content>
            <h5 class="card-title text-center">
                To improve liquidity, initial market makers are incentivised by receiving rebates, irrespective of whether the market maker trades
            </h5>
          </div>
          </div>
        </div>

        <div class="card text-center" data-slide-block>
            <div class="card-graph">
          <div class="card-body" data-slide-title>
            <h2><span class="icon icon-graph"></span></h2>

            <h5 class="card-title text-center">
                Personalised trading stats
            </h5>
          </div>
          <div class="card-body pt-2" data-slide-content>
            <h5 class="card-title text-center">
                Sort, filter and view both incomplete (doubles) and completed market trades as well as individual activity stats: trades, markets made, rebates, market share…
            </h5>
          </div>
          </div>
        </div>

        <div class="card text-center" data-slide-block>
            <div class="card-robot">
          <div class="card-body" data-slide-title>
            <h2><span class="icon icon-robot"></span></h2>

            <h5 class="card-title text-center">
                Automated bookings
            </h5>
          </div>
          <div class="card-body pt-3" data-slide-content>
            <h5 class="card-title text-center">
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
        <p class="card-text text-center">
            Market Martial has been custom built exclusively for inter-bank derivatives traders with the intention of providing a seamless transition to an efficient electronic platform.
        </p>
        <p class="card-text text-center">
            Market Martial moves the focus away from the broker and places the market maker back at centre stage. Instead of removing the broker, Market Martial keeps the broker to maintain accountability, drive markets and provide specialist support where necessary, but lets the machine streamline a fair and objective market making process.
        </p>
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
        <img class="img-fluid pb-4 pr-4 pl-4" src="{{asset('img/liquidity.svg')}}">
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
                        <li>Instantaneous response times.</li>
                        <li>Automated execution, confirmation and booking.</li>
                        <li>Personalised Activity Stats.</li>
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
        <div class="text-center">
            <img class="img-fluid justify-content-center pb-4" src="{{asset('img/maintain_flow.svg')}}">
        </div>
    @endslot
@endcomponent

{{-- Bridge the Gap Card --}}
@component('partials.content_card')
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
        <p class="card-text text-center">
            International volatility traders negotiate trades by quoting premium, while South African volatility traders quote implied volatility. Market Martial simultaneously displays both, catering to respective traders’ market conventions. 
        </p>
    @endslot
@endcomponent

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
        <p class="card-text text-center">
            In order to maintain objective, client-centric and user-friendly functionality Market Martial not only accepts comments and critiques, we encourage them. Please let us know how we can further improve the platform and serve you better.
        </p>
    @endslot
@endcomponent

{{-- Sign Up Card --}}
@if (Auth::guest())
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
@endif

<div class="traders-quote-block mt-5">
    <div class="text-center d-flex justify-content-center">
        <h1 class="quote-start">“</h1>
        <h1 class="traders-quote">We're traders.<br>It needs to do what we want, without fail.</h1>
    </div>
    <div>
        <p class="quote-by text-center">- Francis James CEO</p>
    </div>
</div>

@endsection


@section('sub-footer')

<div class="home-sub-footer">
    <div class="container">
        <div class="row pt-3 pb-3">
            <div class="col col-lg-4 footer-sections">
                <h3>Send us a message</h3>
                <form action="{{ route('contact') }}" method="POST">
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
                        <input type="text" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" id="contactEmail" name="email" placeholder="Enter your email here...">
                        @if ($errors->has('email'))
                            <div class="alert alert-danger">
                                <strong>{{ $errors->first('email') }}</strong>
                            </div>
                        @endif
                    </div>
                    <div class="form-group mb-2">
                        <textarea class="form-control {{ $errors->has('message') ? ' is-invalid' : '' }}" name="message" value="{{ old('message') }}" rows="10" placeholder="Enter your message here..."></textarea>
                        @if ($errors->has('message'))
                            <div class="alert alert-danger">
                                <strong>{{ $errors->first('message') }}</strong>
                            </div>
                        @endif
                    </div>
                    <div class="form-group mb-2">
                        <button type="submit" class="btn mm-footer-button float-right">Submit message</button>
                    </div>
                </form>
            </div>
            <div class="col col-lg-4 footer-sections">
                <h3>About Market Martial</h3>
                <p>
                    With as many brokers as there are banks, the question has always stood: How does a broker create edge, or how does a broker provide a service that truly is superior to all the rest?
                </p>
                <p>
                    After 7 years of managing Investec Bank’s Index and Single Stock Options trading books, with the necessary experience and expertise gained in this very niche market, I believe Market Martial is the answer to that question, and more.
                </p>
            </div>
            <div class="col col-lg-4 benefits-block">
                <h3>Benefits of using Market Martial</h3>
                <div class="row">
                    <div class="col col-md-1">
                        <span class="icon icon-money2"></span>
                    </div>
                    <div class="col col col-md-11">
                        <p class="mb-1">get paid to make markets and trade!</p>
                    </div>

                    <div class="col col-md-1">
                        <span class="icon icon-globe"></span>
                    </div>
                    <div class="col col col-md-11">
                        <p class="mb-1">easy to view markets, from anywhere in the world.</p>
                    </div>

                    <div class="col col-md-1">
                        <span class="icon icon-graph"></span>
                    </div>
                    <div class="col col col-md-11">
                        <p class="mb-1">trade stats and market info at your fingertips.</p>
                    </div>

                    <div class="col col-md-1">
                        <span class="icon icon-man"></span>
                    </div>
                    <div class="col col col-md-11">
                        <p class="mb-1">become a game-changer.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection