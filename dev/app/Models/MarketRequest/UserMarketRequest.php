<?php

namespace App\Models\MarketRequest;
use App\Models\MarketRequest\UserMarketRequestItem;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserManagement\Organisation;
use App\Events\UserMarketRequested;
use App\Helpers\Broadcast\Stream;
use App\Notifications\NotifyUserMarketRequestCleared;
use DB;

class UserMarketRequest extends Model
{
    use \App\Traits\ResolvesUser;
    use \App\Traits\ActionListCache;

    /**
     * @property integer $id
     * @property integer $user_id
     * @property integer $trade_structure_id
     * @property integer $chosen_user_market_id
     * @property integer $market_id
     * @property boolean $active
     * @property \Carbon\Carbon $created_at
     * @property \Carbon\Carbon $updated_at
     */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_market_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ["market_id","trade_structure_id","chosen_user_market_id"];

    // protected $dates = [
    //     'created_at'
    // ];

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarketRequestTradables()
    {
        return $this->hasMany(
            'App\Models\MarketRequest\UserMarketRequestTradable',
            'user_market_request_id'
        );
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeStructure()
    {
        return $this->belongsTo('App\Models\StructureItems\TradeStructure','trade_structure_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarketRequestGroups()
    {
        return $this->hasMany(
            'App\Models\MarketRequest\UserMarketRequestGroup',
            'user_market_request_id'
        );
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function user()
    {
        return $this->belongsTo('App\Models\UserManagement\User','user_id');
    }

    /**
     * Return relation based of jse_intergration_id_foreign index
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function userSubscriptions()
    {
        return $this->belongsToMany('App\Models\UserManagement\User', 'user_market_request_user', 'user_market_request_id', 'user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function chosenUserMarket()
    {
        return $this->belongsTo('App\Models\Market\UserMarket','chosen_user_market_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarkets()
    {
        return $this->hasMany('App\Models\Market\UserMarket','user_market_request_id')->orderBy('updated_at', 'desc');
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
    public function tradables()
    {
        return $this->hasMany('App\Models\MarketRequest\UserMarketRequestTradable','user_market_request_id');
    }

    /**
    * Scope for active markets today
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function scopeActive($query)
    {
        // staging/demo is scoping to daily records to prevent clutter
        $scope_to_daily = false;

        return $query->where('active', true)
            ->when($scope_to_daily, function($q) {
                $q->where('created_at', '>', now()->startOfDay());
            });
    }

    /**
    * Scope for active markets today
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function scopePreviousDayTraded($query)
    {
        return $query->whereHas('chosenUserMarket', function($q) {
                $q->traded();
            });
    }

    /**
    * Scope for active markets today
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function scopePreviousDayUntraded($query)
    {
        return $query->whereHas('chosenUserMarket', function($q) {
                $q->untraded();
            });
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function getMarketTitleAttribute()
    {
        return $this->market()->pluck('title')[0];
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function rebates()
    {
        return $this->hasMany('App\Models\Trade\Rebate','user_market_request_id');
    }


    public function getRatioAttribute() {
        $first = null;
        return $this->getDynamicItems('Quantity')->reduce(function($out, $item) use (&$first) {
            if($first == null) {
                $first = floatval($item);
            }
            if($first != $item) {
                $out = true;
            }
            return $out;
        }, false);
    }


    /**
    * Return relation based of _id_foreign index
    * @return \App\Models\Market\UserMarket
    */
    public function createQuote($data)
    {
        try {
            DB::beginTransaction();

            $userMarket = $this->userMarkets()->create(
                collect($data)->only([
                    'user_id',
                ])->toArray()
            );

            // negotiation
            $marketNegotiation = $userMarket
                ->marketNegotiations()
                ->create(
                    collect($data['current_market_negotiation'])->only([
                        "user_id",
                        "user_market_id",
                        "bid",
                        "offer",
                        "offer_qty",
                        "bid_qty",
                        "bid_premium",
                        "offer_premium",
                        "future_reference",
                        "has_premium_calc",

                        // "is_private", // cant on quote
                        "cond_is_repeat_atw",
                        "cond_fok_apply_bid",
                        "cond_fok_spin",
                        "cond_timeout",
                        "cond_is_oco",
                        "cond_is_subject",
                        // "cond_buy_mid", // cant on quote
                        // "cond_buy_best", // cant on quote
                    ])->toArray()
                );

            $userMarket
                ->currentMarketNegotiation()
                ->associate($marketNegotiation)
                ->save();

            if(isset($data['volatilities']) && !empty($data['volatilities'])) {
                $vols = collect($data['volatilities'])->keyBy('group_id');
                $groups = $this->userMarketRequestGroups()->chosen()->get();
                foreach($groups as $group) {
                    $userMarket->volatilities()->create([
                        'user_market_id'    =>  $this->id,
                        'user_market_request_group_id'  =>  $group->id,
                        'volatility'    =>  $vols[$group->id]['value']
                    ]);
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e);
            return false;
        }

        return $userMarket;
    }    


    /**
    * Return pre formatted request for frontend
    * @return \App\Models\MarketRequest\UserMarketRequest
    */
    public function preFormatted()
    {
        // if the market request has been deactivated, we preformat as removed
        if($this->active == false) {
            return [
                "id"        =>  $this->id,
                "market_id" =>  $this->market_id,
                "inactive"  =>  true,
            ];
        }

        $current_org_id =  $this->resolveOrganisationId();

        $interest_org_id = $this->user->organisation_id;
        $is_interest = $interest_org_id == $current_org_id && $current_org_id != null;

        $data = [
            "id"                => $this->id,
            "market_id"         => $this->market_id,
            "chosen_user_market"=> null,
            "quotes"            => [],
            'is_interest'       => $is_interest,
            "is_market_maker"   => false,
            "trade_structure"   => $this->tradeStructure->title,
            "trade_items"       => $this->userMarketRequestGroups
             ->keyBy('tradeStructureGroup.title')
             ->map(function($group) {
                $data = $group->userMarketRequestItems->keyBy('title')->map(function($item) {
                    if($item->type == 'expiration date') {
                        return (new \Carbon\Carbon($item->value))->format("My");
                    }
                    return $item->value;
                });
                $data['id'] = $group->id;
                $data['choice'] = $group->is_selected;
                if($group->tradable) {
                    $data['tradable'] = $group->tradable->preFormatted();
                }
                return $data;
            }),
            "ratio"             => $this->ratio,
            "attributes"        => $this->resolveRequestAttributes(),
            "created_at"         => $this->created_at->format("Y-m-d H:i:s"),
            "updated_at"         => $this->updated_at->format("Y-m-d H:i:s"),
        ];

        $showLevels = $this->isAcceptedState($current_org_id);

        if($showLevels)
        {
            $data["chosen_user_market"] = $this->chosenUserMarket->setOrgContext($this->resolveOrganisation())->preFormattedMarket();
            //market has been chosen and this user is considerd the market maker
            $market_maker_org_id = $this->chosenUserMarket->organisation->id;
            $data['is_market_maker'] = $market_maker_org_id == $current_org_id;

        }else
        {
            $data["quotes"] = $this->userMarkets()->activeQuotes()->when(!$is_interest, function($query) use ($current_org_id) {
                $query->where(function($query) use ($current_org_id) {
                    // Only publicly visible
                    $query->whereHas('currentMarketNegotiation', function($q) {
                        $q->where('is_private', false);
                    });
                    // Or this org is the interest
                    $query->orWhereHas('user',function($q) use ($current_org_id) {
                        $q->where('organisation_id',$current_org_id);
                    });
                });
            })->get()->map(function($item) {
                return $item->setOrgContext($this->resolveOrganisation())->preFormattedQuote(); 
            });
        }

        return $data;
    }

    public function scopePreviousDay($query) {
        return $query->whereBetween('updated_at', [ now()->subDays(1)->startOfDay(), now()->startOfDay() ]);
    }

    
    /**
    * Return pre formatted request for previous day frontend
    * @return \App\Models\MarketRequest\UserMarketRequest
    */
    public function preFormattedPreviousDay($traded = true)
    {
        $current_org_id =  $this->resolveOrganisationId();

        $interest_org_id = $this->user->organisation_id;
        $is_interest = $interest_org_id == $current_org_id && $current_org_id != null;

        // set the attributes
        $attributes = $this->resolveRequestAttributes();
        // $attributes['state'] = (
        //     $traded
        //     ? config('marketmartial.market_request_states.trade-negotiation-pending.interest')
        //     : config('marketmartial.market_request_states.negotiation-open.interest')
        // ); // set to traded or not
        $attributes['action_needed'] = false; // never acton needed

        $data = [
            "id"                => $this->id,
            "market_id"         => $this->market_id,
            "chosen_user_market"=> null,
            "quotes"            => [],
            'is_interest'       => $is_interest,
            "is_market_maker"   => false,
            "trade_structure"   => $this->tradeStructure->title,
            "trade_items"       => $this->userMarketRequestGroups
             ->keyBy('tradeStructureGroup.title')
             ->map(function($group) {
                $data = $group->userMarketRequestItems->keyBy('title')->map(function($item) {
                    if($item->type == 'expiration date') {
                        return (new \Carbon\Carbon($item->value))->format("My");
                    }
                    return $item->value;
                });
                $data['id'] = $group->id;
                $data['choice'] = $group->is_selected;
                if($group->tradable) {
                    $data['tradable'] = $group->tradable->preFormatted();
                }
                return $data;
            }),
            "ratio"             => $this->ratio,
            "attributes"        => $attributes,
            "created_at"         => $this->created_at->format("Y-m-d H:i:s"),
            "updated_at"         => $this->updated_at->format("Y-m-d H:i:s"),
        ];

        $showLevels = $this->isAcceptedState($current_org_id);

        if($showLevels)
        {
            $data["chosen_user_market"] = $this->chosenUserMarket->setOrgContext($this->resolveOrganisation())->preFormattedPreviousDay();
            //market has been chosen and this user is considerd the market maker
            $market_maker_org_id = $this->chosenUserMarket->organisation->id;
            $data['is_market_maker'] = $market_maker_org_id == $current_org_id;

        }else
        {
            $data["quotes"] = $this->userMarkets()->previousDay()->activeQuotes()->when(!$is_interest, function($query) use ($current_org_id) {
                $query->where(function($query) use ($current_org_id) {
                    // Only publicly visible
                    $query->whereHas('currentMarketNegotiation', function($q) {
                        $q->where('is_private', false);
                    });
                    // Or this org is the interest
                    $query->orWhereHas('user',function($q) use ($current_org_id) {
                        $q->where('organisation_id',$current_org_id);
                    });
                });
            })->get()->map(function($item) {
                return $item->setOrgContext($this->resolveOrganisation())->preFormattedQuote(); 
            });
        }

        return $data;
    }


    private $authedUserMarketData = null;
    public function getAuthedUserMarketAttribute() {
        if($this->authedUserMarketData == null) {
            $this->authedUserMarketData = $this->userMarkets()->whereHas('user', function($q) {
                $q->where('organisation_id',$this->resolveOrganisationId());
            })->orderBy('updated_at', 'DESC')
            ->with('currentMarketNegotiation')
            ->first();
        }
        return $this->authedUserMarketData;
    }

    public function notifyRequested($organisations = [], $messages = null)
    {
        $organisations = count($organisations) > 0 ? $organisations : Organisation::verifiedCache();
        
        foreach ($organisations  as $organisation) 
        {
            \Log::info("Notifying Org ".$organisation->id);
            $stream = new Stream(new UserMarketRequested($this,$organisation));
            $stream->run();
        }

        // always send to admin channel
        \Log::info("Notifying Admin");
        $stream = new Stream(new UserMarketRequested($this,"admin"));
        $stream->run();
    }

    /*
    *for the interest show them the levels as son he accepts but for market maker and others only once levels have been sent by the interest
    */
    public function isAcceptedState($current_org_id)
    {
        $interest_org_id = $this->user->organisation->id;
        if($this->chosenUserMarket != null)
        {

            $is_interest = $interest_org_id == $current_org_id;
            $marketCount = $this->chosenUserMarket->marketNegotiations()->withTrashed()->count();

            if($is_interest){
                return  $marketCount > 0;
            }else{
                // quote same org as interest - self made market, open to all be default
                if($this->chosenUserMarket->user->organisation_id == $interest_org_id) {
                    return $marketCount > 0;
                }
                return $marketCount > 0;
            }

        }   
    }



    public function canNegotiate($current_org_id)
    {

        $interest_org_id = $this->user->organisation->id;
        if($this->chosenUserMarket != null)
        {
            $marketCount = $this->chosenUserMarket->marketNegotiations()->count();
            if($marketCount == 1)
            {
                return $interest_org_id == $current_org_id;
            }else
            {
                 $lastNegotiation = $this->chosenUserMarket->lastNegotiation;
                  return $this->chosenUserMarket->marketNegotiations()
                                ->lastNegotiation()
                                ->where(function($q) use ($current_org_id){
                                        $q->organisationInvolved($current_org_id);
                                        // $q->orWhere(function($q) use ($current_org_id){
                                        //     $q->whereHas('marketNegotiationParent',function($q) use ($current_org_id) {
                                        //         $q->organisationInvolved($current_org_id);  
                                        //     });
                                        // });

                                    $q->organisationInvolvedOrCounters($current_org_id);
                    })->where('market_negotiations.id',$lastNegotiation->id)->exists();   
            } 
        } 
    }
    
    /*
    *market is either open after a traded market or spin following each other
    */
    public function openToMarket()
    {

        if($this->chosenUserMarket != null)
        {
            $lastNegotiation = $this->chosenUserMarket->lastNegotiation;

            if(!is_null($lastNegotiation))
            {       
               //after working the balance and the market negotiation,
                if(
                    $lastNegotiation->marketNegotiationParent &&
                    $lastNegotiation->marketNegotiationParent->isTraded() && 
                    // $lastNegotiation->marketNegotiationParent->user->organisation_id == $lastNegotiation->user->organisation_id &&
                    !$lastNegotiation->isTrading()
                )
                {
                    return true;
                }
          
                if($lastNegotiation->isTraded())
                {
                    return true;
                }

                //@TODO @alex market should be open if current is killled and 
                if($lastNegotiation->isFok() && 
                    $lastNegotiation->is_killed == true && 
                    $lastNegotiation->cond_fok_spin == true && 
                    $lastNegotiation->is_repeat == true &&
                    $lastNegotiation->is_private == false
                ) 
                {
                    return true;
                }

                // negotiation history exists
                if(!is_null($lastNegotiation->marketNegotiationParent)) {
                    // open if the last one is killed but isnt a fill


                    //if the last market has been traded open the whole market
                    if($lastNegotiation->isTraded())
                    {
                        return true;
                    }

                    //@TODO @alex not sure why this has to be done
                    if($lastNegotiation->isFok() && 
                        $lastNegotiation->is_killed == true && 
                        $lastNegotiation->is_repeat == false &&
                        $lastNegotiation->is_private == false
                    ) {
                        return true;
                    }
                    
                    if($lastNegotiation->isImprovedRepeatATW()) {
                        return true;
                    }


                    // @TODO: THis is pending discussion with MM around how to handle 'open' markets where private conditions are applied
                    // if there is a private condition applied when the market is open, it should stay open
                    // if(
                    //     $lastNegotiation->isMeetInMiddle()
                    // ) {
                    //     if($lastNegotiation->marketNegotiationParent->marketNegotiationParent) {
                    //         // parent and parents parent
                    //         return $lastNegotiation->marketNegotiationParent->is_repeat 
                    //             && $lastNegotiation->marketNegotiationParent->marketNegotiationParent->is_repeat
                    //     }
                    // }

                    //when spin and parent is spin
                    return $lastNegotiation->is_repeat && $lastNegotiation->marketNegotiationParent->is_repeat;
                } else {

                    // there should only be one - is it same org = open
                    $marketCount = $this->chosenUserMarket->marketNegotiations()->withTrashed()->count();
                    $interest_org_id = $this->user->organisation_id;
                    $market_maker_org_id = $this->chosenUserMarket->user->organisation_id;
                    if($marketCount == 1 && $interest_org_id == $market_maker_org_id) {
                        return true; 
                    }
                }

            }else
            {
                // @TODO: this is breaking the initial levels being set.
                return is_null($lastNegotiation->marketNegotiationParent);
            }
            
            // closed to self prevention
            if(
                ( $lastNegotiation->user && $lastNegotiation->counterUser ) 
                && ( $lastNegotiation->user->organisation_id == $lastNegotiation->counterUser->organisation_id )
            ) {
                return true;
            }
        }

        return false;
    }

    // public function lastTradeNegotiationIsUnTraded()
    // {
    //     if(!is_null($this->chosenUserMarket))
    //     {
    //         $lastNegotiation = $this->chosenUserMarket->lastNegotiation;
    //         return !is_null($lastNegotiation) && !is_null($lastNegotiation->lastTradeNegotiation) && !$lastNegotiation->lastTradeNegotiation->traded;  
    //     }
    // }

     public function lastTradeNegotiationIsTraded()
    {
        if(!is_null($this->chosenUserMarket))
        {
            $lastNegotiation = $this->chosenUserMarket->lastNegotiation;
            // if($lastNegotiation->lastTradeNegotiation)
            // {
            // }else
            // {

            // }

            return !is_null($lastNegotiation) && !is_null($lastNegotiation->lastTradeNegotiation) && $lastNegotiation->lastTradeNegotiation->traded;  
        }
    }


    //need a method for trade at best
    public function isTradeAtBestOpen()
    {
        if($this->chosenUserMarket && $this->chosenUserMarket->lastNegotiation)
        {
            return $this->chosenUserMarket->lastNegotiation->isTradeAtBestOpen();   
        }
        return false;
    }

    public function getStatus($current_org_id)
    {
        //method also used inside policies so be aware when updating
        $hasQuotes          =  $this->userMarkets != null;
        $acceptedState      =  $hasQuotes ?  $this->isAcceptedState($current_org_id) : false;
        $marketOpen         =  $acceptedState ? $this->openToMarket() : false;
        
        // conditions
        $is_fok             =  $acceptedState ? $this->chosenUserMarket->lastNegotiation->isFoK() : false;
        $is_private         =  $is_fok ? $this->chosenUserMarket->lastNegotiation->is_private : false;
        $is_killed          =  $is_private ? $this->chosenUserMarket->lastNegotiation->is_killed == true : false;
        $is_trade_at_best   =  $acceptedState ? $this->chosenUserMarket->lastNegotiation->isTradeAtBestOpen() : false;

        // trading
        $needsBalanceWorked =  $this->chosenUserMarket ? $this->chosenUserMarket->needsBalanceWorked() : false;
        $is_trading         =  $this->chosenUserMarket ? $this->chosenUserMarket->isTrading() : false;
        $lastTraded         =  $this->lastTradeNegotiationIsTraded();

        $balance_worked_no_cares = ($this->chosenUserMarket 
            && $this->chosenUserMarket->lastNegotiation
            && $this->chosenUserMarket->lastNegotiation->lastTradeNegotiation) ? $this->chosenUserMarket->lastNegotiation->lastTradeNegotiation->no_cares : false;
        
        /*
        * check if the current is true and next is false to create a cascading virtual state effect
        */
        // quote
        if(!$hasQuotes)
        {
            return "request";
        }
        // needsBalanceWorked but no cares applied
        elseif($needsBalanceWorked && $balance_worked_no_cares)
        {
            return 'negotiation-open';
        }
        // trading
        elseif($lastTraded && $needsBalanceWorked)
        {
            return 'trade-negotiation-balance';
        }
        elseif($marketOpen && $is_trade_at_best && !$is_trading && !$lastTraded)
        {
            return 'trade-negotiation-open';
        }
        elseif(!$marketOpen && $is_trading && !$lastTraded)
        {
            return 'trade-negotiation-pending';
        }
        // @TODO - state when new trade happens on a market that has already traded 
        elseif($marketOpen && $is_trading && !$lastTraded) // Checks to check if new trade is happening
        {
            // @TODO - figure out what state if not a new one is required.
            return 'trade-negotiation-pending';
        }
        // negotiation
        elseif($hasQuotes && !$acceptedState)
        {
            return "request-vol";
        }
        elseif($acceptedState && !$marketOpen && !$is_trading)
        {
            return 'negotiation-pending';
        }
        elseif($acceptedState && $marketOpen && !$is_trading )
        {
            return 'negotiation-open';
        }
        
    }

    
    public function getCurrentUserRoleInRequest($current_org_id, $interest_org_id,$market_maker_org_id)
    {

        // make sure to handle null organisations as false
        $marketRequestRoles = ["other"];
        if($interest_org_id == $current_org_id && $current_org_id  != null && $market_maker_org_id != $interest_org_id)
        {
            $marketRequestRoles = ["interest"];
        }
        else if ($market_maker_org_id == $current_org_id && $current_org_id  != null && $market_maker_org_id != $interest_org_id) 
        {
            $marketRequestRoles = ["market_maker"];
        }
        else if($market_maker_org_id == $current_org_id  && $interest_org_id == $current_org_id && $current_org_id  != null)
        {
            $marketRequestRoles = ["market_maker","interest"];
        }

        return $marketRequestRoles;
    }

    public function getCurrentUserRoleInMarketNegotiation($marketRequestRoles,$current_org_id)
    {



        if($this->chosenUserMarket != null)
        {
            $lastNegotiation = $this->chosenUserMarket->lastNegotiation;
            $marketNegotiationRoles = ["other"];

            if($this->chosenUserMarket->marketNegotiations()->count() == 1 && in_array('interest', $marketRequestRoles))
            {
                $marketNegotiationRoles = ["counter"];
            }else
            {
                 $negotiator =  $this->chosenUserMarket->marketNegotiations()->where(function($query) use ($current_org_id){
                        $query->organisationInvolved($current_org_id);
                })->where('market_negotiations.id',$lastNegotiation->id)->exists();


                 // $negotiator = $lastNegotiation->user->organisation_id == $current_org_id;

                $counter =  $this->chosenUserMarket->marketNegotiations()->where(function($query) use ($current_org_id){
                        $query->whereHas('counterUser',function($q) use ($current_org_id){
                            $q->where("organisation_id",$current_org_id);
                        });
                })->where('market_negotiations.id',$lastNegotiation->id)->exists();
                // $counter = $lastNegotiation->counterUser->organisation_id == $current_org_id;

                if( $negotiator )
                {
                    $marketNegotiationRoles = ["negotiator"];
                }

                if($counter)
                {
                    $marketNegotiationRoles = ["counter"];
                }
            }           
            return $marketNegotiationRoles;  
        }
    }

    public function getCurrentUserRoleInTradeNegotiation($current_org_id)
    {
        if(!is_null($this->chosenUserMarket))
        {
            $tradeNegotiationRoles = ["other"];


            $tradeNegotiation = $this->chosenUserMarket->tradeNegotiations()->latest()->first();
            if(!is_null($tradeNegotiation))
            {
                if($tradeNegotiation->initiateUser->organisation_id == $current_org_id)
                {
                    $tradeNegotiationRoles = ["negotiator"];
                }

                if($tradeNegotiation->recievingUser->organisation_id == $current_org_id)
                {
                    $tradeNegotiationRoles = ["counter"];
                }
            }  
            return $tradeNegotiationRoles;
        }
    }

    /**
     * resolve the attributes in accordence to the current users organisation and relation to the market request
     *
     * @return Array
     */
    private function resolveRequestAttributes()
    {

        $current_org_id =  $this->resolveOrganisationId();
        $interest_org_id = $this->user->organisation->id;
        $market_maker_org_id = !is_null($this->chosenUserMarket) ? $this->chosenUserMarket->organisation->id : null;
        $state = $this->getStatus($current_org_id,$interest_org_id);
        \Log::info("STATE: ".$state);

        $marketRequestRoles = $this->getCurrentUserRoleInRequest($current_org_id, $interest_org_id,$market_maker_org_id);        
        $marketNegotiationRoles = $this->getCurrentUserRoleInMarketNegotiation($marketRequestRoles,$current_org_id);
        
        $tradeNegotiationRoles = $this->getCurrentUserRoleInTradeNegotiation($current_org_id);



        $attributes = [
            'state'         => config('marketmartial.market_request_states.default'), // default state set first
            'bid_state'     => "",
            'offer_state'   => "",
            'action_needed' => ""
        ];
 
        switch ($state) {
            case "request":
                if(in_array("interest",$marketRequestRoles)) {
                    $attributes['state'] = config('marketmartial.market_request_states.request.interest');
                } else {
                    $attributes['state'] = config('marketmartial.market_request_states.request.other');
                }
            break;
            case "request-vol":
                if(in_array("interest",$marketRequestRoles)) {
                    $attributes['state'] = config('marketmartial.market_request_states.request-vol.interest');
                } else {
                    $attributes['state'] = config('marketmartial.market_request_states.request-vol.other');
                }
            break;
            case "negotiation-pending":
                if(in_array('negotiator',$marketNegotiationRoles)){
                    $attributes['state'] = config('marketmartial.market_request_states.negotiation-pending.negotiator');
               
                }else if(in_array('counter', $marketNegotiationRoles)){
                    $attributes['state'] = config('marketmartial.market_request_states.negotiation-pending.counter');
               
                }else{
                    $attributes['state'] = config('marketmartial.market_request_states.negotiation-pending.other');
                }

            break;
            case "negotiation-open":
                if(in_array('interest',$marketRequestRoles)){
                    $attributes['state'] = config('marketmartial.market_request_states.negotiation-open.interest');
                }else if(in_array('market_maker', $marketRequestRoles)){
                    $attributes['state'] = config('marketmartial.market_request_states.negotiation-open.market_maker');
                }else{
                    $attributes['state'] = config('marketmartial.market_request_states.negotiation-open.other');
                }
            break;
            case "trade-negotiation-open":
                
                if(in_array('negotiator',$tradeNegotiationRoles)){
                    $attributes['state'] = config('marketmartial.market_request_states.trade-negotiation-open.negotiator');
                }else if(in_array('counter', $tradeNegotiationRoles)){
                    $attributes['state'] = config('marketmartial.market_request_states.trade-negotiation-open.counter');
                }else{
                    $attributes['state'] = config('marketmartial.market_request_states.trade-negotiation-open.other');
                }
            break;
             case "trade-negotiation-pending":
                if(in_array('negotiator',$tradeNegotiationRoles)){
                    $attributes['state'] = config('marketmartial.market_request_states.trade-negotiation-pending.negotiator');
                }else if(in_array('counter', $tradeNegotiationRoles)){
                    $attributes['state'] = config('marketmartial.market_request_states.trade-negotiation-pending.counter');
                }else{
                    $attributes['state'] = config('marketmartial.market_request_states.trade-negotiation-pending.other');
                }
            break;
            case "trade-negotiation-balance":
                if(in_array('negotiator',$tradeNegotiationRoles)){
                    $attributes['state'] = config('marketmartial.market_request_states.trade-negotiation-balance.negotiator');
                }else if(in_array('counter', $tradeNegotiationRoles)){
                    $attributes['state'] = config('marketmartial.market_request_states.trade-negotiation-balance.counter');
                }else{
                    $attributes['state'] = config('marketmartial.market_request_states.trade-negotiation-balance.other');
                }
            break;
        }


        $authedUserMarket = $this->authedUserMarket;
        /*
        *   BID / OFFER states
        */
        $attributes['bid_state'] = $authedUserMarket && $authedUserMarket->currentMarketNegotiation && $authedUserMarket->currentMarketNegotiation->bid ? 'action' : '';
        $attributes['offer_state'] = $authedUserMarket && $authedUserMarket->currentMarketNegotiation && $authedUserMarket->currentMarketNegotiation->offer ? 'action' : '';

        /*
        *   Action needed (Alert)
        */
        $needs_action = $this->getAction($this->resolveOrganisationId(), $this->id);
        $attributes['action_needed'] = $needs_action == null ? false : $needs_action;        
    
        return $attributes;
    }

    /**
     * return a trade item singleton by key
     *
     * @return Mixed
     */
    public function getDynamicItem($attr)
    {
        $item = UserMarketRequestItem::whereHas('userMarketRequestGroups', function ($q) {
                $q->whereHas('userMarketRequest',function($qq){
                    $qq->where('id',$this->id);
                });
            })
        ->where('title',$attr)
        ->first();
        if($item)
        {
            switch ($item->type) {
                case 'double':
                    return floatval($item->value);
                    break;
                default:
                    return $item->value;
                    break;
            }
        }else
        {
            return null;
        }
    }

    /**
     * return trade item(s) by key
     *
     * @return Mixed
     */
    public function getDynamicItems($attr, $return_objects = false)
    {
        $query = UserMarketRequestItem::whereHas('userMarketRequestGroups', function ($q) {
            $q->whereHas('userMarketRequest',function($qq){
                $qq->where('id',$this->id);
            });
        });

        if(!is_array($attr))
        {
            $query = $query->where('title',$attr);
        }else
        {
            $query = $query->whereIn('title',$attr); 
        }

        // return objects
        if($return_objects == true) {
            return $query->get();
        }

        return $query->get()
        ->map(function($item) {
            return $item->value;
        });   
    }

    /**
     * get the underlying market
     *
     * @return \App\Models\StructureItems\Market
     */
    public function getUnderlyingAttribute()
    {
        //@TODO for sigle stock set up the relations and update method with tradeables
        return $this->market;
    }

    /**
     * Get string summary of the market request, tradables, expiries strikes etc
     *
     * @return String
     */
    public function getSummary() {
        // risky:       [underlying] [exp date][RISKY] [strike1ch / strike2ch]
        // calendar:    [underlying] [exp date1]vs[exp date] [CALENDAR][strike1ch / strike2ch]
        // fly:         [underlying] [exp date][FLY] [strike1ch / strike2 / strike3ch]
        // outright:    [underlying] [exp date][structure][strike]
        switch($this->trade_structure_slug) {
            case 'risky':
                $exp_date = "Expiration Date";
                $strike = "Strike";
                $groups = $this->userMarketRequestGroups;
                return implode(" ", [
                    $this->market->title,
                    \Carbon\Carbon::parse($this->getDynamicItem($exp_date))->format("My"),
                    "RISKY",
                    $groups[0]->getDynamicItem($strike).($groups[0]->is_selected ? 'ch' : ''),
                    "/",
                    $groups[1]->getDynamicItem($strike).($groups[1]->is_selected ? 'ch' : ''),
                ]);
            break;
            case 'calendar':
                $exp_date = "Expiration Date";
                $strike = "Strike";
                $groups = $this->userMarketRequestGroups;
                return implode(" ", [
                    $this->market->title,
                    \Carbon\Carbon::parse($groups[0]->getDynamicItem($exp_date))->format("My"),
                    "vs",
                    \Carbon\Carbon::parse($groups[1]->getDynamicItem($exp_date))->format("My"),
                    "CALENDAR",
                    $groups[0]->getDynamicItem($strike).($groups[0]->is_selected ? 'ch' : ''),
                    "/",
                    $groups[1]->getDynamicItem($strike).($groups[1]->is_selected ? 'ch' : ''),
                ]);
            break;
            case 'fly':
                $exp_date = "Expiration Date";
                $strike = "Strike";
                $groups = $this->userMarketRequestGroups;
                return implode(" ", [
                    $this->market->title,
                    \Carbon\Carbon::parse($this->getDynamicItem($exp_date))->format("My"),
                    "FLY",
                    $groups[0]->getDynamicItem($strike).($groups[0]->is_selected ? 'ch' : ''),
                    "/",
                    $groups[1]->getDynamicItem($strike).($groups[1]->is_selected ? 'ch' : ''),
                    "/",
                    $groups[2]->getDynamicItem($strike).($groups[2]->is_selected ? 'ch' : ''),
                ]);
            break;
            case 'option_switch':
                $exp_date = "Expiration Date";
                $strike = "Strike";
                $groups = $this->userMarketRequestGroups;
                return implode(" ", [
                    $this->market->title,
                    \Carbon\Carbon::parse($groups[0]->getDynamicItem($exp_date))->format("My"),
                    "vs",
                    \Carbon\Carbon::parse($groups[1]->getDynamicItem($exp_date))->format("My"),
                    "OPTION SWITCH",
                    $groups[0]->getDynamicItem($strike).($groups[0]->is_selected ? 'ch' : ''),
                    "/",
                    $groups[1]->getDynamicItem($strike).($groups[1]->is_selected ? 'ch' : ''),
                ]);
            break;
            case 'efp_switch':
                $exp_date = "Expiration Date";
                $strike = "Strike";
                $groups = $this->userMarketRequestGroups;
                return implode(" ", [
                    $this->market->title,
                    \Carbon\Carbon::parse($groups[0]->getDynamicItem($exp_date))->format("My"),
                    "vs",
                    \Carbon\Carbon::parse($groups[1]->getDynamicItem($exp_date))->format("My"),
                    "EFP SWITCH",
                ]);
            break;
            case 'efp':
                $exp_date = "Expiration Date";
                return implode(" ", [
                    $this->market->title,
                    \Carbon\Carbon::parse($this->getDynamicItem($exp_date))->format("My"),
                    "EFP"
                ]);
            break;
            case 'rolls':
                $exp_date_1 = "Expiration Date 1";
                $exp_date_2 = "Expiration Date 2";
                $strike = "Strike";
                return implode(" ", [
                    $this->market->title,
                    \Carbon\Carbon::parse($this->getDynamicItem($exp_date_1))->format("My"),
                    "vs",
                    \Carbon\Carbon::parse($this->getDynamicItem($exp_date_2))->format("My"),
                    strtoupper($this->tradeStructure->title)
                ]);
            break;
            case 'outright':
                $exp_date = "Expiration Date";
                $strike = "Strike";
                return implode(" ", [
                    $this->market->title,
                    \Carbon\Carbon::parse($this->getDynamicItem($exp_date))->format("My"),
                    strtoupper($this->tradeStructure->title),
                    $this->getDynamicItem($strike)
                ]);
            break;
            case 'var_swap':
                $exp_date = "Expiration Date";
                $cap = 'Cap';
                $capVal = $this->getDynamicItem($cap);
                return implode(" ", [
                    $this->market->title,
                    \Carbon\Carbon::parse($this->getDynamicItem($exp_date))->format("My"),
                    strtoupper($this->tradeStructure->title),
                    ( $capVal == 0 ? "(Uncapped)" : "(".$capVal."x Cap)" )
                ]);
            break;
        }
    }

    /**
     * get computer readable slug of trade structure. this should be used as the unique identifier
     *
     * @return String
     */
    public function getTradeStructureSlugAttribute() {
        switch($this->trade_structure_id) {
            case 1:
                return 'outright';
            break;
            case 2:
                return 'risky';
            break;
            case 3:
                return 'calendar';
            break;
            case 4:
                return 'fly';
            break;
            case 5:
                return 'option_switch';
            break;
            case 6:
                return 'efp';
            break;
            case 7:
                return 'rolls';
            break;
            case 8:
                return 'efp_switch';
            break;
            case 9:
                return 'var_swap';
            break;
        };
    }

    /**
     * Notify subscribed users and removes subscription,
     *
     * @return void
     */
    public function notifySubscribedUsers() {
        // market has cleared notify subscribed user
        // 1. Get all the users subscribed to the user_market_request.
        $users = $this->userSubscriptions;
        if(count($users) > 0) {
            $user_market_request_id = $this->id;
            // 2. Fire Notification for each user's organisation.
            $users->each(function ($item, $key) use ($user_market_request_id) {
                $this->setAction(
                    $item->organisation->id,
                    $user_market_request_id,
                    true
                );
            });
            // 3. Send User email of market cleared.
            try {
                \Notification::send($users, new NotifyUserMarketRequestCleared($this));
                $this->userSubscriptions()->detach($this->userSubscriptions->pluck("id"));
            } catch(\Swift_TransportException $e) {
                Log::error($e);
            }
        }
    }

    /**
     * get the messages for logging of activity
     *
     * @param string $context
     * @return array<string>
     */
    public function getLogMessages($context = "changed", $userString)
    {
        if($context == "created") {
            return [
                $userString." created ".$this->getHumanizedLabel()." ".$this->market->title." ".$this->tradeStructure->title
            ];
        }
        return false;
    }

    /**
     * get the human readable representation for this model
     *
     * @return string
     */
    public function getHumanizedLabel()
    {
        return "Market Request";
    }

}
