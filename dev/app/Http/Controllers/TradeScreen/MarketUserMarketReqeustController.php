<?php

namespace App\Http\Controllers\TradeScreen;

use App\Models\MarketRequest\UserMarketRequest;
use App\Models\StructureItems\Market;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
                    "created_at",
                    "updated_at",
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

                "created_at"    => $marketRequest->created_at->format("Y-m-d H:i:s"),
                "updated_at"    => $marketRequest->updated_at->format("Y-m-d H:i:s"),
            ];
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StructureItems\Market  $market
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Market $market)
    {
        //
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
