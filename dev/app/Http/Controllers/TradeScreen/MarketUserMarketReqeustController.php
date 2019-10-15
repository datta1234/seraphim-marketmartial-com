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
            ->active()
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StructureItems\Market  $market
     * @return \Illuminate\Http\Response
     */
    public function store(UserMarketRequestRequest $request, Market $market)
    {
        $this->authorize('addMarketReqeust',$market);
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
                "market_id"                         => $market->id,
                "chosen_user_market_id"             => null
            ]);

            
            $userMarketRequest->user_id = $request->user()->id;
            $userMarketRequest->save();
            $responseData = ['id'=> $userMarketRequest->id];

            for($i = 0; $i < $tradeStructure->tradeStructureGroups->count(); $i++) 
            {


                $tradeStructuregroup = $tradeStructure->tradeStructureGroups[$i];//easier to work with
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
                     //most of the values are based of the relation of schema only the value is grabed from the join
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



        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json([
                'data'=> null,
                'message'=>'Could not create market request.', 
            ], 500);
        }

        // Determine if any of the trade_structure_groups has a quantity below the threshold for that relevant market
        $all_above_threshold = array_reduce($input["trade_structure_groups"], function($carry, $item) {

            if(array_key_exists('stock', $item)) {
                return $carry && ($item["fields"]["Quantity"] >= config('marketmartial.thresholds.stock_quantity'));
            }

            if(array_key_exists('Cap', $item["fields"])) {
                return $carry && ($item["fields"]["Quantity"] >= config('marketmartial.thresholds.var_swap_quantity'));
            }

            $config_market_default = config('marketmartial.thresholds.index_quantity.'.$item["market_id"]);
            if($config_market_default == null) {
                return $carry && ($item["fields"]["Quantity"] >= config('marketmartial.thresholds.quantity')); 
            }

            return $carry && ($item["fields"]["Quantity"] >= $config_market_default);
        }, true);       

        // Send the admin a notification if requested market is below the threshold
        if(!$all_above_threshold)
        {
            $recipients = User::whereHas('role', function ($query) {
                $query->where('title', 'Admin');
            })->get();
            try {
                \Notification::send($recipients, new MarketRequestQuantityLowNotification($userMarketRequest));
            } catch(\Swift_TransportException $e) {
                Log::error($e);
            }
        }


        //broadCast new market request;
        $userMarketRequest->fresh()->notifyRequested();
        return response()->json([
            'data'=> $responseData,
            'message'=>"Market Request created successfully.", 
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StructureItems\Market  $market
     * @param  \App\Models\MarketRequest\UserMarketRequest  $marketRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(Market $market, UserMarketRequest $marketRequest)
    {
        $this->authorize('deactivate',$marketRequest);
        $marketRequest->active = false;
        $marketRequest->save();

        $marketRequest->notifyRequested();

        return response()->json([
            'data'=> null,
            'message'=>"Market Request removed successfully.",
        ], 201);
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
        $this->authorize('actionTaken',$userMarketRequest);
        if($request->has('action_needed')) {
            if($userMarketRequest->getAction($request->user()->organisation->id,$userMarketRequest->id) != null) {
                $userMarketRequest->setAction($request->user()->organisation->id, $userMarketRequest->id, $request->input('action_needed'));
                $userMarketRequest->fresh()->notifyRequested();
                return response()->json(['data'=> ["action_needed" => false],'message'=>"Action successfully updated."]);
            } else {
                return response()->json(['data'=> ["action_needed" => false],'message'=>"No action currently tracked."]);
            }
        }
        return response()->json(['message'=>'Failed to update action.', 'errors'=>['action_needed'=>'Action needed is required parameter']], 422);
    }
}
