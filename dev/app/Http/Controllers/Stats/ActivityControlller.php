<?php

namespace App\Http\Controllers\Stats;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Trade\TradeNegotiation;
use App\Models\TradeConfirmations\TradeConfirmation;
use App\Models\StructureItems\Market;
use App\Models\StatsUploads\SafexTradeConfirmation;
use App\Models\UserManagement\Organisation;
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
            DB::raw("DISTINCT YEAR(trade_confirmations.updated_at) as year")
        )->get();

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
                ->groupBy("markets.title",'month')
                ->where('trade_confirmation_status_id', 4);

        $years = TradeConfirmation::select(
            DB::raw("DISTINCT YEAR(trade_confirmations.updated_at) as year")
        )->get();

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
        $is_Admin = $request->user()->isAdmin() && $request->input('is_bank_level');
        
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
        ->whereYear('updated_at',$request->input('year'))
        ->where('trade_confirmation_status_id', 4);

        if($request->input('is_my_activity')) {
            $trade_confirmations = $trade_confirmations->where(function ($tlq) use ($user) {
                $tlq->organisationInvolved($user->organisation_id,'=')
                    ->orgnisationMarketMaker($user->organisation_id, true);
            });
        }

        $trade_confirmations = $trade_confirmations->paginate(10);

        $trade_confirmations->transform(function($trade_confirmation) use ($user, $is_Admin) {
            return $trade_confirmation->preFormatStats($user, $is_Admin);
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

        // Create a new array of csv file lines
        array_walk($csv, function(&$row) {
            array_walk($row, function(&$col) {
                $col = trim($col);
            });
        });

        // Replace the imported fields with the data base fields
        foreach ($csv[0] as $index => $field) {
            $csv[0][$index] = config('marketmartial.import_csv_field_mapping.safex_fields.'.$field);
        }

        // remove headings field and map each value to the heading as key value pair
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

        // removes all previous safex trade confirmation records
        SafexTradeConfirmation::truncate();
        try {
            DB::beginTransaction();
            // create new records for each csv file entry
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
        // @TODO - Change reqeust to a custom reqeust
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
        $years = TradeConfirmation::select(
            DB::raw("YEAR(trade_confirmations.updated_at) as year")
        )->groupBy('year')->get();
        
        return view('admin.stats.bank_activity')->with(compact('graph_data','years'));
    }
}
