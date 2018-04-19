@extends('layouts.app')

@section('content')

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
            <h5 class="card-title text-center">
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
          <div class="card-body" data-slide-content>
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
          <div class="card-body" data-slide-content>
            <h5 class="card-title text-center">
                Upon acceptance of automated confirmations, trades are instantaneously booked to pre-selected trading accounts, removing delays and human error
            </h5>
          </div>
        </div>
        </div>
    </div>

    <div class="card text-center">
      <div class="card-body">
        <h2 class="info-card"><span class="icon icon-info"></span></h2>

        <h2 class="card-title text-center">
            What does Market Martial do?
        </h2>
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
@card()
    @slot('header')
        <h2><span class="icon icon-drop"></span></h2>
    @endslot
    @slot('title')
        We improve liquidity
    @endslot
    @slot('body')
        <img class="img-fluid" src="{{asset('img/liquidity.svg')}}">
    @endslot
@endcard

{{-- Electronic efficiency Card --}}
@card()
    @slot('header')
        <h2><span class="icon icon-award"></span></h2>
    @endslot
    @slot('title')
        Electronic efficiency
    @endslot
    @slot('body')
        <div class="d-flex justify-content-center">
            <ul>
                <li>All markets in one place, on one screen.</li>
                <li>No broker bias.</li>
                <li>Instantaneous response times.</li>
                <li>Automated execution, confirmation and booking.</li>
                <li>Personalised Activity Stats.</li>
                <li>Record of market trades, including market doubles (incomplete trades).</li>
                <li>Secure and anonymous trading.</li>
            </ul>
        </div>
    @endslot
@endcard

{{-- Feel and flow Card --}}
@card()
    @slot('header')
        <h2><span class="icon icon-fluid"></span></h2>
    @endslot
    @slot('title')
        We maintain the feel and flow
    @endslot
    @slot('body')
        <img class="img-fluid" src="{{asset('img/maintain_flow.svg')}}">
    @endslot
@endcard

{{-- Bridge the Gap Card --}}
@card()
    @slot('header')
        <h2><span class="icon icon-star-circle"></span></h2>
    @endslot
    @slot('title')
        We bridge the gap: implied volatility vs premium
    @endslot
    @slot('body')
        <p class="card-text text-center">
            International volatility traders negotiate trades by quoting premium, while South African volatility traders quote implied volatility. Market Martial simultaneously displays both, catering to respective traders’ market conventions. 
        </p>
    @endslot
@endcard

{{-- Priority Card --}}
@card()
    @slot('header')
        <h2><span class="icon icon-man-circle"></span></h2>
    @endslot
    @slot('title')
        You are our priority
    @endslot
    @slot('body')
        <p class="card-text text-center">
            In order to maintain objective, client-centric and user-friendly functionality Market Martial not only accepts comments and critiques, we encourage them. Please let us know how we can further improve the platform and serve you better.
        </p>
    @endslot
@endcard

{{-- Sign Up Card --}}
@card()
    @slot('header')
        <h2><span class="icon icon-addprofile"></span></h2>
    @endslot
    @slot('title')
    @endslot
    @slot('body')
        <div class="text-center">
            <p class="card-text text-center">
            Derivatives trading is now seamless and fast. Request, make, trade and view markets with efficiency and ease. Click the sign up button to get more information.
            </p>
            <a class="btn mm-button w-25" href="{{ route('register') }}">Sign up now</a>
        </div>
    @endslot
@endcard

@endsection