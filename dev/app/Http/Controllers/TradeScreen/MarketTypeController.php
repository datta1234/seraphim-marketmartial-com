<?php

namespace App\Http\Controllers\TradeScreen;

use App\Models\StructureItems\MarketType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MarketTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return MarketType::all();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StructureItems\MarketType  $marketType
     * @return \Illuminate\Http\Response
     */
    public function show(MarketType $marketType)
    {
        return $marketType;
    }

}
