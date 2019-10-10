<?php

namespace App\Models\TradeConfirmations;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Broadcast\Stream;
use App\Models\Trade\Rebate;
use App\Events\TradeConfirmationEvent;
use App\Models\TradeConfirmations\TradeConfirmationItem;
use App\Models\TradeConfirmations\TradeConfirmationGroup;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Notifications\TradeConfirmedNotification;

class TradeConfirmation extends Model
{
    use \App\Traits\ResolvesUser;
    use \App\Traits\ResolveTradeStructureSlug;

    use \App\Traits\CalculatesForPhases;
    use \App\Traits\CalculatesForOutright;
    use \App\Traits\CalculatesForRisky;
    use \App\Traits\CalculatesForCalendar;
    use \App\Traits\CalculatesForFly;
    use \App\Traits\CalculatesForOptionSwitch;
    use \App\Traits\CalculatesForEfp;
    use \App\Traits\CalculatesForRolls;
    use \App\Traits\CalculatesForEfpSwitch;

	/**
	 * @property integer $id
	 * @property integer $send_user_id
	 * @property integer $receiving_user_id
	 * @property integer $trade_negotiation_id
	 * @property integer $trade_confirmation_status_id
	 * @property integer $trade_confirmation_id
	 * @property integer $stock_id
	 * @property integer $market_id
	 * @property integer $send_trading_account_id
     * @property integer $receiving_trading_account_id
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trade_confirmations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'send_user_id',
        'receiving_user_id',
        'trade_negotiation_id',
        'stock_id',
        'market_id',
        'trade_structure_id',
        'user_market_request_id',
        'send_trading_account_id',
        'receiving_trading_account_id',
        'trade_confirmation_status_id'
    ];


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmationStatus()
    {
        return $this->belongsTo(
        	'App\Models\TradeConfirmations\TradeConfirmationStatus',
        	'trade_confirmation_status_id'
        );
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function disputes()
    {
        return $this->hasMany('App\Models\TradeConfirmations\Dispute','trade_confirmation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmationParent()
    {
        return $this->belongsTo('App\Models\TradeConfirmations\TradeConfirmation','trade_confirmation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmationChildren()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmation','trade_confirmation_id');
    }


    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function bookedTrades()
    {
        return $this->hasMany('App\Models\TradeConfirmations\BookedTrade','trade_confirmation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function stock()
    {
        return $this->belongsTo('App\Models\StructureItems\Stock','stock_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function market()
    {
        return $this->belongsTo('App\Models\StructureItems\Market','market_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeNegotiation()
    {
        return $this->belongsTo('App\Models\Trade\TradeNegotiation','trade_negotiation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function TradeStructure()
    {
        return $this->belongsTo('App\Models\StructureItems\TradeStructure','trade_structure_id');
    }



     /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
     public function marketRequest()
     {
        return $this->belongsTo('App\Models\MarketRequest\UserMarketRequest','user_market_request_id');
    }

    /**
    * Return string message
    * @return String
    */
    public function getMessage($scenario) {
        switch($scenario) {
            case 'confirmation_disputed':
                if($this->trade_confirmation_status_id == 5) {
                    // trade_confirmation_status_id == 5
                    $partyA = $this->sendUser;
                    $partyB = $this->recievingUser;
                } else {
                    // trade_confirmation_status_id == 3
                    $partyB = $this->sendUser;
                    $partyA = $this->recievingUser;
                }
                $partyA = $partyA->full_name." ".$partyA->cell_phone;
                $partyB = $partyB->full_name." ".$partyB->cell_phone;
                return "Dispute lodged on ".$this->marketRequest->getSummary()." between (".$partyA.") and (".$partyB.")";
            break;
        }
    }

    public function notifyConfirmation($organisation,$message, $timer = 3000)
    {
        $sendOrg = $this->sendUser->organisation;
        $receiveOrg = $this->recievingUser->organisation;

        $organisation->notify("trade_confirmation_store",$message,true,$timer);
        
        $stream = new Stream(new \App\Events\TradeConfirmationEvent($this,$sendOrg));
        $stream->run();

        $stream = new Stream(new \App\Events\TradeConfirmationEvent($this,$receiveOrg));
        $stream->run();        
    }

