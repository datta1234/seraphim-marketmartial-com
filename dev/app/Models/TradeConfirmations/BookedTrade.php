<?php

namespace App\Models\TradeConfirmations;

use Illuminate\Database\Eloquent\Model;

class BookedTrade extends Model
{
	/**
	 * @property integer $id
	 * @property integer $user_id
	 * @property integer $trade_confirmation_id
	 * @property integer $traiding_account_id
	 * @property integer $market_id
	 * @property integer $stock_id
	 * @property integer $booked_trade_status_id
	 * @property boolean $is_sale
	 * @property boolean $is_confirmed
	 * @property double $amount
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */
	
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'booked_trades';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_sale', 'is_confirmed', 'amount',
    ];

    /**
     * Return relation based of _id_foreign index
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function jseTradeIntergrations()
    {
        return $this->belongsToMany('App\Models\ApiIntegration\JseTradeIntergration', 'booked_trade_jse_intergration', 'jse_intergration_id', 'booked_trade_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function bookedTradeStatuses()
    {
        return $this->belongsTo('App\Models\TradeConfirmations\BookedTradeStatus','booked_trade_status_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmations()
    {
        return $this->belongsTo('App\Models\TradeConfirmations\TradeConfirmation','trade_confirmation_id');
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
    public function rebates()
    {
        return $this->hasMany('App\Models\Trade\Rebate','booked_trade_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function users()
    {
        return $this->belongsTo('App\Models\UserManagement\User','user_id');
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
