<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarketRequest\UserMarketRequest;
use App\Models\Market\UserMarket;
use App\Models\Market\MarketNegotiation;
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

        \Config::set('loading_previous_day', true); // set request context

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

        \Config::set('loading_previous_day', false); // reset request context
        return response()->json($data, 200);
    }

    /**
     * Display the previous day page
     *
     * @return \Illuminate\Http\Response
     */
    public function getOldQuotes()
    {
        \Config::set('loading_previous_day', true); // set request context
        $data = UserMarket::whereHas('user', function($q){
                $q->where('organisation_id', \Auth::user()->organisation_id);
            })
            ->activeQuotes()
            ->with('userMarketRequest')
            ->get()
            ->filter(function($quote){
                // only the best quotes
                return $quote->isBestQuote();
            })
            ->map(function($quote) {
                $data = $quote->setOrgContext()->preFormattedQuote();
                $data['market_request_summary'] = $quote->userMarketRequest->getSummary();
                return $data;
            });
        \Config::set('loading_previous_day', false); // reset request context
        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    /**
     * Display the previous day page
     *
     * @return \Illuminate\Http\Response
     */
    public function refreshOldQuotes(Request $request)
    {
        $refresh = $request->get('refresh');

        // get only the applicable quotes belonging to this user
        \Config::set('loading_previous_day', true); // set request context
        $quotes = UserMarket::whereHas('user', function($q){
                $q->where('organisation_id', \Auth::user()->organisation_id);
            })
            ->activeQuotes()
            ->get()
            ->filter(function($quote){
                // only the best quotes
                return $quote->isBestQuote();
            });
        \Config::set('loading_previous_day', false); // reset request context

        // walk through the quotes and refresh if need be or pull
        $marketRequests = [];
        foreach($quotes as $quote) {
            if(in_array($quote->id, $refresh)) {
                // REFRESH
                $quote->touch();
                $quote->lastNegotiation->touch();
                // mark request to be updated
                if(!in_array($quote->user_market_request_id, $marketRequests)) {
                    $marketRequests[] = $quote->user_market_request_id;
                }
            } else {
                // PULL
                $quote->delete();
            }
        }

        // push out notifications for required market requests
        UserMarketRequest::whereIn('id', $marketRequests)
        ->get()
        ->each(function(&$marketRequest) {
            $marketRequest->notifyRequested();
        });

        return response()->json([
            'success' => true,
            'data' => [
                'message' => $quotes->count()." quotes were refreshed"
            ]
        ], 200);
    }

    /**
     * Display the previous day page
     *
     * @return \Illuminate\Http\Response
     */
    public function getOldNegotiations()
    {
        \Config::set('loading_previous_day', true); // set request context
        $data = MarketNegotiation::whereHas('user', function($q){
            $q->where('organisation_id', \Auth::user()->organisation_id);
        })
        ->previousDayRefreshable()
        ->get()
        ->map(function($negotiation) {
            $item = $negotiation->setOrgContext()->preFormattedMarketNegotiation();
            $item['market_request_summary'] = $negotiation->userMarket->userMarketRequest->getSummary();
            return $item;
        });
        \Config::set('loading_previous_day', false); // reset request context
        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    /**
     * Display the previous day page
     *
     * @return \Illuminate\Http\Response
     */
    public function refreshOldNegotiations(Request $request)
    {
        $refresh = $request->get('refresh');

        // get only the applicable quotes belonging to this user
        \Config::set('loading_previous_day', true); // set request context
        $negotiations = MarketNegotiation::whereHas('user', function($q){
            $q->where('organisation_id', \Auth::user()->organisation_id);
        })
        ->previousDayRefreshable()
        ->get();
        \Config::set('loading_previous_day', false); // reset request context

        // walk through the quotes and refresh if need be or pull
        $marketRequests = [];
        foreach($negotiations as $negotiation) {
            if(in_array($negotiation->id, $refresh)) {
                // REFRESH
                $negotiation->touch();
                $negotiation->userMarket->touch();
                $negotiation->userMarket->userMarketRequest->touch();

                // mark request to be updated
                if(!in_array($negotiation->userMarket->user_market_request_id, $marketRequests)) {
                    $marketRequests[] = $negotiation->userMarket->user_market_request_id;
                }
            } else {
                // PULL
                $negotiation->userMarket->userMarketRequest->active = false;
                $negotiation->userMarket->userMarketRequest->save();
            }
        }

        // push out notifications for required market requests
        UserMarketRequest::whereIn('id', $marketRequests)
        ->get()
        ->each(function(&$marketRequest) {
            $marketRequest->notifyRequested();
        });

        return response()->json([
            'success' => true,
            'data' => [
                'message' => $negotiations->count()." levels were refreshed"
            ]
        ], 200);
    }
}
