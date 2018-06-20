<?php

namespace App\Http\Controllers\TradeScreen;

use App\Models\StructureItems\Market;
use App\Models\StructureItems\MarketType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MarketTypeMarketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\StructureItems\MarketType  $marketType
     * @return \Illuminate\Http\Response
     */
    public function index(MarketType $marketType)
    {
        return $marketType->markets()->where('parent_id', NULL)->with('children')->get();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StructureItems\MarketType  $marketType
     * @param  \App\Models\StructureItems\Market  $market
     * @return \Illuminate\Http\Response
     */
    public function show(MarketType $marketType, Market $market)
    {
        return $market;
    }

}
