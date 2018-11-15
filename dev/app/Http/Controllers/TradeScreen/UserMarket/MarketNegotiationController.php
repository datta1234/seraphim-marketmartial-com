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
        // dd("here");
        if($request->has('is_repeat') && $request->input('is_repeat'))
        {
            $this->authorize('spinNegotiation',$userMarket); 
            $marketNegotiation = $userMarket->spinNegotiation($request->user());  
        }
        else
        {
            $this->authorize('addNegotiation',$userMarket);
            $marketNegotiation = $userMarket->addNegotiation($request->user(),$request->all());  
        }

        //broadCast new market request;
        $userMarket->fresh()->userMarketRequest->notifyRequested();
        if($marketNegotiation) {
            return response()->json(['success'=>true,'data'=>$marketNegotiation->preFormattedMarketNegotiation() ,'message'=>'']);

        } else {
            return response()->json(['success'=>false,'data'=>null ,'message'=>'There was a problem adding your levels'], 500);
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
    public function update(MarketNegotiationRequest $request,UserMarket $userMarket, MarketNegotiation $marketNegotiation)
    {
        $this->authorize('amend', $marketNegotiation);
        try {
            \DB::beginTransaction();

            $userMarket->updateNegotiation($marketNegotiation, $request->all());
            $userMarket->fresh()->userMarketRequest->notifyRequested();

            \DB::commit();
            return response()->json(['success'=>true,'data'=>$marketNegotiation->preFormattedMarketNegotiation() ,'message'=>'']);
        } catch(\Illuminate\Database\QueryException $e) {
            \Log::error($e);
            \DB::rollback();
            return response()->json(['success'=>false,'data'=>null ,'message'=>'There was a problem adding your levels'], 500);
        }
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
            $marketNegotiation->userMarket
                ->trackActivity(
                    "organisation.".$marketNegotiation->user->organisation_id.".proposal.".$marketNegotiation->id.".killed",
                    "FoK killed by counter", 
                    10
                );
            $marketNegotiation->userMarket
                ->trackActivity(
                    "organisation.".$marketNegotiation->counterUser->organisation_id.".proposal.".$marketNegotiation->id.".kill",
                    "FoK killed", 
                    10
                );
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

        $userMarket->userMarketRequest->fresh()->notifyRequested();
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

        $marketNegotiation->userMarket
            ->trackActivity(
                "organisation.".$marketNegotiation->user->organisation_id.".proposal.".$marketNegotiation->id.".countered",
                "Counter Proposal received", 
                10
            );
        $marketNegotiation->userMarket
            ->trackActivity(
                "organisation.".$marketNegotiation->counterUser->organisation_id.".proposal.".$marketNegotiation->id.".counter",
                "Proposal countered", 
                10
            );
        
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

        $marketNegotiation->userMarket
            ->trackActivity(
                "organisation.".$marketNegotiation->user->organisation_id.".proposal.".$marketNegotiation->id.".repeated",
                "Proposal repeated by counter", 
                10
            );
        $marketNegotiation->userMarket
            ->trackActivity(
                "organisation.".$marketNegotiation->counterUser->organisation_id.".proposal.".$marketNegotiation->id.".repeat",
                "Proposal repeated", 
                10
            );
        
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
        $last = $userMarket->lastNegotiation;

        $last->improveBest($request->user(), $request->all());

        $marketNegotiation->userMarket
            ->trackActivity(
                "organisation.".$marketNegotiation->user->organisation_id.".proposal.".$marketNegotiation->id.".improved",
                "New best level has been set", 
                10
            );
        $last->userMarket
            ->trackActivity(
                "organisation.".$last->user->organisation_id.".proposal.".$last->id.".improved",
                "New best level has been set",
                10
            );
        $marketNegotiation->userMarket
            ->trackActivity(
                "organisation.".$request->user()->organisation_id.".proposal.".$last->id.".improve",
                "Best level improved", 
                10
            );

        $userMarket->fresh()->userMarketRequest->notifyRequested();
        return ['success'=>true, 'message'=>'Improvement Sent'];
    }


}
