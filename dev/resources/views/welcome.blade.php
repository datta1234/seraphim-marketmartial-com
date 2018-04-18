@extends('layouts.app')

@section('content')

<div class="info-block">
    <div class="card-group pull-up-card">
        <div class="card text-center">
          <div class="card-body card-screen">
            <h1><span class="icon icon-screen"></span></h1>

            <h5 class="card-title text-center">
                All markets on one screen
            </h5>
          </div>
        </div>

        <div class="card text-center">
          <div class="card-body card-award">
            <h1><span class="icon icon-award2"></span></h1>

            <h5 class="card-title text-center">
                Get rewarded for market making
            </h5>
          </div>
        </div>

        <div class="card text-center">
          <div class="card-body card-graph">
            <h1><span class="icon icon-graph"></span></h1>

            <h5 class="card-title text-center">
                Personalised trading stats
            </h5>
          </div>
        </div>

        <div class="card text-center">
          <div class="card-body card-robot">
            <h1><span class="icon icon-robot"></span></h1>

            <h5 class="card-title text-center">
                Automated bookings
            </h5>
          </div>
        </div>
    </div>

    <div class="card text-center">
      <div class="card-body">
        <h1 class="info-card"><span class="icon icon-info"></span></h1>

        <h2 class="card-title text-center">
            What does Market Martial do?
        </h2>
        <p class="card-text">
            Market Martial has been custom built exclusively for inter-bank derivatives traders with the intention of providing a seamless transition to an efficient electronic platform.
        </p>
        <p class="card-text">
            Market Martial moves the focus away from the broker and places the market maker back at centre stage. Instead of removing the broker, Market Martial keeps the broker to maintain accountability, drive markets and provide specialist support where necessary, but lets the machine streamline a fair and objective market making process.
        </p>
      </div>
    </div>
</div>

{{-- Liquidity Card --}}
@card()
    @slot('header')
        <h1><span class="icon icon-drop"></span></h1>
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
        <h1><span class="icon icon-award"></span></h1>
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
        <h1><span class="icon icon-fluid"></span></h1>
    @endslot
    @slot('title')
        We maintain the feel and flow
    @endslot
    @slot('body')
        <img class="img-fluid" src="{{asset('img/maintain_flow.svg')}}">
    @endslot
@endcard

{{-- Sign Up Card --}}
@card()
    @slot('header')
        <h1><span class="icon icon-addprofile"></span></h1>
    @endslot
    @slot('title')
    @endslot
    @slot('body')
        <div class="text-center">
            <p class="card-text ">
            Derivatives trading is now seamless and fast. Request, make, trade and view markets with efficiency and ease. Click the sign up button to get more information.
            </p>
            <a class="btn mm-button w-25" href="{{ route('register') }}">Sign up now</a>
        </div>
    @endslot
@endcard

@endsection