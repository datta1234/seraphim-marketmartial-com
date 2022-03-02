@component('mail::message', ['trade_confirmation' => $trade_confirmation, 'seraphim_mail' => true])
The following is your confirmed trade with Seraphim.<br><br>
Thank you for the trade!<br>
Date: {{ $trade_confirmation["date"] }}<br>
Structure: {{ $trade_confirmation["trade_structure_title"] }}<br>

###Option
@component('mail::table')

|
 {{ $trade_confirmation["swap_parties"]["initiate_org"] }} | {{ $trade_confirmation["swap_parties"]["recieving_org"] }} | Underlying | Expiry | Volatility Level | Vega | Capped | Near Dated Future Ref |
|-----------------------------------------------------------:|------------------------------------------------------------:|------------:|--------:|------------------:|------:|--------:|-----------------------:|
@foreach ( $trade_confirmation["request_groups"] as $request_group )
| {{ $trade_confirmation['swap_parties']['is_offer'] ? "Buys" : "Sells" }} | {{ $trade_confirmation['swap_parties']['is_offer'] ? "Sells" : "Buys" }} | {{ $request_group["tradable"]["title"] }} | {{ Carbon\Carbon::parse($request_group['items']->firstWhere("title","Expiration Date")["value"])->format("d-M-Y") }} | {{ $trade_confirmation["volatility"] }} | {{ $request_group['items']->firstWhere("title","Quantity")["value"] ? number_format($request_group['items']->firstWhere("title","Quantity")["value"],0,'.',' ') : '-' }} | {{ $request_group['items']->firstWhere("title","Cap")["value"] }} | {{ $request_group['items']->firstWhere("title","Future")["value"] ? number_format($request_group['items']->firstWhere("title","Future")["value"],2,'.',' ') : '-' }} |
@endforeach

@endcomponent

###Fees
@include('emails.tradeconfirmations.partials.fee_groups', ['trade_confirmation' => $trade_confirmation])

@endcomponent

