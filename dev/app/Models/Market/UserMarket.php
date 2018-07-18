<?php

namespace App\Models\Market;

use Illuminate\Database\Eloquent\Model;

class UserMarket extends Model
{
    use \App\Traits\ResolvesUser;
	/**
	 * @property integer $id
	 * @property integer $user_id
	 * @property integer $user_market_request_id
	 * @property integer $user_market_status_id
	 * @property integer $current_market_negotiation_id
	 * @property boolean $is_trade_away
	 * @property boolean $is_market_maker_notified
     * @property boolean $is_hold_on
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 * @property \Carbon\Carbon $deleted_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_markets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'is_trade_away',
        'is_market_maker_notified',
        'user_market_request_id',
    ];


    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function marketNegotiations()
    {
        return $this->hasMany('App\Models\Market\MarketNegotiation','user_market_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function currentMarketNegotiation()
    {
        return $this->belongsTo('App\Models\Market\MarketNegotiation','current_market_negotiation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarketSubscriptions()
    {
        return $this->hasMany('App\Models\Market\UserMarketSubscription','user_market_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarketRequest()
    {
        return $this->belongsTo('App\Models\MarketRequest\UserMarketRequest','user_market_request_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function rebates()
    {
        return $this->hasMany('App\Models\Trade\Rebate','user_market_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeNegotiations()
    {
        return $this->hasMany('App\Models\Trade\TradeNegotiation','user_market_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function trades()
    {
        return $this->hasMany('App\Models\Trade\Trade','user_market_id');
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
    * Return pre formatted request for frontend
    * @return \App\Models\Market\UserMarket
    */
    public function preFormattedQuote()
    {
       $is_marker = is_null($this->user->organisation) ? false : $this->resolveOrganisationId() == $this->user->organisation->id;
       $is_interest = is_null($this->userMarketRequest->user->organisation) ? false : $this->resolveOrganisationId() == $this->userMarketRequest->user->organisation->id;


        $data = [
            "id"    => $this->id,
            "is_interest"  =>  $is_interest,
            "is_maker"   => $is_marker,
            "bid_only" => $this->currentMarketNegotiation->offer == null,
            "offer_only" => $this->currentMarketNegotiation->bid == null,
            "vol_spread" => (
                !$this->currentMarketNegotiation->offer == null && !$this->currentMarketNegotiation->bid == null ? 
                $this->currentMarketNegotiation->offer - $this->currentMarketNegotiation->bid : 
                null 
            ),
            "time" => $this->created_at->format("H:i"),
        ];
        if($data['is_maker']) {
            $data['bid'] = $this->currentMarketNegotiation->bid;
            $data['offer'] = $this->currentMarketNegotiation->offer;
            $data['bid_qty'] = $this->currentMarketNegotiation->bid_qty;
            $data['offer_qty'] = $this->currentMarketNegotiation->offer_qty;
        }
        return $data;
    }

    /**
    * Return pre formatted request for frontend
    * @return \App\Models\Market\UserMarket
    */
    public function preFormatted()
    {
        $is_marker = is_null($this->user->organisation) ? false : $this->resolveOrganisationId() == $this->user->organisation->id;
       $is_interest = is_null($this->userMarketRequest->user->organisation) ? false : $this->resolveOrganisationId() == $this->userMarketRequest->user->organisation->id;

        $data = [
            "id"    => $this->id,
            "is_interest"  =>  $is_interest,
            "is_maker"   => $is_marker,
            "bid_only" => $this->currentMarketNegotiation->offer == null,
            "offer_only" => $this->currentMarketNegotiation->bid == null,
            "vol_spread" => (
                !$this->currentMarketNegotiation->offer == null && !$this->currentMarketNegotiation->bid == null ? 
                $this->currentMarketNegotiation->offer - $this->currentMarketNegotiation->bid : 
                null 
            ),
            "time" => $this->created_at->format("H:i"),
        ];
        if($data['is_maker']) {
            $data['bid'] = $this->currentMarketNegotiation->bid;
            $data['offer'] = $this->currentMarketNegotiation->offer;
            $data['bid_qty'] = $this->currentMarketNegotiation->bid_qty;
            $data['offer_qty'] = $this->currentMarketNegotiation->offer_qty;
        }
        return $data;
    }

}
