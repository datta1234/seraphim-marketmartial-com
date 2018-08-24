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
    public function store(UserMarketStoreRequest $request,UserMarketRequest $userMarketRequest)
    {   
        $user = $request->user();
             
        $this->authorize('addQoute',$userMarketRequest);     
        $data = $request->all();
        // user market
        $data['user_id'] = $request->user()->id;
        $userMarket = UserMarket::create($data);

        // negotiation
        $data['current_market_negotiation']['user_id'] = $request->user()->id;
        $marketNegotiation = $userMarket
            ->marketNegotiations()
            ->create($data['current_market_negotiation']);

        $userMarket
            ->currentMarketNegotiation()
            ->associate($marketNegotiation)
            ->save();

        // conditions
        $marketNegotiationConditions = $marketNegotiation
            ->marketConditions()
            ->sync(collect($data['current_market_negotiation']['conditions'])->pluck('id'));
        

        // Set action that needs to be taken for the org related to this userMarketRequest
        $userMarket->userMarketRequest->setAction(
            $userMarket->userMarketRequest->user->organisation->id,
            $userMarket->userMarketRequest->id,
            true
        );
        
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
        $organisations = null;

        if($request->has('is_on_hold') && $request->input('is_on_hold'))
        {

           $this->authorize('placeOnHold',$userMarket);
            $success = $userMarket->placeOnHold();
            $message = 'You have placed a market on hold. Response sent to counterparty.';
            // Set action that needs to be taken for the org being put on hold
            $userMarketRequest->setAction($userMarket->user->organisation->id,$userMarketRequest->id,true);
        }elseif($request->has('accept') && $request->input('accept'))
        {
            $this->authorize('accept',$userMarket);
            $success = $userMarket->accept();
            $message = 'You have accepted the market. Response sent to counterparty.';
            $organisations[] = $request->user()->organisation;

            // Set action that needs to be taken for theaccepted
            $userMarketRequest->setAction($userMarket->user->organisation->id,$userMarketRequest->id,true);
        }else
        {
            $this->authorize('updateNegotiation',$userMarket);
            //the market maker allowed responses
            if($request->has('is_repeat') && $request->input('is_repeat'))
            {
                $success = $userMarket->repeatQuote($request->user());
            }else
            {
                $success = $userMarket->updateQuote($request->user(),$request->all());
            }

            // Set action that needs to be taken for the org related to this userMarketRequest
            $userMarket->userMarketRequest->setAction(
                $userMarket->userMarketRequest->user->organisation->id,
                $userMarket->userMarketRequest->id,
                true
            );

            $message = 'Response sent to Interest.';

        }

        // TODO add error handeling and error response
        $userMarketRequest->fresh()->notifyRequested($organisations);

        return response()->json(['data' => $success, 'message' => $message]);
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
        $userMarketRequest->notifyRequested();
        return response()->json(['data' => null, 'message' => 'Your quote has been pulled.']);
    }
}
