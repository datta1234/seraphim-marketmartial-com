<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trade\TradeNegotiation;
use App\Models\TradeConfirmations\TradeConfirmation;
use App\Models\StructureItems\Market;

class StatsController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
    	$user = $request->user();
    	$organisation_trade_confirmations = TradeConfirmation::organisationInvolved($user->organisation_id);
    	$markets = Market::all();
    	$graph_data = array();
    	foreach ($markets as $market) {
    		$graph_data[$market->title] = null;
    	}
    	
    	// Number of trades	- trade_negotiations.traded == true || Trade confirmation
    	$number_of_trades = $organisation_trade_confirmations->get()
    		->groupBy(function ($item, $key) {
    			return $item->market->title;
    		});
    	foreach ($number_of_trades as $market => $number_of_trade) {
    		$graph_data[$market]["total_trades"] = $number_of_trade->groupBy(function ($item, $key) {
                    return $item->month;
                });
            foreach ($graph_data[$market]["total_trades"] as $key => $items) {
                $graph_data[$market]["total_trades"][$key] = $items->count();
            }
    	}

    	// Markets Made (Traded) - organisation was market maker and organisation traded
    	$markets_made_traded = $organisation_trade_confirmations
    		->orgnisationMarketMaker($user->organisation_id)
    		->get()
    		->groupBy(function ($item, $key) {
    			return $item->market->title;
    		});
    	foreach ($markets_made_traded as $market => $single) {
    		$graph_data[$market]["traded"] = $single->groupBy(function ($item, $key) {
                    return $item->month;
                });
            foreach ($graph_data[$market]["traded"] as $key => $items) {
                $graph_data[$market]["traded"][$key] = $items->count();
            }
    	}

    	// Markets Made (Traded Away) - organisation was market maker and someone else traded
    	$markets_made_traded_away = TradeConfirmation::organisationNotInvolved($user->organisation_id)
    		->orgnisationMarketMaker($user->organisation_id)
    		->get()->groupBy(function ($item, $key) {
    			return $item->market->title;
    		});
    	foreach ($markets_made_traded_away as $market => $single) {
    		$graph_data[$market]["traded_away"] = $single->groupBy(function ($item, $key) {
                    return $item->month;
                });
            foreach ($graph_data[$market]["traded_away"] as $key => $items) {
                $graph_data[$market]["traded_away"][$key] = $items->count();
            }
    	}

        return view('stats.show')->with(['user' => $user, 'graph_data' => $graph_data]);
    }
}
