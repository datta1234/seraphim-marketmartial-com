<?php

namespace App\Models\Market;

use Illuminate\Database\Eloquent\Model;

class MarketNegotiation extends Model
{
    use \App\Traits\ResolvesUser;
    
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


    protected $appends = ["time"];

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function marketNegotiationParent()
    {
        return $this->belongsTo('App\Models\Market\MarketNegotiation','market_negotiation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function marketNegotiationChildren()
    {
        return $this->hasMany('App\Models\Market\MarketNegotiation','market_negotiation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarket()
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
    public function user()
    {
        return $this->belongsTo('App\Models\UserManagement\User','user_id');
    }

    public function getTimeAttribute()
    {
        return $this->created_at->format("H:i");
    }

    /**
    * Return pre formatted request for frontend
    * @return \App\Models\Market\UserMarket
    */
    public function preFormattedQuote()
    {

        $currentUserOrganisationId = $this->user->organisation_id;
        $interestUserOrganisationId = $this->userMarket->userMarketRequest->user->organisation_id;
        $marketMakerUserOrganisationId = $this->userMarket->user->organisation_id;
        $loggedInUserOrganisationId = $this->resolveOrganisationId();


        //dd($currentUserOrganisationId,$interestUserOrganisationId,$marketMakerUserOrganisationId,$loggedInUserOrganisationId);

         $is_maker = is_null($marketMakerUserOrganisationId) ? false : $currentUserOrganisationId == $marketMakerUserOrganisationId;
         $is_interest = is_null($interestUserOrganisationId) ? false : $currentUserOrganisationId == $interestUserOrganisationId;

        $data = [
            'id'                    => $this->id,
            "market_negotiation_id" => $this->market_negotiation_id,
            "user_market_id"        => $this->user_market_id,
            "bid"                   => $this->bid,
            "offer"                 => $this->offer,
            "offer_qty"             => $this->offer_qty,
            "bid_qty"               => $this->bid_qty,
            "bid_premium"           => $this->bid_premium,
            "offer_premium"         => $this->offer_premium,
            "future_reference"      => $this->future_reference,
            "has_premium_calc"      => $this->has_premium_calc,
            "is_repeat"             => $this->is_repeat,
            "is_accepted"           => $this->is_accepted,
            "is_private"            => $this->is_private,
            "cond_is_repeat_atw"    => $this->cond_is_repeat_atw,
            "cond_fok_apply_bid"    => $this->cond_fok_apply_bid,
            "cond_fok_spin"         => $this->cond_fok_spin,
            "cond_timeout"          => $this->cond_timeout,
            "cond_is_ocd"           => $this->cond_is_ocd,
            "cond_is_subject"       => $this->cond_is_subject,
            "cond_buy_mid"          => $this->cond_buy_mid,
            "cond_buy_best"         => $this->cond_buy_best,
            "is_interest"           => $is_interest,
            "is_maker"              => $is_maker,
            "is_my_org"             => $currentUserOrganisationId == $loggedInUserOrganisationId,
            "time"                  => $this->time,
            "created_at"            => $this->created_at->format("d-m-Y H:i:s")

        ];

        return $data;
    }
}
