<?php

namespace App\Models\Trade;

use Illuminate\Database\Eloquent\Model;

class TradeNegotiation extends Model
{
	/**
	 * @property integer $id
	 * @property integer $user_market_id
	 * @property integer $trade_negotiation_id
     * @property integer $market_negotiation_id
	 * @property integer $initiate_user_id
	 * @property integer $recieving_user_id
	 * @property integer $trade_negotiation_status_id
	 * @property double $quantity
	 * @property double $traded
	 * @property boolean $is_offer
	 * @property boolean $is_distpute
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trade_negotiations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quantity', 'is_offer', 'is_distpute',
    ];

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeNegotiationStatus()
    {
        return $this->belongsTo('App\Models\Trade\TradeNegotiationStatus','trade_negotiation_status_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeNegotiationParents()
    {
        return $this->hasMany('App\Models\Trade\TradeNegotiation','trade_negotiation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeNegotiationChildren()
    {
        return $this->belongsTo('App\Models\Trade\TradeNegotiation','trade_negotiation_id');
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
    public function initiateUser()
    {
        return $this->belongsTo('App\Models\UserManagement\User','initiate_user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function recievingUser()
    {
        return $this->belongsTo('App\Models\UserManagement\User','recieving_user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmations()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmation','trade_negotiation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function marketNegotiation()
    {
        return $this->belongsTo('App\Models\Market\MarketNegotiation','market_negotiation_id');
    }
}
