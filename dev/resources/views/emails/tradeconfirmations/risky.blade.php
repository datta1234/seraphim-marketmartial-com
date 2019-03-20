@component('mail::message', ['trade_confirmation' => $trade_confirmation])
The Following is your confirmed trade with the Market Martial Platform.
Thank you for your trade!
Date: {{ $trade_confirmation["date"]->toDateString() }}
Structure: {{ $trade_confirmation["trade_structure_title"] }}

###Option
@component('mail::table')

| {{ $trade_confirmation["organisation"] }} | Underlying | Strike | Put/Call | Nominal | Contracts | Expiry | Volatility | Gross Prem | Net Prem |  
|-------------------------------------------|------------|--------|----------|---------|-----------|--------|------------|------------|----------|
@foreach ( $trade_confirmation["option_groups"] as $option_group )
| {{ $option_group["trade_confirmation_items"]->firstWhere("title","is_offer")->value ? "Buys" : "Sells" }} |
 {{ $option_group["user_market_request_group"]["tradable"]["title"] }} |
 {{ $option_group["user_market_request_group"]["items"]->firstWhere("title","Strike")->value }} |
 {{ $option_group["trade_confirmation_items"]->firstWhere("title","is_put")->value ? "Put" : "Call" }} |
 {{  }} |
 {{  }} |
 {{  }} |
 {{  }} |
 {{  }} |
 {{  }} |
@endforeach

@endcomponent

###Futures


@endcomponent

