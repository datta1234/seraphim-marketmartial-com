<?php

namespace App\Http\Controllers\TradeScreen\MarketType;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StructureItems\TradeStructure;
use App\Models\StructureItems\MarketType;

class TradeStructureController extends Controller
{

	/**
     * Display a listing of the resource.
     *
     * @param  \App\Models\StructureItems\MarketType  $marketType
     * @return \Illuminate\Http\Response
     */
   	public function index(MarketType $marketType)
   	{
   		return $marketType->tradeStructures()->with('tradeStructureGroups.items')->get();
   	}
}
