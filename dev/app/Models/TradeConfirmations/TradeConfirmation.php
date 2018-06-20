<?php

namespace App\Models\TradeConfirmations;

use Illuminate\Database\Eloquent\Model;

class TradeConfirmation extends Model
{
	/**
	 * @property integer $id
	 * @property integer $send_user_id
	 * @property integer $receiving_user_id
	 * @property integer $trade_id
	 * @property integer $trade_confirmation_status_id
	 * @property integer $trade_confirmation_id
	 * @property integer $stock_id
	 * @property integer $market_id
	 * @property integer $traiding_account_id
	 * @property double $spot_price
	 * @property double $future_reference
	 * @property double $near_expiery_reference
	 * @property double $contracts
	 * @property double $puts
	 * @property double $calls
	 * @property double $delta
	 * @property double $gross_premiums
	 * @property double $net_premiums
	 * @property boolean $is_confirmed
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trade_confirmations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'spot_price',
		'future_reference',
		'near_expiery_reference',
		'contracts',
		'puts',
		'calls',
		'delta',
		'gross_premiums',
		'net_premiums',
		'is_confirmed',
    ];

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmationStatuses()
    {
        return $this->belongsTo(
        	'App\Models\TradeConfirmations\TradeConfirmationStatus',
        	'trade_confirmation_status_id'
        );
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function distputes()
    {
        return $this->hasMany('App\Models\TradeConfirmations\Distpute','trade_confirmation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmationParents()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmation','trade_confirmation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmationChildren()
    {
        return $this->belongsTo('App\Models\TradeConfirmations\TradeConfirmation','trade_confirmation_id');
    }


    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function bookedTrades()
    {
        return $this->hasMany('App\Models\TradeConfirmations\BookedTrade','trade_confirmation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function stocks()
    {
        return $this->belongsTo('App\Models\StructureItems\Stock','stock_id');
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
    public function trades()
    {
        return $this->belongsTo('App\Models\Trade\Trade','trade_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function sendUsers()
    {
        return $this->belongsTo('App\Models\UserManagement\User','send_user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function recievingUsers()
    {
        return $this->belongsTo('App\Models\UserManagement\User','receiving_user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradingAccounts()
    {
        return $this->belongsTo('App\Models\UserManagement\TradingAccount','traiding_account_id');
    }
}
