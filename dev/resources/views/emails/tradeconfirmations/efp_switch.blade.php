@component('mail::message', ['trade_confirmation' => $trade_confirmation])
The Following is your confirmed trade with the Market Martial Platform.<br><br>
Thank you for your trade!<br>
Date: {{ $trade_confirmation["date"] }}<br>
Structure: {{ $trade_confirmation["trade_structure_title"] }}<br>

###Futures
@component('mail::table')

| {{ $trade_confirmation["organisation"] }} | Underlying | Spot {{ $trade_confirmation['market_id'] == 4 ? "(ZAR)": ""}} | Future {{ $trade_confirmation['market_id'] == 4 ? "(ZAR)": ""}} | Contracts | Expiry |  
|-------------------------------------------|------------|---------------------------------------------------------------|-----------------------------------------------------------------|-----------|--------|
@foreach ( $trade_confirmation["future_groups"] as $future_group )
| {{ $future_group["trade_confirmation_items"]->firstWhere("title","is_offer")['value'] ? "Buys" : "Sells" }} | {{ $future_group["user_market_request_group"]["tradable"]["title"] }} | {{ number_format($future_group["trade_confirmation_items"]->firstWhere("title","Spot")['value'],0,'.',' ') }} | {{ number_format($future_group["trade_confirmation_items"]->firstWhere("title","Future")['value'],0,'.',' ') }} | {{ number_format($future_group["trade_confirmation_items"]->firstWhere("title","Contract")['value'],0,'.',' ') }} | {{ $future_group["user_market_request_group"]["items"]->firstWhere("title","Expiration Date")['value'] }} |
@endforeach

@endcomponent

@endcomponent

