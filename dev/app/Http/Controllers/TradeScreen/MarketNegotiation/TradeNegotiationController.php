<?php

namespace App\Http\Controllers\TradeScreen\MarketNegotiation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Market\MarketNegotiation;
use App\Http\Requests\TradeScreen\MarketNegotiation\TradeNegotiationStoreRequest;

class TradeNegotiationController extends Controller
{


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TradeNegotiationStoreRequest $request,MarketNegotiation $marketNegotiation)
    {
        $user = $request->user();
        $this->authorize('addTradeNegotiation',$marketNegotiation);
        $tradeNegotiation = $marketNegotiation->addTradeNegotiation($user,$request->all());
        
        // $user->organisation->notify("market_request_store","Response sent to interest.",true);
        if($tradeNegotiation)
        {
            $marketNegotiation->fresh()->userMarket->userMarketRequest->notifyRequested();
            return response()->json(['data' => $tradeNegotiation, 'message' => "Response sent to counterparty."]);    
        }
        else
        {
            return response()->json(['data' => false, 'message' => "Response failed to be sent."],500);
        }
    }
}