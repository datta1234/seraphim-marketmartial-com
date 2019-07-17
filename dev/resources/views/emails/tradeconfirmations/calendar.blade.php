@component('mail::message', ['trade_confirmation' => $trade_confirmation])
The Following is your confirmed trade with the Market Martial Platform.<br><br>
Thank you for your trade!<br>
Date: {{ $trade_confirmation["date"] }}<br>
Structure: {{ $trade_confirmation["trade_structure_title"] }}<br>

###Option
@include('emails.tradeconfirmations.partials.option_groups', ['trade_confirmation' => $trade_confirmation])

###Futures
@include('emails.tradeconfirmations.partials.future_groups', ['trade_confirmation' => $trade_confirmation])

@endcomponent

