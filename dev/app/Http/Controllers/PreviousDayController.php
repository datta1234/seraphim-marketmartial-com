<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarketRequest\UserMarketRequest;
use App\Models\StructureItems\Market;

class PreviousDayController extends Controller
{
    /**
     * Display the previous day page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.previous_day');
    }

    /**
     * Display the previous day page
     *
     * @return \Illuminate\Http\Response
     */
    public function showMarkets()
    {
        return Market::where('parent_id', NULL)->with('children')->get();
    }

    /**
     * Display the previous day page
     *
     * @return \Illuminate\Http\Response
     */
    public function showMarketRequests()
    {
        $data = [];
        $data['traded_market_requests'] = UserMarketRequest::previousDayTraded()->get()->map(function($mr){
            return $mr->setOrgContext()->preFormatted();
        });
        $data['untraded_market_requests'] = UserMarketRequest::previousDayUntraded()->get()->map(function($mr){
            return $mr->setOrgContext()->preFormatted();
        });
        return response()->json($data, 200);
    }
}
