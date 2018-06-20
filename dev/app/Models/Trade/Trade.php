<?php

namespace App\Models\Trade;

use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
	/**
	 * @property integer $id
	 * @property integer $trade_negotiation_id
	 * @property integer $user_market_id
	 * @property integer $initiate_user_id
	 * @property integer $recieving_user_id
	 * @property integer $trade_status_id
	 * @property double $contracts
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trades';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contracts'
    ];

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeStatuses()
    {
        return $this->belongsTo('App\Models\Trade\TradeStatus','trade_status_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function rebates()
    {
        return $this->hasMany('App\Models\Trade\Rebate','trade_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeNegotiations()
    {
        return $this->belongsTo('App\Models\Trade\TradeNegotiation','trade_negotiation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmations()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmation','trade_id');
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
    public function initiateUsers()
    {
        return $this->belongsTo('App\Models\UserManagement\User','initiate_user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function recievingUsers()
    {
        return $this->belongsTo('App\Models\UserManagement\User','recieving_user_id');
    }
}
