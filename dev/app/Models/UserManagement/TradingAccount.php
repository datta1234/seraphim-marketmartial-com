<?php

namespace App\Models\UserManagement;

use Illuminate\Database\Eloquent\Model;

class TradingAccount extends Model
{
	/**
	 * @property integer $id
	 * @property integer $user_id
	 * @property integer $market_id
	 * @property string $safex_number
	 * @property string $sub_account
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */
	
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trading_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'safex_number', 'sub_account','market_id'
    ];

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function user()
    {
        return $this->belongsTo('App\Models\UserManagement\User', 'user_id');
    }

    /**
    * Return relation based of market_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function market()
    {
        return $this->belongsTo('App\Models\StructureItems\Market', 'market_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function bookedTrades()
    {
        return $this->hasMany('App\Models\TradeConfirmations\BookedTrade','trading_account_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmations()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmation','trading_account_id');
    }
}
