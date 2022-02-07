@component('mail::table')

|  @foreach(explode(" ",$trade_confirmation["organisation"]) as $word) {{ $word }} <br> @endforeach | Underlying | Spot {{ $trade_confirmation['market_id'] == 4 ? "(ZAR)": ""}} | Future {{ $trade_confirmation['market_id'] == 4 ? "(ZAR)": ""}} | Contracts | Expiry |  
|-------------------------------------------:|------------:|---------------------------------------------------------------:|-----------------------------------------------------------------:|-----------:|--------:|
@foreach ( $trade_confirmation["future_groups"] as $future_group )
| {{ $future_group["trade_confirmation_items"]->firstWhere("title","is_offer")['value'] ? "Buys" : "Sells" }} | {{ $future_group["user_market_request_group"]["tradable"]["title"] }} | {{ $future_group["trade_confirmation_items"]->firstWhere("title","Spot")['value'] ? number_format($future_group["trade_confirmation_items"]->firstWhere("title","Spot")['value'],2,'.',' ') : '-' }} | {{ $future_group["trade_confirmation_items"]->firstWhere("title","Future")['value'] ? number_format($future_group["trade_confirmation_items"]->firstWhere("title","Future")['value'],2,'.',' ') : '-' }} | {{ $future_group["trade_confirmation_items"]->firstWhere("title","Contract")['value'] ? number_format($future_group["trade_confirmation_items"]->firstWhere("title","Contract")['value'],0,'.',' ') : '-' }} | {{ \Carbon\Carbon::parse($future_group["user_market_request_group"]["items"]->firstWhere("title","Expiration Date")['value'])->format("d-M-Y") }} |
@endforeach

@endcomponent