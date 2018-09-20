<?php

namespace App\Models\MarketRequest;

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
    public function rebates()
    {
        return $this->hasMany('App\Models\Trade\Rebate','user_market_request_id');
    }


    /**
    * Return pre formatted request for frontend
    * @return \App\Models\MarketRequest\UserMarketRequest
    */
    public function preFormatted()
    {
        $current_org_id =  $this->resolveOrganisationId();

        $interest_org_id = $this->user->organisation->id;

        $data = [
            "id"                => $this->id,
            "market_id"         => $this->market_id,
            'is_interest'       => $interest_org_id == $current_org_id && $current_org_id != null,
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
            $data["chosen_user_market"] = $this->chosenUserMarket->setOrgContext($this->org_context)->preFormattedMarket();
            $data["quotes"]  = [];
            //market has been chosen and this user is considerd the market maker
            $market_maker_org_id = $this->chosenUserMarket->organisation->id;
            $data['is_market_maker'] = $market_maker_org_id == $current_org_id;

        }else
        {
            $data["sent_quote"]        = $this->authedUserMarket;
            $data["quotes"]            = $this->userMarkets->map(function($item){ 
                                                    return $item->setOrgContext($this->org_context)->preFormattedQuote(); 
                                           });
        }

        return $data;
    }

    private static $authedUserMarket = null;
    public function getAuthedUserMarketAttribute() {
        if(!self::$authedUserMarket) {
            self::$authedUserMarket = $this->userMarkets()->whereHas('user', function($q) {
                $q->where('organisation_id',$this->resolveOrganisationId());
            })->orderBy('updated_at', 'DESC')
            ->with('currentMarketNegotiation')
            ->first();
        }
        return self::$authedUserMarket;
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
                                            $q->whereHas('marketNegotiationParent',function($q) use ($current_org_id){
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
           return $this->chosenUserMarket->marketNegotiations()->where(function($query){
                    $query->where('is_repeat',true)
                            ->whereHas('marketNegotiationParent',function($query){
                                    $query->where('is_repeat',true);
                            });
            })->where('market_negotiations.id',$lastNegotiation->id)->exists();
        }
    }

    public function getStatus($current_org_id)
    {
        //method also used inside policies so be aware when updating
        $hasQuotes       = $this->userMarkets != null;
        $acceptedState   =  $hasQuotes ?  $this->isAcceptedState($current_org_id) : false;
        $marketOpen      = $acceptedState ? $this->openToMarket() : false;
        $canNegotiate    = $this->canNegotiate($current_org_id);

        if(!$hasQuotes)
        {
            return "request";
        }else if($hasQuotes && !$acceptedState)
        {
            return "request-vol";
        }else if($acceptedState && !$marketOpen)
        {
            return 'negotiation-pending';
        }elseif ($marketOpen)
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

    /**
     * resolve the attributes in accordence to the current users organisation and relation to the market request
     *
     * @return Array
     */
    private function resolveRequestAttributes()
    {

        $current_org_id =  $this->resolveOrganisationId();
        $interest_org_id = $this->user->organisation->id;
        $market_maker_org_id = $this->chosenUserMarket()->exists() ? $this->chosenUserMarket->organisation->id : null;
        $state = $this->getStatus($current_org_id,$interest_org_id);
        $marketRequestRoles = $this->getCurrentUserRoleInRequest($current_org_id, $interest_org_id,$market_maker_org_id);        
        $marketNegotiationRoles = $this->getCurrentUserRoleInMarketNegotiation($marketRequestRoles,$current_org_id);

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

}
