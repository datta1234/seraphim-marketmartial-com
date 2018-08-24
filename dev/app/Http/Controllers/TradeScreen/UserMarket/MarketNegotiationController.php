<?php

namespace App\Http\Controllers\TradeScreen\UserMarket;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Market\UserMarket;
use App\Models\Market\MarketNegotiation;
use App\Http\Requests\TradeScreen\UserMarket\MarketNegotiationRequest;


class MarketNegotiationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UserMarket $userMarket)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(UserMarket $userMarket)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MarketNegotiationRequest $request, UserMarket $userMarket)
    {
        if($request->has('is_repeat') && $request->input('is_repeat'))
        {
            $oldNegotiation = $userMarket->marketNegotiations()->where('user_id',$request->user()->id)->orderBy('created_at', 'desc')->first();
            $marketNegotiation = $oldNegotiation->replicate();
            $marketNegotiation->is_repeat = true;
            $marketNegotiation->save();

        }else
        {
            $marketNegotiation = new MarketNegotiation($request->all());     
        }
       
       
        $marketNegotiation->user_id = $request->user()->id;
        $counterNegotiation = $userMarket->marketNegotiations()->orderBy('created_at', 'desc')->first();

        if($counterNegotiation)
        {
             $marketNegotiation->market_negotiation_id = $counterNegotiation->id;
             $marketNegotiation->counter_user_id = $counterNegotiation->user_id;
        }

        $userMarket->marketNegotiations()->save($marketNegotiation);
        $userMarket->current_market_negotiation_id = $marketNegotiation->id;
        $userMarket->save();
        
        //broadCast new market request;
        $userMarket->userMarketRequest->notifyRequested();
        return ['success'=>true,'data'=>$marketNegotiation ,'message'=>"Your levels have been sent."];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(UserMarket $userMarket,MarketNegotiation $marketNegotiation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(MarketNegotiation $marketNegotiation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MarketNegotiationRequest $request,UserMarket $userMarket,MarketNegotiation $marketNegotiation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserMarket $userMarket,MarketNegotiation $marketNegotiation)
    {
        //
    }
}
