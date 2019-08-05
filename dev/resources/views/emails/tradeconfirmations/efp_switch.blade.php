@component('mail::message', ['trade_confirmation' => $trade_confirmation])
The following is your confirmed trade with the Market Martial Platform.<br><br>
Thank you for the trade!<br>
Date: {{ $trade_confirmation["date"] }}<br>
Structure: {{ $trade_confirmation["trade_structure_title"] }}<br>

###Futures
@include('emails.tradeconfirmations.partials.future_groups', ['trade_confirmation' => $trade_confirmation])

@endcomponent

