@extends('layouts.previous_day')

@section('head')
@parent
<!-- Trading Open Time -->
<meta name="trading-opens" content="{{ $tradeStart }}">
@endsection

@section('content')
<div class="container-fluid previous-day-wrapper mt-3">
    <div class="row">
        <trading-countdown :open-time="trading_opens"></trading-countdown>
    </div class="row">
    <div class="row">
        <div class="trading-title col-12 text-center">
            YESTERDAY'S TRADES
        </div>
    </div class="row">
    <div class="row">
        <traded-markets class="previous-day-traded" :markets="display_markets_traded" class="mb-5"></traded-markets>
    </div class="row">
    <div class="row">
        <untraded-markets class="previous-day-untraded" :markets="display_markets_untraded"></untraded-markets>
    </div class="row">
</div>
@endsection