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

class MarketUserMarketReqeustController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\StructureItems\Market  $market
     * @return \Illuminate\Http\Response
     */
    public function index(Market $market)
    {
        $userMarketRequests = $market->userMarketRequests()->select([
                    "id", 
                    "trade_structure_id", 
                    "user_id",
                    "created_at"
        ])->with([
            'tradeStructure' => function($q){ 
                $q->select([
                    "id", 
                    "title"]); 
            }, 
            'userMarketRequestGroups' => function($q){ 
                $q->select([
                    "id", 
                    "user_market_request_id", 
                    "trade_structure_group_id"
                ]); 
            },
            'userMarketRequestGroups.tradeStructureGroup' => function($q){ 
                $q->select([
                    "id", 
                    "title"
                ]); 
            },
            'userMarketRequestGroups.userMarketRequestItems' => function($q){ 
                $q->select([
                    "id", 
                    "title",
                    "value",
                    "item_id",
                    "user_market_request_group_id"
                ]); 
            }
        ])->get();

        $output = $userMarketRequests->map(function($marketRequest) {
            return [
                "id"                => $marketRequest->id,
                "trade_structure"   => $marketRequest->tradeStructure->title,
                "trade_items"       => $marketRequest->userMarketRequestGroups
                 ->keyBy('tradeStructureGroup.title')
                 ->map(function($group) {
                    return $group->userMarketRequestItems->keyBy('title')->map(function($item) {
                        return $item->value;
                    });
                }),
                "attributes" => $this->resolveRequestAttributes($marketRequest),
                "quotes"    => [
                    // UserMarketQuote
                ],
                // AND
                "quote:"    => [], //UserMarketQuote
                // OR - show quote to all, user_market to interest & market maker
                "user_market"   => [], //UserMarket

                "created_at"    => $marketRequest->created_at
            ];
        });

        // $output
        return $output;
    }   

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\StructureItems\Market  $market
     * @return \Illuminate\Http\Response
     */
    private function resolveRequestAttributes(UserMarketRequest &$userMarketRequest)
    {
        $attributes = [
            'state' => config('marketmartial.market_request_states.default'), // default state set first
            'bid_state' => "",
            'offer_state'   => "",
        ];

        // get is this current user the 
        $self_user = $userMarketRequest->user_id == \Auth::user()->id;
        $self_org = $userMarketRequest->user->organisation_id == \Auth::user()->organisation_id;

        // if not quotes/user_markets preset => REQUEST
        if($userMarketRequest->userMarkets->isEmpty()) {
            if($self_org) {
                $attributes['state'] = config('marketmartial.market_request_states.request.interest');
            } else {
                $attributes['state'] = config('marketmartial.market_request_states.request.other');
            }
        } 
        // if quotes exist, show as vol spread
        else {
            if($self_org) {
                $attributes['state'] = config('marketmartial.market_request_states.request-vol.interest');
            } else {
                $attributes['state'] = config('marketmartial.market_request_states.request-vol.other');
            }
        }

        return $attributes;
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

        $tradeStructure = TradeStructure::where('title',$request->input('trade_structure'))->with('tradeStructureGroups.items')->firstOrFail();

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
                DB::commit();
            } catch (Exception $e) 
            {
                DB::rollBack();
                Log::error($e->getMessage());
                return ['success'=>false,'data'=> null,'message'=>"Could not create market request."];
            }

        //broadCast new market request;

        return ['success'=>true,'data'=> $responseData,'message'=>"Market Request created successfully."];
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
}
