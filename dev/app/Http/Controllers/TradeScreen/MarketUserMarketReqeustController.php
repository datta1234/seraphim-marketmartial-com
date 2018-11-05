<?php

namespace App\Http\Controllers\TradeScreen;

use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\MarketRequest\UserMarketRequest;
use App\Models\StructureItems\Market;
use App\Models\StructureItems\TradeStructure;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StructureItems\TradeStructureGroup;
use App\Models\MarketRequest\UserMarketRequestGroup;
use App\Models\MarketRequest\UserMarketRequestItem;
use App\Models\MarketRequest\UserMarketRequestTradable;
use App\Models\StructureItems\Stock;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\TradeScreen\Market\UserMarketRequestRequest;
use App\Events\UserMarketRequested;
use Notification;
use App\Events\UserMarkerRequestQuantityLow;
use App\Models\UserManagement\User;
use App\Notifications\MarketRequestQuantityLowNotification;

class MarketUserMarketReqeustController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\StructureItems\Market  $market
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,Market $market)
    {
        $userMarketRequests = $market->userMarketRequests()
            ->activeForToday()
            ->with([
                'tradeStructure', 
                'userMarketRequestGroups',
                'userMarketRequestGroups.tradeStructureGroup' =>function($q){
                    $q->where("trade_structure_group_type_id",1)
                    ->with('items');
                },
                'userMarketRequestGroups.userMarketRequestItems'
            ])->get();


        $user = $request->user();

        $output = $userMarketRequests->map(function($marketRequest) use ($user) {
            return $marketRequest->setOrgContext($user->organisation)->preFormatted();
        });

        // $output
        return response()->json($output);
    }   

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\StructureItems\Market  $market
     * @return \Illuminate\Http\Response
     */
    public function create(Market $market)
    {


        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StructureItems\Market  $market
     * @return \Illuminate\Http\Response
     */
    public function store(UserMarketRequestRequest $request, Market $market)
    {
        $input = $request->all();
    


        $tradeStructure = TradeStructure::where('title',$request->input('trade_structure'))->with(['tradeStructureGroups' => function($q){
            $q->where("trade_structure_group_type_id",1)
            ->with('items');
        }]
        )->firstOrFail();

        $inputTradeStructureGroups = $request->input('trade_structure_groups');
        
        $responseData = [
            "trade_structure"   => $tradeStructure->title,
            "trade_items"       => []
        ];

        try {
                DB::beginTransaction();

                $userMarketRequest = new UserMarketRequest([
                    "trade_structure_id"                => $tradeStructure->id,
                    "user_market_request_statuses_id"   => 1,
                    "market_id"                         => $market->id,
                    "chosen_user_market_id"             => null
                ]);

                
                $userMarketRequest->user_id = $request->user()->id;
                $userMarketRequest->save();
                $responseData = ['id'=> $userMarketRequest->id];

                for($i = 0; $i < $tradeStructure->tradeStructureGroups->count(); $i++) 
                {


                    $tradeStructuregroup = $tradeStructure->tradeStructureGroups[$i];//earier to work with
                    $userMarketRequestGroup = UserMarketRequestGroup::create([
                        'is_selected'               =>  $tradeStructuregroup->force_select === null ? $inputTradeStructureGroups[$i]['is_selected'] : $tradeStructuregroup->force_select,
                        'trade_structure_group_id'  =>  $tradeStructuregroup->id,
                        'user_market_request_id'    => $userMarketRequest->id
                    ]);

                    $stock_id = null; 
                    $market_id = null;     

                    if(array_key_exists('stock', $inputTradeStructureGroups[$i]))//if a stock id passed then
                    {
                        $stock = Stock::where('code',$inputTradeStructureGroups[$i]['stock'])->first();
                        if(!$stock)
                        {
                            $stock = Stock::create([
                                'code'      => $inputTradeStructureGroups[$i]['stock'],
                                'verified'  => true
                            ]);
                        }
                        $stock_id = $stock->id;
                        $responseData['trade_items'][$tradeStructuregroup->title]["stock_code"] = $stock->code;

                    }else
                    {
                        $market_id = $inputTradeStructureGroups[$i]['market_id'];
                        $responseData['trade_items'][$tradeStructuregroup->title]["market"] = $market->title;
                    }

                    $userMarketRequestTradable = UserMarketRequestTradable::create([
                        "user_market_request_id"        => $userMarketRequest->id,
                        "market_id"                     => $market_id,
                        "stock_id"                      => $stock_id, 
                        "user_market_request_group_id"  => $userMarketRequestGroup->id
                    ]);


                    $inputTradeStructureGroupsfields = $inputTradeStructureGroups[$i]['fields'];
                    
                    foreach ($tradeStructuregroup->items as $structureItem)
                    {
                        if(array_key_exists($structureItem->title, $inputTradeStructureGroupsfields))
                        {       
                         //most of the values are based of the relation of schema only the va,ue is grabed from the join
                         $userMarketRequestItem =   UserMarketRequestItem::create([
                                'user_market_request_group_id'  => $userMarketRequestGroup->id,
                                'item_id'                       => $structureItem->id,
                                'title'                         => $structureItem->title,
                                'type'                          => $structureItem->itemType->title,
                                'value'                         => $inputTradeStructureGroupsfields[$structureItem->title]//actual sent from the frontend
                            ]); 

                        $responseData['trade_items'][$tradeStructuregroup->title][$userMarketRequestItem->title] = $userMarketRequestItem->value;

                        }else
                        {
                            // throw exception market request input incomplte
                        }
                        
                    }
                }
                if($input["trade_structure_groups"][0]["fields"]["Quantity"] < config('marketmartial.thresholds.quantity'))
                {
                    // @TODO - add send mail for singles where quantity is lower than 50
                    $recipients = User::whereHas('role', function ($query) {
                        $query->where('title', 'Admin');
                    })->get();
                    \Notification::send($recipients, new MarketRequestQuantityLowNotification($userMarketRequest));
                }

                DB::commit();



            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                Log::error($e->getMessage());
                return response()->json([
                    'success'=>false,
                    'data'=> null,
                    'message'=>'Could not create market request.', 
                ], 500);
            }

        //broadCast new market request;
        $userMarketRequest->notifyRequested();
        return response()->json([
            'success'=>true,
            'data'=> $responseData,
            'message'=>"Market Request created successfully.", 
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StructureItems\Market  $market
     * @param  \App\Models\MarketRequest\UserMarketRequest  $userMarketRequest
     * @return \Illuminate\Http\Response
     */
    public function show(Market $market, UserMarketRequest $userMarketRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StructureItems\Market  $market
     * @param  \App\Models\MarketRequest\UserMarketRequest  $userMarketRequest
     * @return \Illuminate\Http\Response
     */
    public function edit(Market $market, UserMarketRequest $userMarketRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StructureItems\Market  $market
     * @param  \App\Models\MarketRequest\UserMarketRequest  $userMarketRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Market $market, UserMarketRequest $userMarketRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StructureItems\Market  $market
     * @param  \App\Models\MarketRequest\UserMarketRequest  $userMarketRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(Market $market, UserMarketRequest $userMarketRequest)
    {
        //
    }

    /**
     * Changes cached variable's state on a User Market Request related to the current
     *  Authed Organisation to track Alerts on User Market Requests for an Organisation
     *
     * @param  \Illuminate\Http\Request\Request $request
     * @param  \App\Models\MarketRequest\UserMarketRequest  $userMarketRequest
     *
     * @return \Illuminate\Http\Response
     */
    public function actionTaken(Request $request, UserMarketRequest $userMarketRequest)
    {   
        if(!$request->input('action_needed')) {
            if($userMarketRequest->getAction($request->user()->organisation->id,$userMarketRequest->id) != null) {
                $userMarketRequest->setAction($request->user()->organisation->id, $userMarketRequest->id, $request->input('action_needed'));
                return ['success'=>true,'data'=> ["action_needed" => false],'message'=>"Action successfully updated."];
            } else {
                return ['success'=>true,'data'=> ["action_needed" => false],'message'=>"No action currently tracked."];
            }
        }
        return ['success'=>false,'data'=> null,'message'=>'Failed to update action.'];
    }
}
