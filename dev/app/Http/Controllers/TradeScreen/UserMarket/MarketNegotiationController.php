<?php

namespace App\Http\Controllers\Tradescreen\UserMarket;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Market\MarketNegotiation;
use  App\Models\Market\UserMarket;

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
    public function update(Request $request, UserMarket $userMarket,MarketNegotiation $marketNegotiation)
    {
        $marketNegotiation->update($request->all());  
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
