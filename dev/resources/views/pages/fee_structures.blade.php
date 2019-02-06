@extends('layouts.app')

@section('content')

{{-- OPTIONS Card --}}
@component('partials.content_card', [ "class" => "card-dark" ])
    @slot('header')
        <span class="icon icon-info"></span>
        <h2 class="mt-1">TRADING SPREADS & FEES</h2>
    @endslot
    @slot('body')
        <h3 class="text-left fee-title" style="margin-top: -1.25rem;">
        OPTIONS
        </h3>
        <div class="row justify-content-md-center">
            <div class="col-9">
                <table class="table fee-table w-100">
                    <tr class="fee-header">
                        <td class=""></td>
                        <td class="">INDEX</td>
                        <td class="">SINGLES</td>
                        <td class="">MARKET-MAKER</td>
                    </tr>
                    <tr class="fee-row">
                        <td class="">Outright</td>
                        <td class="fee-orange">0.3 bps<br>(0.003%)</td>
                        <td class="fee-orange">5 bps<br>(0.05%)</td>
                        <td class="bb-0 fee-green" rowspan="6">Receives a Rebate of 25%<br>of Total Brokerage</td>
                    </tr>
                    <tr class="fee-row">
                        <td class="">Risky</td>
                        <td class="fee-orange">0.2 bps per leg</td>
                        <td class="fee-orange">3.5 bps per leg</td>
                    </tr>
                    <tr class="fee-row">
                        <td class="">Calendar</td>
                        <td class="fee-orange">0.3 bps per leg</td>
                        <td class="fee-orange">5 bps per leg</td>
                    </tr>
                    <tr class="fee-row">
                        <td class="">Fly</td>
                        <td class="fee-orange">0.2 bps per leg</td>
                        <td class="fee-orange">3.5 bps per leg</td>
                    </tr>
                    <tr class="fee-row">
                        <td class="">OptionSwitch</td>
                        <td class="fee-orange">0.3 bps per leg</td>
                        <td class="fee-orange">5 bps per leg</td>
                    </tr>
                    <tr class="fee-row">
                        <td class="">Var Swap</td>
                        <td class="fee-orange">4.0% of Vega</td>
                        <td class="fee-orange"></td>
                    </tr>
                </table>
           </div>
        </div>
        <h3 class="text-left fee-title mt-5">
        DELTA ONE
        </h3>
        <div class="row justify-content-md-center">
            <div class="col-9">
                <table class="table fee-table w-100">
                    <tr class="fee-header">
                        <td class=""></td>
                        <td class="">INDEX</td>
                        <td class=""></td>
                    </tr>
                    <tr class="fee-row">
                        <td class="">EFP</td>
                        <td class="fee-orange">0.2 bps<br>(0.002%)</td>
                        <td class="bb-0" rowspan="6">
                            <div class="w-50" style="margin-left:25%">
                                <table class="table table-sm slim borderless shadowless" style="background: none;">
                                    <tr>
                                        <td>Top40</td>
                                        <td>500</td>
                                        <td>R5,000</td>
                                        <td>*</td>
                                    </tr>
                                    <tr>
                                        <td>DTop</td>
                                        <td>2500</td>
                                        <td>R5,750</td>        
                                    </tr>
                                    <tr>
                                        <td>DCap</td>
                                        <td>1500</td>
                                        <td>R5,250</td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr class="fee-row">
                        <td class="">Rolls</td>
                        <td class="fee-orange">0.2 bps one leg only</td>
                    </tr>
                    <tr class="fee-row">
                        <td class="">EFP Switch</td>
                        <td class="fee-orange">0.2 bps per leg</td>
                    </tr>
                </table>
                <div class="float-right">
                    <table class="table table-sm slim borderless shadowless" style="font-size:0.8em">
                        <tr>
                            <td>*</td>
                            <td>Top40</td>
                            <td>50,000</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>DTop</td>
                            <td>11,500</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>DCap</td>
                            <td>17,500</td>
                        </tr>
                    </table>
                </div>
           </div>
        </div>
        <div class="row justify-content-md-center">
            <div class="col-9">
                <div class="col-12">
                    Spreads are quoted:<br>
                    &nbsp;&nbsp;- Per counterparty.<br>
                    &nbsp;&nbsp;- As a percentage (%) of ZAR nominal<br>
                    Options:<br>
                    &nbsp;&nbsp;- The market-maker receives 25% of total brokerage.<br>
                    &nbsp;&nbsp;- Where applicable, rebates are inclusive of VAT.<br>
                    Trading Spreads and Fees are subject to Terms and Conditions.<br>
                </div>
                <div class="offset-1 col-10 text-center mt-4">
                    MarketMartial.com is wholly owned and operated by Seraphim Financial Services (Pty) Ltd,
                    an authorized Financial Services Provider (49407). LEI: 894500BST0FYEQBT7307. VAT: 4630284075
                </div>
            </div>
        </div>
    @endslot
@endcomponent
<div class="push-down"></div>
@endsection