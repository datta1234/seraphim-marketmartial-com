<?php

namespace App\Http\Controllers\Stats;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Trade\TradeNegotiation;
use App\Models\TradeConfirmations\TradeConfirmation;
use App\Models\StructureItems\Market;
use App\Models\StatsUploads\SafexTradeConfirmation;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Stats\MyActivityYearRequest;
use App\Http\Requests\Stats\CsvUploadDataRequest;
use Validator;
use Illuminate\Validation\Rule;

class ActivityControlller extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $years = TradeConfirmation::select(
            DB::raw("YEAR(trade_confirmations.updated_at) as year")
        )->groupBy('year')->get();

        return view('stats.market_activity')->with(compact('years'));
    }

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

        return view('stats.my_activity')->with(compact('user','graph_data','years'));
    }

    public function yearActivity(MyActivityYearRequest $request)
    {
        $user = $request->input('is_my_activity') ? $request->user() : null;
        $trade_confirmations = TradeConfirmation::basicSearch(
            $request->input('search'),
            $request->input('_order_by'),
            $request->input('_order'),
            [
                "filter_date" => $request->input('filter_date'),
                "filter_market" => $request->input('filter_market'),
                "filter_expiration" => $request->input('filter_expiration')
            ]
        )
        ->whereYear('updated_at',$request->input('year'));

        if($request->input('is_my_activity')) {
            $trade_confirmations = $trade_confirmations->where(function ($tlq) use ($user) {
                $tlq->organisationInvolved($user->organisation_id,'=')
                    ->orgnisationMarketMaker($user->organisation_id, true);
            });
        }

        $trade_confirmations = $trade_confirmations->paginate(10);

        $trade_confirmations->transform(function($trade_confirmation) use ($user) {
            return $trade_confirmation->preFormatStats($user);
        });

        return response()->json($trade_confirmations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CsvUploadDataRequest $request)
    {
        $path = $request->file('csv_upload_file')->getRealPath();
        $csv = array_map('str_getcsv', file($path));

        array_walk($csv, function(&$row) {
            array_walk($row, function(&$col) {
                $col = trim($col);
            });
        });

        foreach ($csv[0] as $index => $field) {
            $csv[0][$index] = config('marketmartial.import_csv_field_mapping.safex_fields.'.$field);
        }

        array_walk($csv, function(&$a) use ($csv) {
            $a = array_combine($csv[0], $a);
            // removing white space before validation
            $a['strike'] = str_replace(" ", "", $a['strike']);
            $a['trade_id'] = str_replace(" ", "", $a['trade_id']);
            $a['nominal'] = str_replace(" ", "", $a['nominal']);
        });
        array_shift($csv);
        
        // Validate the uploaded Csv fields
        $validator = Validator::make($csv,
            config('marketmartial.import_csv_field_mapping.safex_validation.rules'),
            config('marketmartial.import_csv_field_mapping.safex_validation.messages')
        );
        if ($validator->fails()) {
            return [
                'success' => false,
                'data' => ['messages' => $validator->messages()->toJson()],
                'message' => 'Failed to upload Open Interest data.'
            ];
        }

        SafexTradeConfirmation::truncate(); // removes all previous safex trade confirmation records
        try {
            DB::beginTransaction();
            $created = array_map('App\Models\StatsUploads\SafexTradeConfirmation::createFromCSV', $csv);
            DB::commit();
        } catch (\Exception $e) {
            \Log::error($e);
            DB::rollBack();
            return ['success' => false,'data' => null, 'message' => 'Failed to upload Safex data.'];
        }

        return ['success' => true,'data' => null,'message' => 'Safex data successfully uploaded.'];
    }

    public function safexRollingData(Request $request)
    {
        return SafexTradeConfirmation::basicSearch(
            $request->input('search'),
            $request->input('_order_by'),
            $request->input('_order'),
            [
                "filter_date" => $request->input('filter_date'),
                "filter_market" => $request->input('filter_market'),
                "filter_expiration" => $request->input('filter_expiration'),
                "filter_nominal" => $request->input('filter_nominal'),
            ]
        )->paginate(10);
    }
}
