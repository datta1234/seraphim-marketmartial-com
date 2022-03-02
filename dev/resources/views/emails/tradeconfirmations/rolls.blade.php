@component('mail::message', ['trade_confirmation' => $trade_confirmation, 'seraphim_mail' => true])
The following is your confirmed trade with Seraphim.<br><br>
Thank you for the trade!<br>
Date: {{ $trade_confirmation["date"] }}<br>
Structure: {{ $trade_confirmation["trade_structure_title"] }}<br>

###Futures
@component('mail::table')

| {{ $trade_confirmation["organisation"] }} | Underlying | Spot | Future | Contracts | Expiry |  
|-------------------------------------------:|------------:|------:|--------:|-----------:|--------:|
| {{ $trade_confirmation["future_groups"][0]["trade_confirmation_items"]->firstWhere("title","is_offer 1")['value'] ? "Buys" : "Sells" }} | {{ $trade_confirmation["future_groups"][0]["user_market_request_group"]["tradable"]["title"] }} | - | {{ $trade_confirmation["future_groups"][0]["trade_confirmation_items"]->firstWhere("title","Future 1")['value'] ? number_format($trade_confirmation["future_groups"][0]["trade_confirmation_items"]->firstWhere("title","Future 1")['value'],2,'.',' ') : '-' }} | {{ $trade_confirmation["future_groups"][0]["trade_confirmation_items"]->firstWhere("title","Contract")['value'] ? number_format($trade_confirmation["future_groups"][0]["trade_confirmation_items"]->firstWhere("title","Contract")['value'],0,'.',' ') : '-' }} | {{ Carbon\Carbon::parse($trade_confirmation["future_groups"][0]["user_market_request_group"]["items"]->firstWhere("title","Expiration Date 1")['value'])->format("d-M-Y") }} |
 | {{ $trade_confirmation["future_groups"][0]["trade_confirmation_items"]->firstWhere("title","is_offer 2")['value'] ? "Buys" : "Sells" }} | {{ $trade_confirmation["future_groups"][0]["user_market_request_group"]["tradable"]["title"] }} | - | {{ $trade_confirmation["future_groups"][0]["trade_confirmation_items"]->firstWhere("title","Future 2")['value'] ? number_format($trade_confirmation["future_groups"][0]["trade_confirmation_items"]->firstWhere("title","Future 2")['value'],0,'.',' ') : '-' }} | {{ $trade_confirmation["future_groups"][0]["trade_confirmation_items"]->firstWhere("title","Contract")['value'] ? number_format($trade_confirmation["future_groups"][0]["trade_confirmation_items"]->firstWhere("title","Contract")['value'],0,'.',' ') : '-' }} | {{ Carbon\Carbon::parse($trade_confirmation["future_groups"][0]["user_market_request_group"]["items"]->firstWhere("title","Expiration Date 2")['value'])->format("d-M-Y") }} |
@endcomponent

###Fees
@include('emails.tradeconfirmations.partials.fee_groups', ['trade_confirmation' => $trade_confirmation])

@endcomponent

