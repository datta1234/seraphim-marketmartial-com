@extends('layouts.app')

@section('content')

<div class="page-title text-center m-5">
    <h1>About us</h1>
</div>

{{-- About content Card --}}
@component('partials.content_card')
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
        <div class="about-us-content pl-5 pr-5">
            <p class="card-text text-justify">
                With as many brokers as there are banks, the question remains: How does a broker create edge?
            </p>
            <p class="card-text text-justify">
                Applying a combined 18 years of managing our respective bank's equity derivatives trading books, resulting in the necessary experience and expertise in this very niche market, we believe Market Martial is the answer to that question, and more.
            </p>
            <p class="card-text text-justify">
                Market Martial has been custom built exclusively for derivatives traders, by derivatives traders.
            </p>
            <p class="card-text text-justify">
                The simple intention behind Market Martial has been to address all the inefficiencies we have experienced over the years by fast forwarding an old-fashioned market practice into the technical age, utilising the numerous benefits of a state of the art electronic platform.
            </p>
            <p class="card-text text-justify">
                Although the intention may have been simple, building a platform that caters to the many intricacies required to make a relatively complex market more efficient, has been anything but. We can only hope that our hard work and commitment has resulted in a product that you will find as useful as it is intended to be.
            </p>
            <p class="card-text text-justify">
                With you, the derivatives traders, being the core focus of the Market Martial platform, we encourage you to provide us with your comments and critiques tat could further improve your trading experience.
            </p>
            <div class="float-left">
                <img src="http://via.placeholder.com/200x75">
                <p class="card-text">
                    Brendan Harcourt-Wood, CFA<br>
                    Standard Bank Group (2004 - 2018)
                </p>
            </div>
            <div class="float-right">
                <img src="http://via.placeholder.com/200x75">
                <p class="card-text text-right ">
                    Wade Bothwell, CFA<br>
                    Investec Bank Ltd (2009 - 2018)
                </p>
            </div>
        </div>
    @endslot
@endcomponent

{{-- Priority Card --}}
@component('partials.content_card')
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
            <div class="col-md-3 circle-icon-tile text-center">
                <div class="circle-icon-block m-3"><span class="icon icon-business p-4"></span></div>
                <p class="mt-3 pt-3">
                    Experienced<br>specialist support
                </p>
            </div>
            <div class="col-md-2 circle-icon-tile text-center">
            <div class="circle-icon-block m-3"><span class="icon icon-wifi2 p-4"></span></div>
                <p class="mt-3 pt-3">
                    Advanced online<br>functionality
                </p>
            </div>
            <div class="col-md-3 circle-icon-tile text-center">
                <div class="circle-icon-block m-3"><span class="icon icon-money2 p-4"></span></div>
                <p class="mt-3 pt-3">
                    Rewards for market<br>making
                </p>
            </div>
        </div>
    @endslot
@endcomponent

@endsection