@component('mail::message', ['trade_confirmation' => $trade_confirmation])
The Following is your confirmed trade with the Market Martial Platform.<br><br>
Thank you for your trade!<br>
Date: {{ $trade_confirmation["date"] }}<br>
Structure: {{ $trade_confirmation["trade_structure_title"] }}<br>

###Option
@component('mail::table')

|
 {{ $trade_confirmation["swap_parties"]["initiate_org"] }} | {{ $trade_confirmation["swap_parties"]["recieving_org"] }} | Underlying | Expiry | Volatility Level | Vega | Capped | Near Dated Future Ref |
|-----------------------------------------------------------|------------------------------------------------------------|------------|--------|------------------|------|--------|-----------------------|
@foreach ( $trade_confirmation["request_groups"] as $request_group )
| {{ $trade_confirmation['swap_parties']['is_offer'] ? "Buys" : "Sells" }} | {{ $trade_confirmation['swap_parties']['is_offer'] ? "Sells" : "Buys" }} | {{ $request_group["tradable"]["title"] }} | {{ $request_group['items']->firstWhere("title","Expiration Date")["value"] }} | {{ $trade_confirmation["volatility"] }} | {{ number_format($request_group['items']->firstWhere("title","Quantity")["value"],0,'.',' ') }} | {{ $request_group['items']->firstWhere("title","Cap")["value"] }} | {{ number_format($request_group['items']->firstWhere("title","Future")["value"],2,'.',' ') }} |
@endforeach

@endcomponent

@endcomponent

