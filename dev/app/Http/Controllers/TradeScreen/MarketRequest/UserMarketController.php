<?php

namespace App\Http\Controllers\TradeScreen\MarketRequest;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TradeScreen\MarketRequest\UserMarketStoreRequest;
use App\Http\Requests\TradeScreen\MarketRequest\UserMarketUpdateRequest;
use App\Models\Market\UserMarket;
use App\Models\MarketRequest\UserMarketRequest;

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
    public function store(UserMarketStoreRequest $request)
    {
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

        return response()->json($userMarket);
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
        // TODO add error handeling and error response
          $userMarket = $userMarket->update($request->only('is_on_hold'));

          return response()->json(['data' => $userMarket, 'message' => 'User Market successfully updated']);
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
