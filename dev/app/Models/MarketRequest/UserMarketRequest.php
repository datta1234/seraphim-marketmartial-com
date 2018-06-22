<?php

namespace App\Models\MarketRequest;

use Illuminate\Database\Eloquent\Model;

class UserMarketRequest extends Model
{
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

    protected $dates = [
        'created_at'
    ];

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
        return $this->hasMany('App\Models\Market\UserMarket','user_market_request_id');
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
        $data = [
            "id"                => $this->id,
            "trade_structure"   => $this->tradeStructure->title,
            "trade_items"       => $this->userMarketRequestGroups
             ->keyBy('tradeStructureGroup.title')
             ->map(function($group) {
                return $group->userMarketRequestItems->keyBy('title')->map(function($item) {
                    return $item->value;
                });
            }),
            "attributes" => $this->resolveRequestAttributes(),
            "quotes"    => $this->userMarkets->map(function($item){ 
                return $item->preFormattedQuote(); 
            }),

            "user_market"   =>  $this->authedUserMarket, //UserMarket

            "created_at"    => $this->created_at->format("Y-m-d H:i:s"),
            "updated_at"    => $this->updated_at->format("Y-m-d H:i:s"),
        ];

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
            $q->where('organisation_id', \Auth::user()->organisation_id);
        })->orderBy('updated_at', 'DESC')
            ->with('currentMarketNegotiation')
            ->first();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Array
     */
    private function resolveRequestAttributes()
    {
        $attributes = [
            'state' => config('marketmartial.market_request_states.default'), // default state set first
            'bid_state' => "",
            'offer_state'   => "",
        ];

        // get is this current user the 
        $self_user = $this->user_id == \Auth::user()->id;
        // make sure to handle null organisations as false
        $self_org = ( \Auth::user()->organisation_id == null ? false : $this->user->organisation_id == \Auth::user()->organisation_id );

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


        /*
        *   BID / OFFER states
        */
        $attributes['bid_state'] = $this->authedUserMarket && $this->authedUserMarket->currentMarketNegotiation->bid ? 'action' : '';
        $attributes['offer_state'] = $this->authedUserMarket && $this->authedUserMarket->currentMarketNegotiation->offer ? 'action' : '';

        return $attributes;
    }

}
