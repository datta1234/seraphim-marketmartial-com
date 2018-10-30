<?php

namespace App\Models\TradeConfirmations;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Broadcast\Stream;
use App\Events\TradeConfirmationEvent;
use Carbon\Carbon;

class TradeConfirmation extends Model
{
    use \App\Traits\ResolvesUser;
    use \App\Traits\CalcuatesForPhases;

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
        "send_user_id",
        "receiving_user_id",
        "trade_negotiation_id",
        "stock_id",
        "market_id",
        "send_trading_account_id",
        "receiving_trading_account_id",
        "trade_confirmation_status_id",
        "trade_structure_id",
        "user_market_request_id"
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
    public function distputes()
    {
        return $this->hasMany('App\Models\TradeConfirmations\Distpute','trade_confirmation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmationParents()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmation','trade_confirmation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmationChildren()
    {
        return $this->belongsTo('App\Models\TradeConfirmations\TradeConfirmation','trade_confirmation_id');
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

    public function notifyConfirmation($organisation,$message)
    {
        $sendOrg = $this->sendUser->organisation;
        $receiveOrg = $this->recievingUser->organisation;

        $organisation->notify("trade_confirmation_store",$message,true);
        
        $stream = new Stream(new TradeConfirmationEvent($this,$sendOrg));
        $stream->run();

        $stream = new Stream(new TradeConfirmationEvent($this, $receiveOrg));
        $stream->run();        
    }

    public function preFormatted()
    {
        $organisation = $this->resolveOrganisation();
        
        return [
            'id'                        => $this->id,
            'organisation'              => rand(1,100),//$organisation ? $organisation->title : null,

            'trade_structure_title'     => $this->tradeStructure->title,
            'volatility'                => $this->tradeNegotiation->marketNegotiation->volatility,
            'option_groups'             => $this->optionGroups->map(function($item){
                return $item->preFormatted();
            })->toArray(),
            'future_groups'           => $this->futureGroups->map(function($item):array{
                return $item->preFormatted();
            })->toArray(),
            'market_request_id'         => $this->marketRequest->id,
            'market_request_title'      => $this->marketRequest->title,
            
            'market_id'                 => $this->market_id,

            'market_type_id'            => $this->market->market_type_id,

            'status_id'                 => $this->trade_confirmation_status_id,

            'can_interact'              => $this->canInteract(),

            'underlying_id'             => $this->marketRequest->underlying->id,
            'underlying_title'          => $this->marketRequest->underlying->title,

            'is_single_stock'           => false, //@todo when doing single stock ensure to update this method;

            'date'                      => Carbon::now()->format("Y-m-d"),
            
            'traded_at'                 => $this->tradeNegotiation->updated_at,
            'is_offer'                  => $this->tradeNegotiation->isOffer(),
        ];
    }

    public function canInteract()
    {
        $current_org_id =  $this->resolveOrganisationId();
        $is_sender =   $current_org_id == $this->sendUser->organisation->id;
        $is_reciever = $current_org_id == $this->recievingUser->organisation->id;
        $senderStatuses = [1,3];
        $receiverStatuses = [2,5];
       
       
       if($is_sender)
       {
           return  in_array($this->trade_confirmation_status_id,$senderStatuses);
       }else if($is_reciever)
       {
            return in_array($this->trade_confirmation_status_id,$senderStatuses);
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
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmationGroup',
            'trade_confirmation_id')->where('is_options',true);
    }

    /**
    * Return relation based of trade_confirmation_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function futureGroups()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmationGroup',
            'trade_confirmation_id')->where('is_options',false);
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
            }
        );
    }

    public function scopeSentByMyOrganisation($query, $organisation_id)
    {
       return $query->where(function ($q)  use ($organisation_id) {
                $q->whereHas('sendUser', function ($qq) use ($organisation_id) {
                    $qq->where('organisation_id', $organisation_id);
                });
            }
        );
    }

    public function scopeSentToMyOrganisation($query, $organisation_id)
    {
       return $query->where(function ($q)  use ($organisation_id) {
                $q->whereHas('recievingUser', function ($qq) use ($organisation_id) {
                    $qq->where('organisation_id', $organisation_id);
                });
            }
        );
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
        ];

        // Get the user market request items
        $user_market_request_items = array();
        $user_market_request_groups = $this->tradeNegotiation->userMarket->userMarketRequest->userMarketRequestGroups;
        foreach ($user_market_request_groups as $key => $user_market_request_group) {
            $user_market_request_items[] = $user_market_request_group->userMarketRequestItems->groupBy(function ($item, $key) {
                return $item->user_market_request_group_id;
            });
        }

        // Set Strike, Strike percentage, expiration dates and nominals
        foreach ($user_market_request_items as $item_groups) {
            foreach ($item_groups as $item_group) {
                foreach ($item_group as $item) {
                    switch ($item->title) {
                        case 'Expiration Date':
                        case 'Expiration Date 1':
                        case 'Expiration Date 2':
                                $resolved_items["expiration"][] = $item->value;
                            break;
                        case 'Strike':
                            $resolved_items["strike"][] = $item->value;
                            if($this->spot_price !== null) {
                                $resolved_items["strike_percentage"][] = round($item->value/$this->spot_price, 2);
                            } else {
                                $resolved_items["strike_percentage"] = null;
                            }
                            break;
                        case 'Quantity':
                                $resolved_items["nominal"][] = $item->value;
                            break;
                    }
                }    
            }   
        }
        return $resolved_items;
    }

    public function resolveMarketStock() {
        // Resolve stock / market
        if($this->stock) {
            return $this->stock->code;
        } else {
            return $this->market->title;
        }
    }

    public function preFormatStats($user = null, $is_Admin = false)
    {   
        $user_market_request_items = $this->resolveUserMarketRequestItems();
        
        $data = [
            "id" => $this->id,
            "updated_at" => $this->updated_at->format('Y-m-d H:i:s'),
            "market" => $this->resolveMarketStock(),
            "structure" => $this->tradeNegotiation->userMarket->userMarketRequest->tradeStructure->title,
            "nominal" =>  $user_market_request_items["nominal"],
            "strike" =>  $user_market_request_items["strike"],
            "expiration" =>  $user_market_request_items["expiration"],
            "strike_percentage" =>  $user_market_request_items["strike_percentage"],
            "volatility" => array(),
        ];
        
        $market_negotiation = $this->tradeNegotiation->marketNegotiation;
        // volatility
        if($market_negotiation->bid_qty) {
            $data["volatility"][] = $market_negotiation->bid_qty;
        }

        if($market_negotiation->offer_qty) {
            $data["volatility"][] = $market_negotiation->offer_qty;
        }

        if($is_Admin) {
            $data["seller"] = $this->sendUser->organisation->title;
            $data["buyer"] = $this->recievingUser->organisation->title;
            return $data;
        }

        if($user === null) {
            $data["status"] = $this->tradeNegotiation->traded ? 'Traded' : 'Not Traded';
            return $data; 
        }

        // Determine direction, state and trader. Priority - My Trade > My Organisation Trade > Market Maker Traded Away
        switch (true) {
            case ($this->send_user_id == $user->id):
                $data["status"] = 'My Trade';
                $data["trader"] = $user->full_name;
                $data["direction"] = 'Sell';
                break;
            case ($this->receiving_user_id == $user->id):
                $data["status"] = 'My Trade';
                $data["trader"] = $user->full_name;
                $data["direction"] = 'Buy';
                break;
            case ($this->sendUser->organisation->id == $user->organisation->id):
                $data["status"] = 'Trade';
                $data["trader"] = $this->sendUser->full_name;
                $data["direction"] = 'Sell';
                break;
            case ($this->recievingUser->organisation->id == $user->organisation->id):
                $data["status"] = 'Trade';
                $data["trader"] = $this->recievingUser->full_name;
                $data["direction"] = 'Buy';
                break;
            case ($this->tradeNegotiation->userMarket->user->organisation->id == $user->organisation->id):
                $data["status"] = 'Market Maker Traded Away';
                $data["trader"] = null;
                $data["direction"] = null;
                break;
            default:
                $data["status"] = null;
                $data["trader"] = null;
                $data["direction"] = null;
                break;
        }

        return $data;
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
    public static function basicSearch($term = "",$orderBy="updated_at",$order='ASC', $filter = null)
    {

        // Search markets
        // @TODO - Change to account for single stocks and match with stock name (instrument)
        $trade_confirmations_query = TradeConfirmation::where( function ($q) use ($term)
        {
            $q->whereHas('market',function($q) use ($term){
                $q->where('title','like',"%$term%");
            });
        });

        // Apply Filters
        if($filter !== null) {
            if(!empty($filter["filter_date"])) {
                $trade_confirmations_query->whereDate('updated_at', $filter["filter_date"]);
            }

            if(!empty($filter["filter_market"])) {
                $trade_confirmations_query->where('market_id', $filter["filter_market"]);
            }

            if(!empty($filter["filter_expiration"])) {
                $trade_confirmations_query->whereHas('tradeNegotiation', function ($query) use ($filter) {
                    $query->whereHas('userMarket', function ($query) use ($filter) {
                        $query->whereHas('userMarketRequest', function ($query) use ($filter) {
                            $query->whereHas('userMarketRequestGroups', function ($query) use ($filter) {
                                $query->whereHas('userMarketRequestItems', function ($query) use ($filter) {
                                    $query->whereIn('title', ['Expiration Date',"Expiration Date 1","Expiration Date 2"])
                                          ->whereDate('value', \Carbon\Carbon::parse($filter["filter_expiration"]));
                                });
                            });
                        });
                    });
                });
            }
        }

        // Apply Ordering
        // @TODO - move this to a separate function
        /*switch ($orderBy) {
            case 'updated_at':
            //dd("hit");
                $trade_confirmations_query->orderBy($orderBy,$order);
                break;
            case 'market':
                $trade_confirmations_query->whereHas('market',function($q) use ($order){
                    $q->orderBy('title',$order);
                });
                //$trade_confirmations_query->orderBy("market_id",$order)
                break;
            case 'structure':
            
                break;
            case 'direction':
            
                break;
            case 'status':
            
                break;
            case 'trader':
            
                break;
            default:
                $trade_confirmations_query->orderBy("updated_at", "ASC");
                break;
        }*/

      return $trade_confirmations_query;
    }


    public function setUp($tradeNegotiation)
    {

        $marketNegotiation = $tradeNegotiation->marketNegotiation;
        $marketRequest = $marketNegotiation->userMarket->userMarketRequest;

        $tradeConfirmation = self::create([
            'send_user_id' => $tradeNegotiation->initiate_user_id,
            'receiving_user_id' => $tradeNegotiation->recieving_user_id,
            'trade_negotiation_id' => $tradeNegotiation->id,
            'stock_id' => null,
            'market_id' => $marketRequest->market_id,
            'trade_structure_id' =>  $marketRequest->trade_structure_id,
            'user_market_request_id' => $marketRequest->id,
            'send_trading_account_id' => null,
            'receiving_trading_account_id' => null,
            'trade_confirmation_status_id' =>1,
        ]);
       


        //3 index
        //4 single 
        $groups =  $marketRequest->tradeStructure->tradeStructureGroups()->where('trade_structure_group_type_id',3)->get();
        foreach($groups as $tradeStructureGroup) {

            $tradeGroup = $tradeConfirmation->tradeConfirmationGroups()->create([
                'trade_structure_group_id'  =>  $tradeStructureGroup->id,
                'trade_confirmation_id'     =>  $tradeConfirmation->id,
                "is_options"                 =>  $tradeStructureGroup->title == "Options Group" ? 1: 0,
                'user_market_request_group_id' => $marketRequest->userMarketRequestGroups()->where('trade_structure_group_id',$tradeStructureGroup->trade_structure_group_id)->first()->id,
            ]);

            $this->setUpItems($tradeGroup->is_options,$marketNegotiation,$tradeNegotiation,$tradeStructureGroup,$tradeGroup);
        }

        return $tradeConfirmation;
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
    private function setUpItems($isOption,$marketNegotiation,$tradeNegotiation,$tradeStructureGroup,$tradeGroup)
    {

                
           foreach($tradeStructureGroup->items as $item) {

            $value = null;
            switch ($item->title) {
                case 'is_offer':
                $value = $tradeNegotiation->is_offer ? 1 : 0;
                break;
                case 'Put':
                $value = null;
                break;
                case 'Call':
                $value = null;
                break;
                case 'Volatility':
                $value =  $tradeNegotiation->is_offer ? $marketNegotiation->offer :  $marketNegotiation->bid;
                break;
                case 'Gross Premiums':
                $value = null;
                break;
                case 'Net Premiums':
                $value = null;
                break;
                case 'Future':
                $value = null;
                break;
                case 'Contract':
                    if(!$isOption)
                    {
                        $value = $tradeNegotiation->quantity; //quantity   
                    }else
                    {
                        $value = null;
                    }
                break;
            }

            if($item->title == "Gross Premiums" || $item->title =="Net Premiums")
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

            }else
            {
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

    public function updateGroups($groups)
    {
        foreach ($groups as $group) 
        {
            $groupModel = $this->tradeConfirmationGroups->firstWhere('id',$group['id']);
            foreach ($group['items'] as $item) 
            {
              $itemModel = $groupModel->tradeConfirmationItems()
              ->where([
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