<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trade\TradeNegotiation;
use App\Models\TradeConfirmations\TradeConfirmation;
use App\Models\StructureItems\Market;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Stats\MyActivityYearRequest;

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
        $my_org_trade_confirmations = null;
        $other_org_trade_confirmations = null;

        $trade_confirmations = TradeConfirmation::select(
            DB::raw("concat(MONTH(trade_confirmations.updated_at),'-',YEAR(trade_confirmations.updated_at))  as month"),
            DB::raw("count(*) as total"),"markets.title")
                ->leftJoin("markets", "trade_confirmations.market_id", "=", "markets.id")
                ->groupBy("markets.title",'month');

        $years = TradeConfirmation::select(
            DB::raw("YEAR(trade_confirmations.updated_at) as year")
        )->groupBy('year')->get();

        if($request->ajax() && $request->has('my_trades') && $request->input('my_trades') == '1') {
            $my_org_trade_confirmations = clone $trade_confirmations;
            $my_org_trade_confirmations->userInvolved($user->organisation_id,'=');

            $other_org_trade_confirmations = clone $trade_confirmations;
            $other_org_trade_confirmations->userInvolved($user->organisation_id,'!=');
        } else {
            $my_org_trade_confirmations = clone $trade_confirmations;
            $my_org_trade_confirmations->organisationInvolved($user->organisation_id,'=');

            $other_org_trade_confirmations = clone $trade_confirmations;
            $other_org_trade_confirmations->organisationInvolved($user->organisation_id,'!=');
            
        }

    	$markets = Market::all();
    	$graph_data = array();
    	foreach ($markets as $market) {
    		$graph_data[$market->title] = null;
    	}
    	
    	// Number of trades	- trade_negotiations.traded == true || Trade confirmation
    	$number_of_trades = $my_org_trade_confirmations->get()
    		->groupBy(function ($item, $key) {
    			return $item->title;
    		});//$graph_data["total_trades"]
        foreach ($number_of_trades as $market => $single) {
            $graph_data[$market]["total_trades"] = $single;
        }

    	// Markets Made (Traded) - organisation was market maker and organisation traded
    	$markets_made_traded = $my_org_trade_confirmations
    		->orgnisationMarketMaker($user->organisation_id)
    		->get()
    		->groupBy(function ($item, $key) {
    			return $item->title;
    		});
        foreach ($markets_made_traded as $market => $single) {
            $graph_data[$market]["traded"] = $single;
        }

    	// Markets Made (Traded Away) - organisation was market maker and someone else traded
    	$markets_made_traded_away = $other_org_trade_confirmations
    		->orgnisationMarketMaker($user->organisation_id)
    		->get()
            ->groupBy(function ($item, $key) {
    			return $item->title;
    		});
        foreach ($markets_made_traded_away as $market => $single) {
            $graph_data[$market]["traded_away"] = $single;
        }

        if($request->ajax()) {
            return $graph_data;
        }

        return view('stats.show')->with(compact('user','graph_data','years'));
    }

    public function myYearActivity(MyActivityYearRequest $request)
    {
        $user = $request->user();

        $trade_confirmations = TradeConfirmation::whereYear('updated_at',$request->input('year'))
            ->where(function ($tlq) use ($user) {
                $tlq->organisationInvolved($user->organisation_id,'=')
                    // ->orWhere(function ($query) use ($user) {
                    //     $query->orgnisationMarketMaker($user->organisation_id);
                    // });
                ->orgnisationMarketMaker($user->organisation_id, true);
            })->get();

        $output = $trade_confirmations->map(function($trade_confirmation) {
            return $trade_confirmation->preFormatStats();
        });

        return response()->json($output);
    }
}
