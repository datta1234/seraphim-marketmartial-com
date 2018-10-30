<?php

namespace App\Http\Controllers\TradeScreen\UserMarket;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Market\UserMarket;
use App\Models\Market\MarketNegotiation;
use App\Http\Requests\TradeScreen\UserMarket\MarketNegotiationRequest;
use App\Http\Requests\TradeScreen\UserMarket\MarketNegotiationCounterRequest;
use App\Http\Requests\TradeScreen\UserMarket\MarketNegotiationImproveBestRequest;

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

        //broadCast new market request;
        $userMarket->fresh()->userMarketRequest->notifyRequested();
        if($marketNegotiation) {
            $message = "Your levels have been sent.";
            $request->user()->organisation->notify("market_negotiation_store",$message,true);
            return ['success'=>true,'data'=>$marketNegotiation ,'message'=>$message];
        } else {
            return response()->json(['success'=>false,'data'=>$marketNegotiation ,'message'=>'There was a problem adding your levels'], 500);
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
        $success = false;
        $message = 'Invalid Action';

        // Handle FOK kill
        if($marketNegotiation->isFoK()) {
            $success = $marketNegotiation->kill(\Auth::user());
            $message = "FoK Killed";
        }

        // Handle Meet In Middle
        if($marketNegotiation->isProposal() || $marketNegotiation->isMeetInMiddle()) {
            $success = $marketNegotiation->reject();
            $marketNegotiation->userMarket
                ->trackActivity(
                    "organisation.".$marketNegotiation->user->organisation_id.".proposal.".$marketNegotiation->id.".rejected",
                    "Proposal rejected by counter", 
                    10
                );
            $marketNegotiation->userMarket
                ->trackActivity(
                    "organisation.".$marketNegotiation->counterUser->organisation_id.".proposal.".$marketNegotiation->id.".reject",
                    "Proposal rejected", 
                    10
                );
            $message = "Proposal Rejected";
        }

        $userMarket->fresh()->userMarketRequest->notifyRequested();
        return ['success'=>$success ,'message'=>$message];
    }


    /**
     * Counter the proposal
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function counterProposal(MarketNegotiationCounterRequest $request, UserMarket $userMarket,MarketNegotiation $marketNegotiation)
    {
        $this->authorize('counter',$marketNegotiation);
        $market = $marketNegotiation->counter($request->user(), $request->only(['bid', 'offer']));
        $userMarket->fresh()->userMarketRequest->notifyRequested();
        
        return ['success'=>true, 'message'=>'Counter Sent'];
    }

    /**
     * Counter the proposal
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function repeatProposal(MarketNegotiationCounterRequest $request, UserMarket $userMarket,MarketNegotiation $marketNegotiation)
    {
        $this->authorize('spinNegotiation',$userMarket); 
        $marketNegotiation->repeat($request->user());

        $userMarket->fresh()->userMarketRequest->notifyRequested();
        
        return ['success'=>true, 'message'=>'Counter Sent'];
    }


    /**
     * Improve the current best for trade-at-best condition proposal
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function improveBest(MarketNegotiationImproveBestRequest $request, UserMarket $userMarket,MarketNegotiation $marketNegotiation)
    {
        $this->authorize('improveBest',$marketNegotiation);
        $userMarket->lastNegotiation->improveBest($request->user(), $request->all());

        $userMarket->fresh()->userMarketRequest->notifyRequested();
        return ['success'=>true, 'message'=>'Improvement Sent'];
    }


}