    public function preFormatted()
    {
        $organisation = $this->resolveOrganisation();
        $is_sender  = $organisation->id == $this->sendUser->organisation_id;
        return [
            'id'                        => $this->id,
            'root_id'                 => $this->getRoot()->id,
            'organisation'              =>  $organisation ? $organisation->title : null,

            'trade_structure_title'     => $this->tradeStructure->title,
            'volatility'                => $this->tradeNegotiation->marketNegotiation->volatility,
            'option_groups'             => $this->optionGroups->map(function($item) use ($is_sender){
                return $item->preFormatted($is_sender);
            })->toArray(),
            'future_groups'             => $this->futureGroups->map(function($item) use ($is_sender){
                return $item->preFormatted($is_sender);
            })->toArray(),
            'fee_groups'                => $this->feeGroups->map(function($item) use ($is_sender){
                return $item->preFormatted($is_sender);
            })->toArray(),
            'request_groups'                => $this->tradeStructureSlug == 'var_swap' ? $this->marketRequest->userMarketRequestGroups->map(function($item) {
                    return $item->preFormatted();
                })->toArray() : null,
            'swap_parties'              => $this->tradeStructureSlug == 'var_swap' ? [
                    'is_offer' => $this->tradeNegotiation->is_offer,
                    'initiate_org' => $this->tradeNegotiation->initiateUser->organisation->title,
                    'recieving_org' => $this->tradeNegotiation->recievingUser->organisation->title,
                ] : null,
            'market_request_id'         => $this->marketRequest->id,
            'market_request_title'      => $this->marketRequest->title,
            
            'market_id'                 => $this->market_id,

            'market_type_id'            => $this->market->market_type_id,

            'state'                     => $this->tradeConfirmationStatus->title,

            'can_interact'              => $this->canInteract(),

            'underlying_id'             => $this->marketRequest->trading_underlying->trading_market_id,
            'underlying_title'          => $this->marketRequest->trading_underlying->title,

            'date'                      => Carbon::now()->format("Y-m-d"),
            
            'traded_at'                 => $this->tradeNegotiation->updated_at,
            'fee'            => $this->calculateFee()
        ];
    }

