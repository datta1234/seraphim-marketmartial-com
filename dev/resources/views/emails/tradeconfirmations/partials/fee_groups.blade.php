@component('mail::table')

|  Calculated Fee |  
|-------------------------------------------:|
@foreach ( $trade_confirmation["fee_groups"] as $fee_group )
| {{ $fee_group["trade_confirmation_items"]->firstWhere("title","Fee Total")['value'] ? number_format($fee_group["trade_confirmation_items"]->firstWhere("title","Fee Total")['value'],2,'.',' ') : '-' }} |
@endforeach

@endcomponent