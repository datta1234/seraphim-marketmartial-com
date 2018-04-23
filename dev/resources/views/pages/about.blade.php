@extends('layouts.app')

@section('content')

<div class="page-title text-center m-5">
    <h1>About us</h1>
</div>

{{-- About content Card --}}
@card()
    @slot('header')
        <h2><span class="icon icon-globe"></span></h2>
    @endslot
    @slot('title')
        The Next Generation of Derivatives Trading
    @endslot
    @slot('decorator')
        <hr class="title-decorator">
    @endslot
    @slot('body')
        <p class="card-text text-center">
            With as many brokers as there are banks, the question has always stood: How does a broker create edge, or how does a broker provide a service that truly is superior to all the rest?
        </p>
        <p class="card-text text-center">
            After 7 years of managing Investec Bank’s Index and Single Stock Options trading books, with the necessary experience and expertise gained in this very niche market, I believe Market Martial is the answer to that question, and more.
        </p>
        <p class="card-text text-center">
            I can truthfully say that Market Martial has been custom built exclusively for derivatives traders, by a derivatives trader.
        </p>
        <p class="card-text text-center">
            The simple intention behind Market Martial has been to address all the inefficiencies I have experienced over the years by fast forwarding an old-fashioned market practice into the technical age, by utilising the numerous benefits of a state of the art electronic platform.
        </p>
        <p class="card-text text-center">
            Although the intention may have been simple, building a business around a platform that needs to cater to the many intricacies required to efficientize a relatively complex market has been anything but. I can only hope that our hard work and commitment has resulted in a product that you will find as useful as it is intended to be.
        </p>
        <p class="card-text text-center">
            With you, the derivatives traders, being the core focus of the Market Martial platform, I encourage you to provide me with your comments and critiques that could further improve your trading experience.   
        </p>
    @endslot
@endcard

{{-- Priority Card --}}
@card()
    @slot('header')
        <h2><span class="icon icon-question"></span></h2>
    @endslot
    @slot('title')
        Why Us?
    @endslot
    @slot('decorator')
        <hr class="title-decorator">
    @endslot
    @slot('body')
        <div class="row why-us-card justify-content-md-center">
            <div class="col-md-auto circle-icon-tile text-center">
                <div class="circle-icon-block m-3"><span class="icon icon-business p-4"></span></div>
                <p class="mt-3 pt-3">
                    Experienced<br>Management
                </p>
            </div>
            <div class="col-md-auto circle-icon-tile text-center">
            <div class="circle-icon-block m-3"><span class="icon icon-wifi2 p-4"></span></div>
                <p class="mt-3 pt-3">
                    Online and advanced<br>functionality
                </p>
            </div>
            <div class="col-md-auto circle-icon-tile text-center">
                <div class="circle-icon-block m-3"><span class="icon icon-money2 p-4"></span></div>
                <p class="mt-3 pt-3">
                    Low fees
                </p>
            </div>
            <div class="col-md-auto circle-icon-tile text-center">
                <div class="circle-icon-block m-3"><span class="icon icon-star p-4"></span></div>
                <p class="mt-3 pt-3">
                    Rewards for market<br>making
                </p>
            </div>
        </div>
    @endslot
@endcard

@endsection