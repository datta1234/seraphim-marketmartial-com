<?php

namespace App\Http\Controllers\TradeScreen\MarketRequest;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TradeScreen\MarketRequest\UserMarketStoreRequest;
use App\Http\Requests\TradeScreen\MarketRequest\UserMarketUpdateRequest;
use App\Models\Market\UserMarket;
use App\Models\MarketRequest\UserMarketRequest;
use App\Events\UserMarketRequested;

class UserMarketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserMarketStoreRequest $request, UserMarketRequest $userMarketRequest)
    {   
        $user = $request->user();
             
        $this->authorize('addQoute',$userMarketRequest);     
        $data = $request->all();
        // user market
        $data['user_id'] = $request->user()->id;
        $data['current_market_negotiation']['user_id'] = $request->user()->id;
        $userMarket = $userMarketRequest->createQuote($data);

        if(!$userMarket) {
            return response()->json(['error' => "Failed to store quote", 'message' => "Failed to store quote."], 500);
        }

        // Set action that needs to be taken for the org related to this userMarketRequest
        $userMarket->userMarketRequest->setAction(
            $userMarket->userMarketRequest->user->organisation->id,
            $userMarket->userMarketRequest->id,
            true
        );
        
        $user->organisation->notify("market_request_store","Response sent to interest.",true);
        $userMarketRequest->notifyRequested();

        return response()->json(['data' => $userMarket, 'message' => "Response sent to interest."]);

    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }


  public function noFurtherCares(Request $request,UserMarketRequest $userMarketRequest,UserMarket $userMarket)
    {
        $user = $request->user();
        $userMarket->noFurtherCares($user);
        $userMarket->fresh()->userMarketRequest->notifyRequested();
        return response()->json(['data' => $tradeNegotiation, 'message' => ""]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     *
     * @todo add error handeling and error response
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UserMarketUpdateRequest $request, UserMarketRequest $userMarketRequest,UserMarket $userMarket)
    {
        $organisations = [];

        if($request->has('is_on_hold') && $request->input('is_on_hold'))
        {

           $this->authorize('placeOnHold',$userMarket);
            $success = $userMarket->placeOnHold();
            $request->user()->organisation->notify("market_request_update","You have placed a market on hold. Response sent to counterparty.",true);
            // Set action that needs to be taken for the org being put on hold
            $userMarketRequest->setAction($userMarket->user->organisation->id,$userMarketRequest->id,true);
       
        }elseif($request->has('accept') && $request->input('accept'))
        {
            $this->authorize('accept',$userMarket);
            $success = $userMarket->accept();
            $organisations[] = $myOrganisation = $request->user()->organisation;
            $myOrganisation->notify("market_request_update","You have accepted the market. Response sent to counterparty.",true);
            // Set action that needs to be taken for theaccepted
            // $userMarketRequest->setAction($userMarket->user->organisation->id,$userMarketRequest->id,true);
       
        }else
        {
           // dd("here it is");
            $this->authorize('updateNegotiation',$userMarket);
            //the market maker allowed responses
            if($request->has('is_repeat') && $request->input('is_repeat'))
            {
                $success = $userMarket->repeatQuote($request->user());
            }else
            {
                $success = $userMarket->updateQuote($request->user(),$request->all());
            }


            $request->user()->organisation->notify("market_request_update","Response sent to interest.",true);

            // Set action that needs to be taken for the org related to this userMarketRequest
            $userMarket->userMarketRequest->setAction(
                $userMarket->userMarketRequest->user->organisation->id,
                $userMarket->userMarketRequest->id,
                true
            );
        }

        // TODO add error handeling and error response
        $userMarketRequest->fresh()->notifyRequested($organisations);
        return response()->json(['data' => $success]);
    }

    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, UserMarketRequest $userMarketRequest, UserMarket $userMarket)
    {
        $this->authorize('delete',$userMarket);
        $userMarket->delete();
        $request->user()->organisation->notify("market_request_delete","Your quote has been pulled.",true);
        $userMarketRequest->notifyRequested();
        return response()->json(['data' => null]);
    }
}
