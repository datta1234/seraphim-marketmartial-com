@extends('layouts.previous_day')

@section('content')
<div class="container-fluid previous-day-wrapper">
    <b-row>
        <traded-markets :market-requests="traded_market_requests"></traded-markets>
    </b-row>
    <b-row>
        <untraded-markets :market-requests="untraded_market_requests"></untraded-markets>
    </b-row>
</div>
@endsection