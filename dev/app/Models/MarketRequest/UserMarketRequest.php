<?php

namespace App\Models\MarketRequest;
use App\Models\MarketRequest\UserMarketRequestItem;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserManagement\Organisation;
use App\Events\UserMarketRequested;
use App\Helpers\Broadcast\Stream;

class UserMarketRequest extends Model
{
    use \App\Traits\ResolvesUser;
    use \App\Traits\ActionListCache;

    /**
     * @property integer $id
     * @property integer $user_id
     * @property integer $trade_structure_id
     * @property integer $user_market_request_statuses_id
     * @property integer $chosen_user_market_id
     * @property integer $market_id
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
    protected $fillable = ["market_id","trade_structure_id","user_market_request_statuses_id","chosen_user_market_id"];

    // protected $dates = [
    //     'created_at'
    // ];

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarketRequestStatus()
    {
        return $this->belongsTo(
            'App\Models\MarketRequest\UserMarketRequestStatus',
            'user_market_request_statuses_id'
        );
    }

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
    public function markets()
    {
        return $this->belongsTo('App\Models\StructureItems\Market','market_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function rebates()
    {
        return $this->hasMany('App\Models\Trade\Rebate','user_market_request_id');
    }


    /**
    * Return relation based of _id_foreign index
    * @return \App\Models\Market\UserMarket
    */
    public function createQuote($data)
    {
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

        return $userMarket;
    }    


