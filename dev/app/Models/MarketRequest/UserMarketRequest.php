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
            "is_interest"       => $this->resolveOrganisationId() == null ? false : $this->user->organisation_id == $this->resolveOrganisationId(),
            'is_interest'       => $interest_org_id == $current_org_id,
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

        $showLevels = $this->isAcceptedState($current_org_id, $interest_org_id);

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

        // if($this->currentUserUserMarket()) {
        //     $data["quote"]  =  $this->currentUserUserMarket()->preFormattedQuote(); //UserMarketQuote
        // } 
        // // OR - show quote to all, user_market to interest & market maker
        // else {
            
        // }

        return $data;
    }

    public function getAuthedUserMarketAttribute() {
        return $this->userMarkets()->whereHas('user', function($q) {
            $q->where('organisation_id',$this->resolveOrganisationId());
        })->orderBy('updated_at', 'DESC')
            ->with('currentMarketNegotiation')
            ->first();
    }

    public function notifyRequested($organisations = [], $messages = null)
    {
        $organisations = (count($organisations) > 0) ? $organisations : Organisation::verifiedCache();
        foreach ($organisations  as $organisation) 
        {
            $stream = new Stream(new UserMarketRequested($this,$organisation));
            $stream->run();
        } 
        
    
    }


    //for the interest show them the levels
    public function isAcceptedState($current_org_id, $interest_org_id)
    {
        if($this->chosenUserMarket()->exists())
        {
            $is_interest = $interest_org_id == $current_org_id;

            $marketCount = $this->chosenUserMarket->marketNegotiations()->count();
            if($is_interest)
            {
              return  $marketCount > 0;
            }else
            {
              return $marketCount > 1;
            }

        }   
    }

    


    /**
     * Show the form for creating a new resource.
     *
     * @return Array
     */
    private function resolveRequestAttributes()
    {

        $current_org_id =  $this->resolveOrganisationId();
        $interest_org_id = $this->user->organisation->id;

        $attributes = [
            'state' => config('marketmartial.market_request_states.default'), // default state set first
            'bid_state' => "",
            'offer_state'   => "",
            'action_needed' => ""
            ];


        // make sure to handle null organisations as false
        $self_org = ( $current_org_id  == null ? false : $this->user->organisation_id == $current_org_id  );

        if($this->isAcceptedState($current_org_id, $interest_org_id))
        {
            //market has been chosen and this user is considerd the market maker
            $market_maker_org_id = $this->chosenUserMarket->organisation->id;

            if($interest_org_id == $current_org_id)
            {
                $attributes['state'] = config('marketmartial.market_request_states.negotiation-pending.interest');
            }elseif($market_maker_org_id == $current_org_id)
            {
                $attributes['state'] = config('marketmartial.market_request_states.negotiation-pending.market_maker');
            }else
            {
                $attributes['state'] = config('marketmartial.market_request_states.negotiation-pending.other');
            }




        }else //no market has been chosen as of yet
        {
            
               // if not quotes/user_markets preset => REQUEST
            if($this->userMarkets->isEmpty()) {
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
  
        }

       

        /*
        *   BID / OFFER states
        */
        $attributes['bid_state'] = $this->authedUserMarket && $this->authedUserMarket->currentMarketNegotiation->bid ? 'action' : '';
        $attributes['offer_state'] = $this->authedUserMarket && $this->authedUserMarket->currentMarketNegotiation->offer ? 'action' : '';

        /*
        *   Action needed (Alert)
        */
        $needs_action = $this->getAction($this->resolveOrganisationId(), $this->id);
        $attributes['action_needed'] = $needs_action == null ? false : $needs_action;        

        return $attributes;
    }

}
