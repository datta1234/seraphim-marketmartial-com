<?php

namespace App\Models\StructureItems;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
	/**
	 * @property integer $id
	 * @property string $name
	 * @property string $code
     * @property string $verified
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stocks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'code'
    ];

    /**
    * Return relation based of market_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function jseIntergrations()
    {
        return $this->belongsToMany('App\Models\ApiIntegration\JseIntergration', 'jse_intergration_stock', 'jse_intergration_id', 'stock_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarketRequestTradables()
    {
        return $this->hasMany('App\Models\MarketRequest\UserMarketRequestTradable','stock_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmations()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmation','stock_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function bookedTrades()
    {
        return $this->hasMany('App\Models\TradeConfirmations\BookedTrade','stock_id');
    }
}