    /**
    * Return pre formatted request for frontend
    * @return \App\Models\MarketRequest\UserMarketRequest
    */
    public function preFormatted()
    {
        $current_org_id =  $this->resolveOrganisationId();

        $interest_org_id = $this->user->organisation_id;
        $is_interest = $interest_org_id == $current_org_id && $current_org_id != null;

        $data = [
            "id"                => $this->id,
            "market_id"         => $this->market_id,
            'is_interest'       => $is_interest,
            "is_market_maker"   => false,
            "trade_structure"   => $this->tradeStructure->title,
            "trade_items"       => $this->userMarketRequestGroups
             ->keyBy('tradeStructureGroup.title')
             ->map(function($group) {
                return $group->userMarketRequestItems->keyBy('title')->map(function($item) {
                    if($item->type == 'expiration date') {
                        return (new \Carbon\Carbon($item->value))->format("My");
                    }
                    return $item->value;
                });
            }),
            "attributes"        => $this->resolveRequestAttributes(),
            "created_at"         => $this->created_at->format("Y-m-d H:i:s"),
            "updated_at"         => $this->updated_at->format("Y-m-d H:i:s"),
        ];

        $showLevels = $this->isAcceptedState($current_org_id);

        if($showLevels)
        {
            $data["chosen_user_market"] = $this->chosenUserMarket->setOrgContext($this->resolveOrganisation())->preFormattedMarket();
            $data["quotes"]  = [];
            //market has been chosen and this user is considerd the market maker
            $market_maker_org_id = $this->chosenUserMarket->organisation->id;
            $data['is_market_maker'] = $market_maker_org_id == $current_org_id;

        }else
        {
            $data["quotes"]            =    $this->userMarkets()->when(!$is_interest, function($query) use ($current_org_id) {
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
            $stream = new Stream(new UserMarketRequested($this,$organisation));
            $stream->run();
        } 
        
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
            $marketCount = $this->chosenUserMarket->marketNegotiations()->count();

            if($is_interest){
              return  $marketCount > 0;
            }else{
              return $marketCount > 1;
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
                                        $q->orWhere(function($q) use ($current_org_id){
                                            $q->whereHas('marketNegotiationParent',function($q) use ($current_org_id) {
                                                $q->organisationInvolved($current_org_id);  
                                            });
                                        });
                    })->where('market_negotiations.id',$lastNegotiation->id)->exists();   
            } 
        } 
    }

    public function openToMarket()
    {
        if($this->chosenUserMarket != null)
        {
            $lastNegotiation = $this->chosenUserMarket->lastNegotiation;
            if(!is_null($lastNegotiation) && !is_null($lastNegotiation->marketNegotiationParent))
            {
              return $lastNegotiation->is_repeat  && $lastNegotiation->marketNegotiationParent->is_repeat; 
            }
        }
        return false;
    }

    public function lastTradeNegotiationIsUnTraded()
    {
        if(!is_null($this->chosenUserMarket))
        {
            $lastNegotiation = $this->chosenUserMarket->lastNegotiation;
            return !is_null($lastNegotiation) && !is_null($lastNegotiation->lastTradeNegotiation) && !$lastNegotiation->lastTradeNegotiation->traded;  
        }
    }

     public function lastTradeNegotiationIsTraded()
    {
        if(!is_null($this->chosenUserMarket))
        {
            $lastNegotiation = $this->chosenUserMarket->lastNegotiation;
            return !is_null($lastNegotiation) && !is_null($lastNegotiation->lastTradeNegotiation) && $lastNegotiation->lastTradeNegotiation->traded;  
        }
    }

    public function getStatus($current_org_id)
    {
        //method also used inside policies so be aware when updating
        $hasQuotes          =  $this->userMarkets != null;
        $acceptedState      =  $hasQuotes ?  $this->isAcceptedState($current_org_id) : false;
        $marketOpen         =  $acceptedState ? $this->openToMarket() : false;
        $is_killed          =  $acceptedState ? $this->chosenUserMarket->lastNegotiation->is_killed === true : false;
        $lastUntraded       =  $this->lastTradeNegotiationIsUnTraded();
        $lastTraded         =  !$lastUntraded ? $this->lastTradeNegotiationIsTraded() : false;

        /*
        * check if the current is true and next is false to create a cascading virtual state effect
        */
        if(!$hasQuotes)
        {
            return "request";
        }
        elseif($hasQuotes && !$acceptedState)
        {
            return "request-vol";
        }
        elseif($acceptedState && !$marketOpen && !$lastUntraded && $is_killed)
        {
            return 'negotiation-open';
        }
        elseif($acceptedState && !$marketOpen && !$lastUntraded && !$lastTraded)
        {
            return 'negotiation-pending';
        }
        elseif ($acceptedState && !$marketOpen && !$lastUntraded && $lastTraded)
        {
            return 'trade-negotiation-balance';
        }
        elseif ($marketOpen && !$lastUntraded && !$lastTraded)
        {
            return 'negotiation-open';
        }
        elseif($lastUntraded)
        {
            return 'trade-negotiation-pending';
        }
    }

    
    public function getCurrentUserRoleInRequest($current_org_id, $interest_org_id,$market_maker_org_id)
    {

        // make sure to handle null organisations as false
        $marketRequestRoles = ["other"];
        if($interest_org_id == $current_org_id && $current_org_id  != null && $market_maker_org_id != $interest_org_id)
        {
            $marketRequestRoles = ["interest"];
        }else if ($market_maker_org_id == $current_org_id && $current_org_id  != null && $market_maker_org_id != $interest_org_id) 
        {
            $marketRequestRoles = ["market_maker"];
        }else if($market_maker_org_id == $current_org_id  && $interest_org_id == $current_org_id && $current_org_id  != null)
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

                $counter =  $this->chosenUserMarket->marketNegotiations()->where(function($query) use ($current_org_id){
                        $query->whereHas('marketNegotiationParent',function($q) use ($current_org_id){
                            $q->organisationInvolved($current_org_id);
                        });
                })->where('market_negotiations.id',$lastNegotiation->id)->exists();

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


            $lastNegotiation = $this->chosenUserMarket->lastNegotiation;
            if(!is_null($lastNegotiation) && !is_null($lastNegotiation->lastTradeNegotiation))
            {
                if($lastNegotiation->lastTradeNegotiation->initiateUser->organisation_id == $current_org_id)
                {
                    $tradeNegotiationRoles = ["negotiator"];
                }

                if($lastNegotiation->lastTradeNegotiation->recievingUser->organisation_id == $current_org_id)
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
        $attributes['bid_state'] = $authedUserMarket && $authedUserMarket->currentMarketNegotiation->bid ? 'action' : '';
        $attributes['offer_state'] = $authedUserMarket && $authedUserMarket->currentMarketNegotiation->offer ? 'action' : '';

        /*
        *   Action needed (Alert)
        */
        $needs_action = $this->getAction($this->resolveOrganisationId(), $this->id);
        $attributes['action_needed'] = $needs_action == null ? false : $needs_action;        
    

        return $attributes;
    }

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



}
