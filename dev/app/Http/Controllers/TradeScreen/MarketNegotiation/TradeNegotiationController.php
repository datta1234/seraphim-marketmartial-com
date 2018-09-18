<?php

namespace App\Http\Controllers\TradeScreen\MarketNegotiation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Market\MarketNegotiation;
use App\Events\marketNegotiationed;

class TradeNegotiationController extends Controller
{


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,MarketNegotiation $marketNegotiation)
    {   
        $user = $request->user();
             
        //$this->authorize('addTradeNegotiation',$marketNegotiation);     
        $tradeNegotiation = $marketNegotiation->addTradeNegotiation($user,$request->all());


        // // Set action that needs to be taken for the org related to this marketNegotiation
        // $userMarketnegotiation->userMarket->marketNegotiation->setAction(
        //     $userMarket->marketNegotiation->user->organisation->id,
        //     $userMarket->marketNegotiation->id,
        //     true
        // );
        
       // $user->organisation->notify("market_request_store","Response sent to interest.",true);
        $marketNegotiation->userMarket->userMarketRequest->notifyRequested();
        return response()->json(['data' => $tradeNegotiation, 'message' => "Response sent to interest."]);

    }
}