    public function canInteract()
    {
            $current_org_id =  $this->resolveOrganisationId();
            $is_sender =   $current_org_id == $this->sendUser->organisation_id;
            $is_reciever = $current_org_id == $this->recievingUser->organisation_id;
            $senderStatuses = [1,3];
            $receiverStatuses = [2,5];
            
            if($is_sender)
            {
             return  in_array($this->trade_confirmation_status_id,$senderStatuses);
         }else if($is_reciever)
         {
            return in_array($this->trade_confirmation_status_id,$receiverStatuses);
        }

    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function sendUser()
    {
        return $this->belongsTo('App\Models\UserManagement\User','send_user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function recievingUser()
    {
        return $this->belongsTo('App\Models\UserManagement\User','receiving_user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function sendTradingAccount()
    {
        return $this->belongsTo('App\Models\UserManagement\TradingAccount','send_trading_account_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function receivingTradingAccount()
    {
        return $this->belongsTo('App\Models\UserManagement\TradingAccount','receiving_trading_account_id');
    }

    /**
    * Return relation based of trade_confirmation_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function optionGroups()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmationGroup','trade_confirmation_id')
                ->whereHas('tradeConfirmationGroupType',function($q) {
                    $q->where('title',"Options Group");
                });
    }

    /**
    * Return relation based of trade_confirmation_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function futureGroups()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmationGroup','trade_confirmation_id')
                ->whereHas('tradeConfirmationGroupType',function($q) {
                    $q->where('title',"Futures Group");
                });
    }

    /**
    * Return relation based of trade_confirmation_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function feeGroups()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmationGroup','trade_confirmation_id')
                ->whereHas('tradeConfirmationGroupType',function($q) {
                    $q->where('title',"Fees Group");
                });
    }

   /**
    * Return relation based of trade_confirmation_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmationGroups()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmationGroup',
            'trade_confirmation_id');
    }

    public function scopeMarketType($query, $market_type_id)
    {
        return $query->where(function ($q)  use ($market_type_id) {
            $q->whereHas('market', function ($qq) use ($market_type_id) {
                $qq->where('market_type_id', $market_type_id);
            });
        });
    }

    public function scopeSentByMyOrganisation($query, $organisation_id)
    {
        return $query->where(function ($q)  use ($organisation_id) {
            $q->whereHas('sendUser', function ($qq) use ($organisation_id) {
                $qq->where('organisation_id', $organisation_id);
            });
        });
    }

    public function scopeSentToMyOrganisation($query, $organisation_id)
    {
        return $query->where(function ($q)  use ($organisation_id) {
            $q->whereHas('recievingUser', function ($qq) use ($organisation_id) {
                $qq->where('organisation_id', $organisation_id);
            });
        });
    }



    public function scopeOrganisationInvolved($query, $organistation_id, $operator, $or = false)
    {
        return $query->{($or ? 'orWhere' : 'where')}(function ($tlq)  use ($organistation_id,$operator) {
            $tlq->whereHas('sendUser', function ($query) use ($organistation_id,$operator) {
                $query->where('organisation_id', $operator,$organistation_id);
            })
            ->orWhereHas('recievingUser', function ($query) use ($organistation_id,$operator) {
                $query->where('organisation_id', $operator,$organistation_id);
            });
        });

    }

    public function scopeUserInvolved($query, $user_id, $operator, $or = false)
    {
        return $query->{($or ? 'orWhere' : 'where')}(function ($tlq) use ($user_id,$operator) {
            $tlq->where('send_user_id', $operator,$user_id)
            ->orWhere('receiving_user_id', $operator,$user_id);
        });
    }    

    public function scopeOrgnisationMarketMaker($query, $organistation_id, $or = false)
    {
        return $query->{($or ? 'orWhere' : 'where')}(function ($tlq) use ($organistation_id) {
            $tlq->whereHas('tradeNegotiation', function ($query) use ($organistation_id) {
                $query->whereHas('userMarket', function ($query) use ($organistation_id) {
                    $query->whereHas('user', function ($query) use ($organistation_id) {
                        $query->where('organisation_id', $organistation_id);
                    });
                });
            });
        });
    }

    public function resolveUserMarketRequestItems()
    {
        $resolved_items = [
            "expiration" => array(),
            "strike" => array(),
            "strike_percentage" => array(),
            "nominal" => array(),
            "underlying" => array(),
            "volatility" => array(),
        ];

        foreach ($this->marketRequest->userMarketRequestGroups as $key => $group) {
            foreach ($group->userMarketRequestItems as $key => $item) {
                switch ($item->title) {
                    case 'Expiration Date':
                    case 'Expiration Date 1':
                    case 'Expiration Date 2':
                        $resolved_items["expiration"][] = $item->value;
                        break;
                    case 'Strike':
                        $resolved_items["strike"][] = $item->value;
                        break;
                    case 'Quantity':
                        $resolved_items["nominal"][] = $group->tradable->isStock() ? 'R'.$item->value.'m' : $item->value;
                        break;
                }
            }
            // Add the group underlying
            $resolved_items["underlying"][] = $group->tradable->title;

            // Add the group volatility
            if($group->is_selected) {
                $resolved_items["volatility"][] = $group->volatility->volatility;        
            } else {
                $marketNegotiation = $this->tradeNegotiation->marketNegotiation;
                $resolved_items["volatility"][] = $this->tradeNegotiation->getRoot()->is_offer ? $marketNegotiation->offer :  $marketNegotiation->bid;
            }
        }

        // Resolve Strike percentage
        foreach ($this->tradeConfirmationGroups as $key => $group) {
            $tradable = $group->userMarketRequestGroup->tradable;
            $strike = $group->getOpVal('Strike');
            $spot_price = $group->getOpVal('Spot');

            // Will get into for Markets without a user defined Spot Price
            // Asked by client to exclude
            /*if(!empty($strike) && empty($spot_price)) {
                switch ($tradable->market->title) {
                    case 'TOP40':
                    case 'DTOP':
                    case 'DCAP':
                        $spot_price = $tradable->market->spot_price_ref;
                        break;
                }
            }*/

            // Only Calculate percentage if both Strike and Spot Price is set
            if( !empty($strike) && !empty($spot_price) ) {
                $resolved_items["strike_percentage"][] = round($strike/$spot_price, 2);
            }
        }

        $is_versus_underlying_type = ['option_switch','efp_switch'];
        // Remove duplicates tradables for non versus underlying trade structures
        if(in_array($this->tradeStructureSlug, $is_versus_underlying_type)) {
            $resolved_items["underlying"] = array_unique($resolved_items["underlying"]);
        }

        return $resolved_items;
    }

    public function resolveUnderlying()
    {
        // Get the user market request items
        $underlyings = array();
        $user_market_request_tradables = $this->marketRequest->userMarketRequestTradables;
        foreach ($user_market_request_tradables as $key => $user_market_request_tradable) {
            $underlyings[] = $user_market_request_tradable->stock_id === null ?
                $user_market_request_tradable->market->title : $user_market_request_tradable->stock->code;
        }

        return $underlyings;
    }

    public function setUp($tradeNegotiation)
    {

        $marketNegotiation = $tradeNegotiation->marketNegotiation;
        $marketRequest = $marketNegotiation->userMarket->userMarketRequest;
        $tradeNegotiationRoot = $tradeNegotiation->getRoot();
        $this->fill([
            'send_user_id' => $tradeNegotiationRoot->initiate_user_id,
            'receiving_user_id' => $tradeNegotiationRoot->recieving_user_id,
            'trade_negotiation_id' => $tradeNegotiation->id,
            'stock_id' => null,
            'market_id' => $marketRequest->market_id,
            'trade_structure_id' =>  $marketRequest->trade_structure_id,
            'user_market_request_id' => $marketRequest->id,
            'send_trading_account_id' => null,
            'receiving_trading_account_id' => null,
            'trade_confirmation_status_id' =>1,
        ]);
        $this->save();



        //3 index
        //4 single 
        $groups =  $marketRequest->tradeStructure->tradeStructureGroups()->where('trade_structure_group_type_id',3)->get();
        $ratio = $tradeNegotiation->getTradingRatio();
        foreach($groups as $key => $tradeStructureGroup) {
            $tradeGroupType = TradeConfirmationGroup::where("title",$tradeStructureGroup->title)->first();
            $tradeGroup = $this->tradeConfirmationGroups()->create([
                'trade_structure_group_id' => $tradeStructureGroup->id,
                'trade_confirmation_id' => $this->id,
                "trade_confirmation_group_type_id" => $tradeGroupType->id,
                'user_market_request_group_id' => $marketRequest->userMarketRequestGroups()->where('trade_structure_group_id',$tradeStructureGroup->trade_structure_group_id)->first()->id,
            ]);

            $is_option = $tradeStructureGroup->title == "Options Group" ? 1: 0;
            $is_single_stock = $tradeGroup->userMarketRequestGroup->tradable->isStock();

            $this->setUpItems($is_option,$marketNegotiation,$tradeNegotiation,$tradeStructureGroup,$tradeGroup,$is_single_stock,$ratio);
        }

        return $this;
    } 

    /**
     * Return a simple or query object based on the search term
     *
     * @param string $term
     * @param string $orderBy
     * @param string $order
     * @param array  $filter
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function setUpItems($isOption,$marketNegotiation,$tradeNegotiation,$tradeStructureGroup,$tradeGroup,$is_single_stock,$ratio)
    {
        $delta_one_list = ['efp', 'rolls', 'efp_switch'];

        foreach($tradeStructureGroup->items as $key => $item) {

            $value = null;
            switch ($item->title) {
                case 'is_put':
                case 'is_offer':
                    $value = null;
                    break;
                case 'Volatility':
                    if($tradeGroup->userMarketRequestGroup->is_selected) {
                        $value = $tradeGroup->userMarketRequestGroup->volatility->volatility;
                    } else {
                        $value =  $tradeNegotiation->getRoot()->is_offer ? $marketNegotiation->offer :  $marketNegotiation->bid;
                    }
                    break;
                case 'Gross Premiums':
                case 'Net Premiums':
                case 'Fee Total':
                    $value = null;
                    break;
                case 'Future':
                case 'Future 2':
                    if(in_array($this->tradeStructureSlug, $delta_one_list)) {
                        if($tradeGroup->userMarketRequestGroup->is_selected) {
                            $value = $tradeGroup->userMarketRequestGroup->volatility->volatility;
                        } else {
                            $value = $tradeNegotiation->getRoot()->is_offer ? $marketNegotiation->offer :  $marketNegotiation->bid;
                        } 
                    } else {
                        $value = null;
                    }
                    break;
                case 'Future 1':
                    $value = null;
                    break;
                case 'Contract':
                    if(($isOption && !$is_single_stock) || in_array($this->tradeStructureSlug, $delta_one_list)) {
                        $value = $tradeGroup->userMarketRequestGroup->is_selected 
                        ? round( ($tradeGroup->userMarketRequestGroup->getDynamicItem('Quantity') ) * $ratio, 2)
                        : $tradeNegotiation->quantity; //quantity
                    } else {
                        $value = null;
                    }
                    break;
                case 'Nominal':
                    if($is_single_stock) {
                        // Need to multiply by 1M because the Nomninal is amount per million
                        $value = $tradeGroup->userMarketRequestGroup->is_selected 
                        ? round( ($tradeGroup->userMarketRequestGroup->getDynamicItem('Quantity') * 1000000 ) * $ratio, 2)
                        : $tradeNegotiation->quantity * 1000000;
                    }
                    break;
            }

            if($item->title =="Net Premiums" || $item->title == "Fee Total")
            {
                $tradeGroup->tradeConfirmationItems()->create([
                    'item_id' => $item->id,
                    'title' => $item->title,
                    'value' =>  $value,
                    "is_seller" => false,
                    'trade_confirmation_group_id' => $tradeStructureGroup->id
                ]);

                $tradeGroup->tradeConfirmationItems()->create([
                    'item_id' => $item->id,
                    'title' => $item->title,
                    'value' =>  $value,
                    "is_seller" => true,
                    'trade_confirmation_group_id' => $tradeStructureGroup->id
                ]);
            } else if($item->title == "is_offer") {
                $seller_value = $tradeNegotiation->getIsOfferForOrg($this->sendUser->organisation_id);
                $buyer_value = $tradeNegotiation->getIsOfferForOrg($this->recievingUser->organisation_id); 
                
                if($isOption || in_array($this->tradeStructureSlug, $delta_one_list)) {
                    $seller_is_offer = $tradeGroup->userMarketRequestGroup->is_selected ? !$seller_value : $seller_value;
                    $buyer_is_offer = $tradeGroup->userMarketRequestGroup->is_selected ? !$buyer_value : $buyer_value;
                } else {
                    $seller_is_offer = null;
                    $buyer_is_offer = null;
                }

                $tradeGroup->tradeConfirmationItems()->create([
                    'item_id' => $item->id,
                    'title' => $item->title,
                    'value' =>  $seller_is_offer,
                    "is_seller" => true,
                    'trade_confirmation_group_id' => $tradeStructureGroup->id
                ]);

                $tradeGroup->tradeConfirmationItems()->create([
                    'item_id' => $item->id,
                    'title' => $item->title,
                    'value' =>  $buyer_is_offer,
                    "is_seller" => false,
                    'trade_confirmation_group_id' => $tradeStructureGroup->id
                ]);
            } else if($item->title == "is_offer 1" || $item->title == "is_offer 2") {
                // if BUY roll - buying long further dated leg and selling near dated leg
                // if SELL roll - Selling long further dated leg and buying near dated leg
                $is_offer = $tradeNegotiation->getRoot()->is_offer; // is_offer true means buying

                if($item->title == "is_offer 1") {
                    $sender_is_offer = $is_offer ? false : true;
                    $receiver_is_offer = !$sender_is_offer;
                }

                if($item->title == "is_offer 2") {
                    $sender_is_offer = $is_offer ? true : false;
                    $receiver_is_offer = !$sender_is_offer;
                }           

                $tradeGroup->tradeConfirmationItems()->create([
                    'item_id' => $item->id,
                    'title' => $item->title,
                    'value' =>  $sender_is_offer,
                    "is_seller" => true,
                    'trade_confirmation_group_id' => $tradeStructureGroup->id
                ]);

                $tradeGroup->tradeConfirmationItems()->create([
                    'item_id' => $item->id,
                    'title' => $item->title,
                    'value' =>  $receiver_is_offer,
                    "is_seller" => false,
                    'trade_confirmation_group_id' => $tradeStructureGroup->id
                ]);
            } else if($item->title == "Spot") {
                if($tradeGroup->userMarketRequestGroup->hasSpotPrice()) {
                    $tradeGroup->tradeConfirmationItems()->create([
                        'item_id' => $item->id,
                        'title' => $item->title,
                        "is_seller" => null,
                        'value' =>  $value,
                        'trade_confirmation_group_id' => $tradeStructureGroup->id
                    ]);
                } 

            } else if($item->title == "Nominal") {
                if($is_single_stock) {
                    $tradeGroup->tradeConfirmationItems()->create([
                        'item_id' => $item->id,
                        'title' => $item->title,
                        "is_seller" => null,
                        'value' =>  $value,
                        'trade_confirmation_group_id' => $tradeStructureGroup->id
                    ]);
                } 
            } else if(in_array($this->tradeStructureSlug, $delta_one_list) && ($item->title == "Future" || $item->title == "Future 2")) {
                $tradeGroup->tradeConfirmationItems()->create([
                    'item_id' => $item->id,
                    'title' => $item->title,
                    'value' =>  $value,
                    "is_seller" => false,
                    'trade_confirmation_group_id' => $tradeStructureGroup->id
                ]);

                $tradeGroup->tradeConfirmationItems()->create([
                    'item_id' => $item->id,
                    'title' => $item->title,
                    'value' =>  $value,
                    "is_seller" => true,
                    'trade_confirmation_group_id' => $tradeStructureGroup->id
                ]);
            } else {
                $tradeGroup->tradeConfirmationItems()->create([
                    'item_id' => $item->id,
                    'title' => $item->title,
                    "is_seller" => null,
                    'value' =>  $value,
                    'trade_confirmation_group_id' => $tradeStructureGroup->id
                ]);
            }         
        }
    }

    public function updateGroups($groups, $update_only = null, $update_exclude = null)
    {
        if(!isset($update_only)) {
            $update_only = [];
        }
        if(!isset($update_exclude)) {
            $update_exclude = [];
        }
        foreach ($groups as $group) 
        {
            $groupModel = TradeConfirmationGroup::where('trade_confirmation_group_id',$group['id'])->first();
            if(is_null($groupModel)){
                $groupModel = $this->tradeConfirmationGroups->firstWhere('id',$group['id']);
            }

            foreach ($group['items'] as $item) 
            {
                if( (empty($update_only) || in_array($item['title'], $update_only)) && !in_array($item['title'], $update_exclude) ) {
                    $itemModel = $groupModel->tradeConfirmationItems()->where([
                        'trade_confirmation_group_id' => $groupModel->id,
                        'title'=>$item['title']
                    ])->first();

                    $itemModel->update(
                        ['value'=>$item['value']]
                    );
                }
            }
        }
    }

    public function updateFutureContracts()
    {

    }

    public function setAccount($user,$trading_account)
    {
        if($user->organisation_id == $this->sendUser->organisation_id)
        {
            $this->send_trading_account_id = $trading_account;
        }
        else if($user->organisation_id == $this->recievingUser->organisation_id)
        {
            $this->receiving_trading_account_id = $trading_account;
        }
    }

    public function bookTheTrades()
    {
        $userMarket = $this->marketRequest->chosenUserMarket;

        //need to update for other tradesctruvtures
        $sendIsOffer = $this->resolveItem("is_offer",true);
        $recieverIsOffer = $this->resolveItem("is_offer",false);
        $senderNetPremium = $this->resolveItem("Net Premiums",true);
        $recieverNetPremium = $this->resolveItem("Net Premiums",false);


        $rebatetotal =  config('marketmartial.rebates_settings.rebate_percentage') * ($this->getBrokerageTotal(true) + $this->getBrokerageTotal(false));

        //outright part of the optionGroup
        try {
            DB::beginTransaction();
            // trade trade for sender
            $this->bookedTrades()->create([
                "trading_account_id"        => $this->send_trading_account_id,
                "is_sale"                   => !$sendIsOffer,
                "is_purchase"               => $sendIsOffer,
                "is_rebate"                 => false,
                "is_confirmed"              => false,
                "amount"                    => $senderNetPremium,
                "user_id"                   => $this->send_user_id,
                "trade_confirmation_id"     => $this->id,
                "market_request_id"         => $this->user_market_request_id
            ]);


            $this->bookedTrades()->create([
                "trading_account_id"        => $this->receiving_trading_account_id,
                "is_sale"                   => !$recieverIsOffer ,
                "is_purchase"               => $recieverIsOffer ,
                "is_rebate"                 => false,
                "is_confirmed"              => false,
                "amount"                    => $this->resolveItem("Net Premiums",false),
                "user_id"                   => $this->receiving_user_id,
                "trade_confirmation_id"     => $this->id,
                "market_request_id"         => $this->user_market_request_id
            ]);

            // DELTA ONE's dont have rebates [MM-900]
            if(!in_array($this->marketRequest->trade_structure_slug, ['efp', 'rolls', 'efp_switch'])) {
                Rebate::create([
                    "user_market_request_id"    => $this->user_market_request_id,
                    "user_market_id"            => $userMarket->id,
                    "organisation_id"           => $userMarket->user->organisation_id,
                    "user_id"                   => $userMarket->user_id,
                    "is_paid"                   => false,
                    "trade_confirmation_id"     => $this->id,
                    "trade_date"                => Carbon::now(),
                    "amount"                    => $rebatetotal
                ]);

                $organisation = $userMarket->user->organisation;
                $organisation->notify("rebate_earned","You earned a rebate",true);
                Rebate::notifyOrganisationUpdate($organisation);
            }

            //book the trade

            DB::commit();

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            \Log::error($e);
        }

    }

    public function getBrokerageTotal($is_sender)
    {
        $brokerFee = 0;
        foreach ($this->optionGroups as $group) 
        {
            $isOffer = $group->getOpVal("is_offer",$is_sender);  
            $grossPremium = $group->getOpVal("Gross Premiums");
            $netPremium = $group->getOpVal("Net Premiums",$is_sender);
            $contracts = $group->getOpVal("Contract");
            $brokerFee += abs($this->calcBrokerage($isOffer,$netPremium,$grossPremium,$contracts));
        }

        return  $brokerFee;
    }

    public function calcBrokerage($isOffer,$netPremium,$grossPremium,$contracts)
    {
            //isOffer is Buy
        if($isOffer)
        {
            return ($netPremium - $grossPremium) * $contracts;
        }else
        {
            return ($grossPremium - $netPremium) * $contracts;
        }

    }

    public function resolveItem($title,$isSeller)
    {
        $netPremium = TradeConfirmationItem::whereHas('tradeConfirmationGroup', function ($q) {
            $q->whereHas('tradeConfirmation',function($qq){
                $qq->where('id',$this->id);
            });
        })
        ->where('title',$title)
        ->where('is_seller',$isSeller)
        ->first();

        if($netPremium)
        {
            return $netPremium->value;
        }
    }

    /**
     * Creates a child duplicate including tradeConfirmationGroups and their items
     *
     * @return \App\Models\TradeConfirmations\TradeConfirmation
     */
    public function createChild()
    {
        $child = $this->replicate();
        $child->trade_confirmation_id = $this->id;
        $child->save();

        foreach ($this->tradeConfirmationGroups as $key => $group) {
            $child_group = $group->replicate();
            $child_group->trade_confirmation_id = $child->id;
            $child_group->trade_confirmation_group_id = $group->id;
            $child_group->save();

            foreach ($group->tradeConfirmationItems as $index => $item) {
                $child_item = $item->replicate();
                $child_item->trade_confirmation_group_id = $child_group->id;
                $child_item->save();
            }
        }
        return $child;
    }

    /**
     *  Get source of confirmation
     *   
     */
    public function getRoot()
    {
        $table = $this->table;
        $parentKey = $this->tradeConfirmationParent()->getForeignKey();
        $id = (int)$this->id;
        $confirmation_root = \DB::select("
            SELECT *
                FROM (
                    SELECT @id AS _id, (
                        SELECT @id := $parentKey FROM $table WHERE id = _id
                    ) as parent_id
                    FROM (
                        SELECT @id := $id
                    ) tmp1
                    JOIN $table ON @id IS NOT NULL
                ) parent_struct
                JOIN $table outcome ON parent_struct._id = outcome.id
        ");
        return self::hydrate($confirmation_root)->sortBy('id')->first();
    }

    /**
     *  Resolve the Parent Confirmation that is not an update
     *   
     */
    public function resolveParent()
    {
        $table = $this->table;
        $parentKey = $this->tradeConfirmationParent()->getForeignKey();
        $id = (int)$this->id;
        $parent = DB::select("
            SELECT *
            FROM (
                SELECT @id AS _id, @status as _status, (
                    SELECT @id := trade_confirmation_id FROM $table WHERE id = _id
                ) as parent_id, (
                    SELECT @status := trade_confirmation_status_id FROM $table WHERE id = _id
                ) as parent_status
                FROM (
                    SELECT @id := $id, @status := 0
                ) tmp1
                JOIN $table ON @status IN (0,6,7)
                ORDER BY parent_id ASC
                LIMIT 1
            ) parent_struct
            JOIN $table outcome ON parent_struct.parent_id = outcome.id
        ");

        return self::hydrate($parent)->first();
    }

    /**
     *  Notifies both parties of a trade with the details of the confirmed trade confirmation
     *   
     */
    public function notifyTradingPartyEmails()
    {
        $sending_users_recipients = $this->sendUser->notificationEmails;
        $receiving_users_recipients = $this->recievingUser->notificationEmails;

        $trade_confirmation = $this->fresh()->load([
            'tradeConfirmationGroups'=>function($q)
            {
                $q->with(['tradeConfirmationItems','userMarketRequestGroup.userMarketRequestItems']);
            }
        ]);

        if($sending_users_recipients) {
            try {
                \Notification::send($sending_users_recipients, new TradeConfirmedNotification(
                    $trade_confirmation, 
                    $this->tradeStructureSlug, 
                    $this->sendUser)
                );
            } catch(\Swift_TransportException $e) {
                \Log::error($e);
            }
        }

        if($receiving_users_recipients) {
            try {
                \Notification::send($receiving_users_recipients, new TradeConfirmedNotification(
                    $trade_confirmation, 
                    $this->tradeStructureSlug, 
                    $this->recievingUser)
                );
            } catch(\Swift_TransportException $e) {
                \Log::error($e);
            }
        }
    }

    public function calculateFee()
    {
        // @TODO - change to calculated value, currently hardcoded with test value
        return 58.36;
    }
}