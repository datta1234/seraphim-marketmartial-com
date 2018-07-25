<?php

namespace App\Models\Market;

use Illuminate\Database\Eloquent\Model;

class MarketNegotiation extends Model
{
	/**
	 * @property integer $id
	 * @property integer $user_id
	 * @property integer $market_negotiation_id
	 * @property integer $user_market_id
	 * @property integer $market_negotiation_status_id
	 * @property double $bid
	 * @property double $offer
	 * @property double $bid_qty
     * @property double $offer_qty
     * @property double $bid_premium
     * @property double $offer_premium
     * @property double $future_reference
	 * @property boolean $has_premium_calc
	 * @property boolean $is_repeat
	 * @property boolean $is_accepted
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'market_negotiations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

            "user_id",
            "counter_user_id",
            "market_negotiation_id",
            "user_market_id",
            "bid",
            "offer",
            "offer_qty",
            "bid_qty",
            "bid_premium",
            "offer_premium",
            "future_reference",
            "has_premium_calc",
            "is_repeat",
            "is_accepted",

            "is_private",
            "cond_is_repeat_atw",
            "cond_fok_apply_bid",
            "cond_fok_spin",
            "cond_timeout",
            "cond_is_ocd",
            "cond_is_subject",
            "cond_buy_mid",
            "cond_buy_best",
    ];

    protected $hidden = ["user_id","counter_user_id"];

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function marketConditions()
    {
        return $this->belongsToMany('App\Models\Market\MarketCondition','market_negotiation_condition');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function marketNegotiationParents()
    {
        return $this->hasMany('App\Models\Market\MarketNegotiation','market_negotiation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function marketNegotiationChildren()
    {
        return $this->belongsTo('App\Models\Market\MarketNegotiation','market_negotiation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarkets()
    {
        return $this->belongsTo('App\Models\Market\UserMarket','user_market_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function currentUserMarkets()
    {
        return $this->hasMany('App\Models\Market\UserMarket','current_market_negotiation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function users()
    {
        return $this->belongsTo('App\Models\UserManagement\User','user_id');
    }
}
