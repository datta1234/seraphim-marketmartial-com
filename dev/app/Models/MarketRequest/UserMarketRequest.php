<?php

namespace App\Models\MarketRequest;
use App\Models\MarketRequest\UserMarketRequestItem;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserManagement\Organisation;
use App\Models\TradeConfirmations\TradeConfirmationGroup;
use App\Models\Trade\TradeNegotiation;
use App\Models\TradeConfirmations\TradeConfirmation;
use App\Models\UserManagement\User;
use App\Events\UserMarketRequested;
use App\Helpers\Broadcast\Stream;
use App\Notifications\NotifyUserMarketRequestCleared;
use DB;

class UserMarketRequest extends Model
{
    use \App\Traits\ResolvesUser;
    use \App\Traits\ActionListCache;
    use \App\Traits\ResolveTradeStructureSlug;
    use \App\Traits\ScopesToPreviousDay;

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
    public function tradingGroup()
    {
        return $this->hasOne(
            'App\Models\MarketRequest\UserMarketRequestGroup',
            'user_market_request_id'
        )->where('is_selected', false);
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
    public function tradeConfirmations()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmation','user_market_request_id');
    }

    /**
    * Scope for active markets today
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function scopeActive($query)
    {
        // staging/demo is scoping to daily records to prevent clutter
        $scope_to_daily = true;

        return $query->where(function($q) use ($scope_to_daily) {
            $q->when(!$scope_to_daily, function($q) {
                $q->where('active', true);
            });
            $q->when($scope_to_daily, function($q) {
                $q->where(function($q) {
                    $q->where('active', true);
                    $q->where('updated_at', '>', now()->startOfDay());
                });
                $q->orWhere(function($q) {
                    $q->where('active', true);
                    $q->where(\DB::raw('(
                        select `trade_sub`.`traded` as `trade_sub_traded`
                        from `user_markets` 
                        left join (
                            select *
                            from `market_negotiations` 
                            where `market_negotiations`.`deleted_at` is null
                            order by `updated_at` desc 
                        ) `neogitation_sub`
                        on `user_markets`.`id` = `neogitation_sub`.`user_market_id` 
                        left join (
                            select * 
                            from `trade_negotiations`
                            order by `updated_at` desc 
                        ) `trade_sub`
                        on `neogitation_sub`.`id` = `trade_sub`.`market_negotiation_id` 
                        where `user_market_requests`.`chosen_user_market_id` = `user_markets`.`id` 
                        and `user_markets`.`deleted_at` is null
                        order by `neogitation_sub`.`updated_at` desc, `trade_sub`.`updated_at` desc
                        limit 1
                    )'), 0);
                });
            });
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
        return $query->whereDoesntHave('chosenUserMarket', function($q) {
                $q->traded();
            })->orWhereDoesntHave('chosenUserMarket');// include quote phases
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

    public function marketDefaultQuantity($tradable) {
        if($this->tradeStructureSlug == 'var_swap') {
            $default = config('marketmartial.thresholds.var_swap_quantity');
        } else {
            $default = $tradable->isStock() ? config('marketmartial.thresholds.stock_quantity') : config('marketmartial.thresholds.index_quantity.'.$tradable->market_id);
        }

        return empty($default) ? config('marketmartial.thresholds.quantity') : $default;
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
            "created_at"        => $this->created_at->format("Y-m-d H:i:s"),
            "updated_at"        => $this->updated_at->format("Y-m-d H:i:s"),
        ];

        // default quantity should be from the quantity on group
        $default_group = $this->userMarketRequestGroups->first(function ($value, $key) {
            return $value->is_selected == false;
        });
        $quantity = $default_group->getDynamicItem("Quantity");
        $data["default_quantity"] = $quantity ? $quantity : $this->marketDefaultQuantity($default_group->tradable);

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

        // admin needs to see who owns what
        if($this->isAdminContext()) {
            $data['user'] = $this->user->full_name;
            $data['org'] = $this->user->organisation->title;
        }
        return $data;
    }

    // moved to Trait \App\Traits\ScopesToPreviousDay
    // public function scopePreviousDay($query)

    
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
     * market is either open after a traded market or spin following each other
     *  or a market negotiation has been killed
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

                if($lastNegotiation->isKilled())
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

    public function lastTradeNegotiationIsKilled()
    {
        if(!is_null($this->chosenUserMarket))
        {
            $lastNegotiation = $this->chosenUserMarket->lastNegotiation;
            return !is_null($lastNegotiation) && !is_null($lastNegotiation->lastTradeNegotiation) && $lastNegotiation->lastTradeNegotiation->trade_killed;  
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
        $lastKilled         =  $this->lastTradeNegotiationIsKilled();

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
        elseif($marketOpen && $is_trade_at_best && !$is_trading && !$lastTraded && !$lastKilled)
        {
            return 'trade-negotiation-open';
        }
        elseif(!$marketOpen && $is_trading && !$lastTraded && !$lastKilled)
        {
            return 'trade-negotiation-pending';
        }
        // @TODO - state when new trade happens on a market that has already traded 
        elseif($marketOpen && $is_trading && !$lastTraded && !$lastKilled) // Checks to check if new trade is happening
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
        dd([$acceptedState , $marketOpen , !$is_trading]);
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



        if($this->chosenUserMarket != null && $this->chosenUserMarket->lastNegotiation)
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
        $traded_on_day = (
            $this->chosen_user_market_id !== null
            ? $this->chosenUserMarket->marketNegotiations()->selectedDay()->traded()->exists() // this should be formatted by loading_previous_day / loadign_current_day config settings
            : false
        );
        $involved = false;
        if($this->chosen_user_market_id != null) {
            $last_negotiation = $this->chosenUserMarket->lastNegotiation;

            if($last_negotiation !== null) {
                if($last_negotiation->lastTradeNegotiation && !$last_negotiation->lastTradeNegotiation->traded) {
                    $involved = $last_negotiation->lastTradeNegotiation->isOrganisationInvolved($current_org_id);
                } elseif(
                    ($last_negotiation->user && $last_negotiation->user->organisation_id == $current_org_id)
                    || ($last_negotiation->counterUser && $last_negotiation->counterUser->organisation_id == $current_org_id)
                ) {
                    $involved = true;
                }
            }
        }

        $attributes = [
            'state'         => config('marketmartial.market_request_states.default'), // default state set first
            'bid_state'     => "",
            'offer_state'   => "",
            'action_needed' => "",
            'traded_on_day'  => $traded_on_day,
            'market_open'   => in_array($state, [
                'request',
                'request-vol',
                'negotiation-open',
                'trade-negotiation-open',
            ]),
            'involved' => $involved,
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
     * get the first underlying market
     *
     * @return \App\Models\StructureItems\Market
     */
    public function getTradingUnderlyingAttribute()
    {
        $trading_group = $this->userMarketRequestGroups()->where('is_selected', false)->first();
        return $trading_group->tradable;
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
                    //$this->market->title,
                    $this->getTradeablesTitle(true),
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
                    //$this->market->title,
                    $this->getTradeablesTitle(true),
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
                    //$this->market->title,
                    $this->getTradeablesTitle(true),
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
                    //$this->market->title,
                    $this->getTradeablesTitle(),
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
                    //$this->market->title,
                    $this->getTradeablesTitle(),
                    \Carbon\Carbon::parse($groups[0]->getDynamicItem($exp_date))->format("My"),
                    "vs",
                    \Carbon\Carbon::parse($groups[1]->getDynamicItem($exp_date))->format("My"),
                    "EFP SWITCH",
                ]);
            break;
            case 'efp':
                $exp_date = "Expiration Date";
                return implode(" ", [
                    //$this->market->title,
                    $this->getTradeablesTitle(),
                    \Carbon\Carbon::parse($this->getDynamicItem($exp_date))->format("My"),
                    "EFP"
                ]);
            break;
            case 'rolls':
                $exp_date_1 = "Expiration Date 1";
                $exp_date_2 = "Expiration Date 2";
                $strike = "Strike";
                return implode(" ", [
                    //$this->market->title,
                    $this->getTradeablesTitle(),
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
                    //$this->market->title,
                    $this->getTradeablesTitle(),
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
                    //$this->market->title,
                    $this->getTradeablesTitle(),
                    \Carbon\Carbon::parse($this->getDynamicItem($exp_date))->format("My"),
                    strtoupper($this->tradeStructure->title),
                    ( $capVal == 0 ? "(Uncapped)" : "(".$capVal."x Cap)" )
                ]);
            break;
        }
    }

    /**
     * Notify subscribed users and removes subscription,
     *
     * @return void
     */
    public function getTradeablesTitle($unique = false) {
        $tradeables = $this->userMarketRequestTradables;
        return $tradeables->pluck('title')->when($unique, function($c){ return $c->unique(); })->implode(',');
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


    /**
     * Return a simple or query object based on the search term
     *   
     *   This method requires extra fields to be present in the query see
     *   \App\Http\Controllers\Stats\yearActivity
     *   
     *   Fields: trade_date, trade_send_user_id, trade_receiving_user_id,
     *           trade_negotiation_id, trade_confirmation_id
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
        if($order == null)
        {
            $order = "DESC";
        }
        
        // Search markets
        $user_market_requests_query = UserMarketRequest::where( function ($q) use ($term)
        {
            $q->whereHas('userMarketRequestGroups', function ($q) use ($term) {
                $q->whereHas('tradable', function ($q) use ($term) {
                    $q->whereHas('market', function ($q) use ($term) {
                        $q->where('title','like',"%$term%");
                    })
                    ->orWhereHas('stock', function ($q) use ($term) {
                        $q->where('code','like',"%$term%");  
                    });
                });
            });
        });

        // Apply Filters
        if($filter !== null) {
            if(!empty($filter["filter_date"])) {
                $user_market_requests_query->whereDate('trade_confirmations.updated_at', $filter["filter_date"]);
            }

            if(!empty($filter["filter_market"])) {
                $user_market_requests_query->where('user_market_requests.market_id', $filter["filter_market"]);
            }

            if(!empty($filter["filter_expiration"])) {
                $user_market_requests_query->whereHas('tradeConfirmations', function ($query) use ($filter) {
                    $query->whereHas('tradeNegotiation', function ($query) use ($filter) {
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
                });
            }
        }

        // Apply Ordering
        switch ($orderBy) {
            /*case 'updated_at':
                $trade_confirmations_query->orderBy($orderBy,$order);
                break;
            case 'market':
                $trade_confirmations_query->whereHas('market',function($q) use ($order){
                    $q->orderBy('title',$order);
                });
                //$trade_confirmations_query->orderBy("market_id",$order)
                break;
*/
            case 'updated_at':
                if($order == "DESC") {
                    $user_market_requests_query->orderByRaw('IF(trade_confirmations.updated_at IS NULL,user_market_requests.created_at,trade_confirmations.updated_at) DESC');
                } else {
                    $user_market_requests_query->orderByRaw('IF(trade_confirmations.updated_at IS NULL,user_market_requests.created_at,trade_confirmations.updated_at) ASC');
                }
                break;
            /*case 'underlying':
                
                break;*/
            case 'structure':
                $user_market_requests_query->orderBy("trade_structure_title", $order);
                break;
            /*case 'direction':
            
                break;
            case 'nominal':
            
                break;
            case 'strike_percentage':
            
                break;
            case 'strike':
            
                break;
            case 'volatility':
            
                break;
            case 'expiration':
            
                break;*/
            default:
                $user_market_requests_query->orderByRaw('IF(trade_confirmations.updated_at IS NULL,user_market_requests.created_at,trade_confirmations.updated_at) DESC');
        }


        return $user_market_requests_query;
    }

    /**
     * Scope for Markets made by the organisation
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrganisationMarketMaker($query, $organisation_id, $or = false)
    {
        return $query->{($or ? 'orWhere' : 'where')}(function ($tlq) use ($organisation_id) {
            $tlq->whereHas('chosenUserMarket', function ($query) use ($organisation_id) {
                $query->whereHas('user', function ($query) use ($organisation_id) {
                    $query->where('organisation_id', $organisation_id);
                });
            });
        });
    }

    /**
     * Scope for Markets where the organisation was involved in the trade
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrganisationInvolvedTrade($query, $organisation_id, $operator, $or = false)
    {
        return $query->{($or ? 'orWhere' : 'where')}(function ($tlq)  use ($organisation_id,$operator) {
            $tlq->whereHas('tradeConfirmations', function ($q) use ($organisation_id,$operator) {
                $q->whereHas('sendUser', function ($q) use ($organisation_id,$operator) {
                    $q->where('organisation_id', $operator,$organisation_id);
                })
                ->orWhereHas('recievingUser', function ($q) use ($organisation_id,$operator) {
                    $q->where('organisation_id', $operator,$organisation_id);
                });
            });
        });

    }

    /**
     * Scope for Markets made by the organisation
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrganisationInterestNotTraded($query, $organisation_id, $or = false)
    {
        return $query->{($or ? 'orWhere' : 'where')}(function ($tlq) use ($organisation_id) {
            $tlq->whereHas('user', function ($q) use ($organisation_id) {
                $q->where('organisation_id', $organisation_id);
            })->doesnthave('tradeConfirmations');
        });
    }

    /**
     * Return pre formatted request for frontend with regards to stats.
     *   
     *   This method requires extra fields to be present in the collection see
     *   \App\Http\Controllers\Stats\yearActivity
     *   
     *   Fields: trade_date, trade_send_user_id, trade_receiving_user_id,
     *           trade_negotiation_id, trade_confirmation_id
     *
     * @return \App\Models\MarketRequest\UserMarketRequest
     */
    public function preFormatStats($user = null, $is_Admin = false)
    {   
        $data = [
            "id" => $this->id,
            "updated_at"        => is_null($this->trade_date) ? $this->created_at->format("Y-m-d H:i:s") : $this->trade_date,
            "underlying"        => array(),
            "structure"         => $this->tradeStructure->title,
            "nominal"           => array(),
            "strike"            => array(),
            "expiration"        => array(),
            "volatility"        => array(),
        ];
        $tradeNegotiation = TradeNegotiation::find($this->trade_negotiation_id);
        $ratio = $tradeNegotiation ? $tradeNegotiation->getTradingRatio() : 1 ;

        foreach ($this->userMarketRequestGroups as $key => $group) {
            foreach ($group->userMarketRequestItems as $key => $item) {
                switch ($item->title) {
                    case 'Expiration Date':
                    case 'Expiration Date 1':
                    case 'Expiration Date 2':
                        $data["expiration"][] = $item->value;
                        break;
                    case 'Strike':
                        $data["strike"][] = $item->value;
                        break;
                    case 'Quantity':
                        if($group->is_selected || is_null($this->trade_negotiation_id)) {
                            $ratio_value = round( $item->value * $ratio, 2);
                            $data["nominal"][] = $group->tradable->isStock() ? 'R'.$ratio_value.'m' : $ratio_value;
                        } else {
                            $data["nominal"][] = $group->tradable->isStock() ? 'R'.$tradeNegotiation->quantity.'m' : $tradeNegotiation->quantity;
                        }
                        break;
                }
            }
            // Add the group underlying
            $data["underlying"][] = $group->tradable->title;

            // Add the group volatility
            if($group->is_selected) {
                $data["volatility"][] = $group->volatility->volatility;        
            } else {
                if(is_null($this->trade_negotiation_id)) {
                    $data["volatility"][] = $this->chosenUserMarket->lastNegotiation->bid;
                    $data["volatility"][] = $this->chosenUserMarket->lastNegotiation->offer;
                } else {
                    $data["volatility"][] = $tradeNegotiation->getRoot()->is_offer ? $tradeNegotiation->marketNegotiation->offer :  $tradeNegotiation->marketNegotiation->bid;
                }
            }
        }

        $trade_confirmation_groups = TradeConfirmationGroup::where('trade_confirmation_id', $this->trade_confirmation_id)->get();

        if($user === null) {
            $data["strike_percentage"] = array();
            // Resolve Strike percentage
            foreach ($trade_confirmation_groups as $key => $group) {
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
                    $data["strike_percentage"][] = round($strike/$spot_price * 100, 2);
                }
            }
        }

        $is_versus_underlying_type = ['option_switch','efp_switch'];
        // Remove duplicates tradables for non versus underlying trade structures
        if(!in_array($this->tradeStructureSlug, $is_versus_underlying_type)) {
            $data["underlying"] = array_unique($data["underlying"]);
        }

        if($is_Admin) {
            if(!is_null($this->trade_confirmation_id) && $tradeNegotiation->traded) {
                $root_trade_negotiation = $tradeNegotiation->getRoot();
                if($root_trade_negotiation->is_offer) {
                    $data["buyer"] = $root_trade_negotiation->initiateUser->organisation->title;
                    $data["seller"] = $root_trade_negotiation->recievingUser->organisation->title;
                } else {
                    $data["buyer"] = $root_trade_negotiation->recievingUser->organisation->title;
                    $data["seller"] = $root_trade_negotiation->initiateUser->organisation->title;
                }
                return $data;
            }

            $data["buyer"] = null;
            $data["seller"] = null;    
            return $data;
        }


        if($user === null) {
            $data["status"] = !is_null($this->trade_confirmation_id) && $tradeNegotiation->traded ? 'Traded' : 'Not Traded';
            return $data; 
        }

        if(is_null($this->trade_confirmation_id)) {
            $data["status"] = 'Not Traded';
            $data["trader"] = null;
            $data["direction"] = null;

            return $data;
        }

        // Direction (Buy / Sell)
        $root_trade_negotiation = $tradeNegotiation->getRoot();

        switch (true) {
        // My Trade
            case ($root_trade_negotiation->initiate_user_id == $user->id):
                $data["status"] = 'My Trade';
                $data["trader"] = $user->full_name;
                $data["direction"] = $root_trade_negotiation->is_offer ? 'Buy' : 'Sell';
                break;
            case ($root_trade_negotiation->recieving_user_id == $user->id):
                $data["status"] = 'My Trade';
                $data["trader"] = $user->full_name;
                $data["direction"] = $root_trade_negotiation->is_offer ? 'Sell' : 'Buy';
                break;
        // Org Trade
            case ($root_trade_negotiation->initiateUser->organisation_id == $user->organisation_id):
                $data["status"] = 'Trade';
                $data["trader"] = $root_trade_negotiation->initiateUser->full_name;
                $data["direction"] = $root_trade_negotiation->is_offer ? 'Buy' : 'Sell';
                break;
            case ($root_trade_negotiation->recievingUser->organisation_id == $user->organisation_id):
                $data["status"] = 'Trade';
                $data["trader"] = $root_trade_negotiation->recievingUser->full_name;
                $data["direction"] = $root_trade_negotiation->is_offer ? 'Sell' : 'Buy';
                break;
        // Traded Away
            case ($this->chosenUserMarket->user->organisation_id == $user->organisation_id):
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
}
