<?php

namespace App\Http\Controllers\Tradescreen\UserMarket;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Market\MarketNegotiation;
use App\Models\Market\UserMarket;
use App\Http\Requests\Tradescreen\UserMarket\MarketNegotiationUpdateRequest;

class MarketNegotiationController extends Controller
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
    public function store(Request $request)
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
    public function update(MarketNegotiationUpdateRequest $request, UserMarket $userMarket,MarketNegotiation $marketNegotiation)
    {

        $data = $request->all();

        if(array_key_exists('is_repeat',$data) && $data['is_repeat'])
        {
            $marketNegotiation->update(['is_repeat'=>true]);
        }else
        {
            $marketNegotiation->update($data);
        }

        $userMarket->update(['is_on_hold'=>false]);
        // Set action that needs to be taken for the org related to this userMarketRequest
        $userMarket->userMarketRequest->setAction(
            $userMarket->userMarketRequest->user->organisation->id,
            $userMarket->userMarketRequest->id,
            true
        );
        
        $userMarket->userMarketRequest->notifyRequested();
        
        return response()->json(['data' => $marketNegotiation, 'message' => 'Response sent to Interest.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
