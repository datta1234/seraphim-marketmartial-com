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
            $this->authorize('spinNegotiation',$userMarket); 
            $marketNegotiation = $userMarket->spinNegotiation($request->user());  
        }else
        {
            $this->authorize('addNegotiation',$userMarket);
            $marketNegotiation = $userMarket->addNegotiation($request->user(),$request->all());  
        }
       
        $message = "Your levels have been sent.";
        $request->user()->organisation->notify("market_negotiation_store",$message,true);

        //broadCast new market request;
        $userMarket->fresh()->userMarketRequest->notifyRequested();
        if($marketNegotiation) {
            return ['success'=>true,'data'=>$marketNegotiation ,'message'=>$message];
        } else {
            return ['success'=>false,'data'=>$marketNegotiation ,'message'=>'There was a problem adding your levels'];
        }
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
        // $request->has('')
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserMarket $userMarket,MarketNegotiation $marketNegotiation)
    {
        $success = $marketNegotiation->kill();
        //broadCast Update;
        $userMarket->fresh()->userMarketRequest->notifyRequested();
        return response()->json([
            "data" => $success
        ]);
    }
}
