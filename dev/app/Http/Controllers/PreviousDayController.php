<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarketRequest\UserMarketRequest;
use App\Models\StructureItems\Market;
use Carbon\Carbon;

class PreviousDayController extends Controller
{
    /**
     * Display the previous day page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $now = Carbon::now();
        $tradeStart = Carbon::createFromTimeString(config('marketmartial.window.trade_start_time'));
        // if we are already in trading time
        if( $now->gt($tradeStart) ) {
            $tradeEnd = Carbon::createFromTimeString(config('marketmartial.window.trade_end_time'));
            // if we have passed the closing time
            if( $now->gt($tradeEnd) ) {
                // add 1 day to make it tomorrow
                $tradeStart = $tradeStart->addDays(1);
            }
        }

        return view('pages.previous_day')->with(compact('tradeStart'));
    }

    /**
     * Display the previous day page
     *
     * @return \Illuminate\Http\Response
     */
    public function showMarkets()
    {
        return Market::where('parent_id', NULL)
            ->with('children')
            ->get()
            ->map(function($market) {
                $market->is_seldom = true;
                return $market;
            });
    }

    /**
     * Display the previous day page
     *
     * @return \Illuminate\Http\Response
     */
    public function showMarketRequests()
    {
        $data = [];

        \Config::set('loading_previous_day', true); // set context

        $data['traded_market_requests'] = UserMarketRequest::active()
            ->previousDayTraded()
            ->get()
            ->map(function($mr) {
                return $mr->setOrgContext()->preFormattedPreviousDay(true);
            });

        $data['untraded_market_requests'] = UserMarketRequest::active()
            ->previousDayUntraded()
            ->get()
            ->map(function($mr) {
                return $mr->setOrgContext()->preFormattedPreviousDay(false);
            });

        \Config::set('loading_previous_day', false); // remove context

        return response()->json($data, 200);
    }
}
