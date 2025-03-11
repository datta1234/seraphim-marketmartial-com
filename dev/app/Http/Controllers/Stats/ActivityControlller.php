<?php

namespace App\Http\Controllers\Stats;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Trade\TradeNegotiation;
use App\Models\TradeConfirmations\TradeConfirmation;
use App\Models\MarketRequest\UserMarketRequest;
use App\Models\StructureItems\Market;
use App\Models\StatsUploads\SafexTradeConfirmation;
use App\Models\UserManagement\Organisation;
use App\Models\MarketRequest\UserMarketRequestItem;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Stats\MyActivityYearRequest;
use App\Http\Requests\Stats\CsvUploadDataRequest;
use App\Http\Requests\Stats\SafexRollingDataRequest;
use Validator;
use Illuminate\Validation\Rule;

class ActivityControlller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $years = DB::select('
            SELECT DISTINCT YEAR(updated_at) as year
            FROM (
                SELECT updated_at FROM trade_confirmations
                UNION
                SELECT updated_at FROM user_market_requests
            ) updated_years
        ');

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
            DB::raw("count(*) as total"),
            "markets.title"
        )
        ->leftJoin("markets", "trade_confirmations.market_id", "=", "markets.id")
        ->groupBy("markets.title",'month')
        ->where('trade_confirmation_status_id', 4);

        $years = DB::select('
            SELECT DISTINCT YEAR(updated_at) as year
            FROM (
                SELECT updated_at FROM trade_confirmations
                UNION
                SELECT updated_at FROM user_market_requests
            ) updated_years
        ');

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

        return view('stats.my_activity')->with(compact('graph_data','years'));
    }

    public function yearActivity(MyActivityYearRequest $request)
    {
        // Checks if the table is all MM or My activity table
        $user = $request->input('is_my_activity') ? $request->user() : null;

        // Checks for admin bank tables only
        $is_Admin = $request->user()->role_id == 1 && $request->input('is_bank_level');
        
        $user_market_requests = UserMarketRequest::basicSearch(
            $request->input('search'),
            $request->input('_order_by'),
            $request->input('_order'),
            [
                "filter_date" => $request->input('filter_date'),
                "filter_market" => $request->input('filter_market'),
                "filter_expiration" => $request->input('filter_expiration')
            ]
        )
        ->whereYear('user_market_requests.updated_at',$request->input('year'))
        ->has('chosenUserMarket')
        ->where(function ($tlq) {
            $tlq->where(function ($q) {
                $q->has('tradeConfirmations');
            })
            ->orWhere(function ($q) {
                $q->doesnthave('tradeConfirmations');
            });
        });

        if($request->input('is_my_activity')) {
            $user_market_requests = $user_market_requests->where(function ($tlq) use ($user) {
                $tlq->organisationInvolvedTrade($user->organisation_id,'=')
                    ->organisationMarketMaker($user->organisation_id, true)
                    ->organisationInterestNotTraded($user->organisation_id, true);
            });
        }

        $user_market_requests =  $user_market_requests->select(DB::raw(
            'user_market_requests.*,
            trade_confirmations.trade_confirmation_status_id as trade_status, 
            trade_confirmations.updated_at as trade_date,
            trade_confirmations.send_user_id as trade_send_user_id, 
            trade_confirmations.receiving_user_id as trade_receiving_user_id,
            trade_confirmations.trade_negotiation_id as trade_negotiation_id,
            trade_confirmations.id as trade_confirmation_id, 
            (
                select title 
                from trade_structures
                where id=user_market_requests.trade_structure_id
            ) as trade_structure_title'))
        ->leftJoin('trade_confirmations', function($join)
        {
           $join->on('trade_confirmations.user_market_request_id', '=', 'user_market_requests.id')
                ->where('trade_confirmations.trade_confirmation_status_id', '=', 4);
        });

        $user_market_requests = $user_market_requests->paginate(50);

        $user_market_requests->transform(function($user_market_request) use ($user, $is_Admin) {
            return $user_market_request->preFormatStats($user, $is_Admin);
        });

        $expiration_dates = UserMarketRequestItem::select("value")->where('type', 'expiration date')->distinct()->orderBy('value', 'ASC')->pluck("value");

        return response()->json([
            'message' => "Year Data Loaded",
            'data' => [ 
                "table_data" => $user_market_requests,
                "expiration_dates" => $expiration_dates,     
            ]
        ], 200);
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

        if(count($csv) > 5001) {
            return response()->json([
                'errors' => [],
                'message' => 'Failed to upload Safex data. Csv file is larger than 5001 lines.'
            ], 422);
        }

        // 1. Create a new array of csv file lines
        array_walk($csv, function(&$row,$row_index) use (&$csv) {
            array_walk($row, function(&$col) use (&$csv,$row,$row_index) {
                if(count($row) <= 1 && $col == null) {
                    unset($csv[$row_index]);
                } else {
                    $col = trim($col);
                }
            });
        });

        // 2. Replace the imported fields with the data base fields
        foreach ($csv[0] as $index => $field) {
            $csv[0][$index] = config('marketmartial.import_csv_field_mapping.safex_fields.'.$field);
        }

        $errors = array();
        // 3. remove headings field and map each value to the heading as key value pair
        array_walk($csv, function(&$a, $idx) use ($csv,&$errors) {
            $a = array_combine($csv[0], $a);
            // 3.1 removing white space before validation
            $a['strike'] = str_replace(" ", "", $a['strike']);
            $a['trade_id'] = str_replace(" ", "", $a['trade_id']);
            $a['nominal'] = str_replace(" ", "", $a['nominal']);

            if($idx > 0) {
                // 3.2 Validate current row fields
                $validator = Validator::make($a,
                    config('marketmartial.import_csv_field_mapping.safex_validation.rules'),
                    config('marketmartial.import_csv_field_mapping.safex_validation.messages')
                );

                if($validator->fails()) {
                    // 3.3 Add row validation errors to the list
                    $errors[$idx] = $validator->messages();
                }
            }
        });
        array_shift($csv);
        
        // 4. Return validation errors
        if (!empty($errors)) {
            return response()->json([
                'errors' => $errors,
                'message' => 'Failed to upload Safex data.'
            ], 422);
        }

        // 5. removes all previous safex trade confirmation records
        SafexTradeConfirmation::truncate();
        try {
            DB::beginTransaction();
            // 6. create new records for each csv file entry
            $created = array_map('App\Models\StatsUploads\SafexTradeConfirmation::createFromCSV', $csv);
            SafexTradeConfirmation::insert($created);
            DB::commit();
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error($e);
            DB::rollBack();
            return response()->json(['message' => 'Failed to upload Safex data.', 'errors'=>[]], 500);
        }

        return response()->json(['data' => null,'message' => 'Safex data successfully uploaded.']);
    }

    public function safexRollingData(SafexRollingDataRequest $request)
    {
        $safex_confirmation_data = SafexTradeConfirmation::basicSearch(
            $request->input('search'),
            $request->input('_order_by'),
            $request->input('_order'),
            [
                "filter_date" => $request->input('filter_date'),
                "filter_market" => $request->input('filter_market'),
                "filter_expiration" => $request->input('filter_expiration'),
                "filter_nominal" => $request->input('filter_nominal'),
                "filter_non_expired" => $request->input('filter_non_expired'),
            ]
        )->paginate(50);

        $latest_date = SafexTradeConfirmation::orderBy('trade_date', 'DESC')->first();
        $expiration_dates = SafexTradeConfirmation::select("expiry")->distinct()->orderBy('expiry', 'ASC')->pluck("expiry");

        return response()->json([
            'message' => "Year Data Loaded",
            'data' => [ 
                "table_data" => $safex_confirmation_data,
                "expiration_dates" => $expiration_dates,
                "latest_date" => isset($latest_date) ? $latest_date->trade_date : $latest_date
            ]
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function adminShow(Request $request)
    {
        $markets = Market::pluck("title", "id");
        $organisations = Organisation::pluck("title", "id");
        $trade_confirmations = DB::select('SELECT tc.market_id ,
            (
                SELECT u.organisation_id
                FROM users u
                WHERE u.id = tc.send_user_id
            ) as sending_org_id,
            (
                SELECT u.organisation_id
                FROM users u
                WHERE u.id = tc.receiving_user_id
            ) as receiving_org_id,
            (
                SELECT (
                    SELECT (
                        SELECT uu.organisation_id
                        FROM users uu
                        WHERE uu.id = um.user_id
                    )
                    FROM user_markets um
                    WHERE um.id = tn.user_market_id
                )
                FROM trade_negotiations tn
                WHERE tn.id = tc.trade_negotiation_id


            ) as maker_org_id
            FROM `trade_confirmations` tc
            WHERE tc.trade_confirmation_status_id = ?',[4]
        );
        
        $graph_data = collect($trade_confirmations)->reduce(function ($carry, $item) use ($markets, $organisations){
            $market_id = $markets[$item->market_id];
            $sender_organisation = $organisations[$item->sending_org_id];
            $receiver_organisation = $organisations[$item->receiving_org_id];
            $maker_organisation = $organisations[$item->maker_org_id];

            if( !isset($carry[$market_id]) ) {
                $carry[$market_id] = [];
            }

            // ensure that the org is being tracked in new array and set default counts for each
            if( !isset($carry[$market_id][$sender_organisation]) ) {
                $carry[$market_id][$sender_organisation] = [
                    'total' => 0,
                    'traded' => 0,
                    'traded_away' => 0
                ];
            }
            if( !isset($carry[$market_id][$receiver_organisation]) ) {
                $carry[$market_id][$receiver_organisation] = [
                    'total' => 0,
                    'traded' => 0,
                    'traded_away' => 0
                ];
            }
            if( !isset($carry[$market_id][$maker_organisation]) ) {
                $carry[$market_id][$maker_organisation] = [
                    'total' => 0,
                    'traded' => 0,
                    'traded_away' => 0
                ];
            }

            // Number of trades - organisation was sending_org || receiving_org
            $carry[$market_id][$sender_organisation]['total']++;
            $carry[$market_id][$receiver_organisation]['total']++;

            // Markets Made (Traded) - organisation was market maker and organisation traded
            if( in_array($maker_organisation, [ $sender_organisation, $receiver_organisation ]) ) {
                $carry[$market_id][$maker_organisation]['traded']++;
            } else {
            // Markets Made (Traded Away) - organisation was market maker and someone else traded
                $carry[$market_id][$maker_organisation]['traded_away']++;
            }

            return $carry;
        }, array_fill_keys( $markets->values()->toArray() , null)); // Adding all the markets that are not in our dataset

        // Get the years for the yearly tables
        $years = DB::select('
            SELECT DISTINCT YEAR(updated_at) as year
            FROM (
                SELECT updated_at FROM trade_confirmations
                UNION
                SELECT updated_at FROM user_market_requests
            ) updated_years
        ');
        
        return view('admin.stats.bank_activity')->with(compact('graph_data','years'));
    }
}
