<?php

namespace App\Models\StructureItems;

use Illuminate\Database\Eloquent\Model;

class Market extends Model
{
	/**
	 * @property integer $id
	 * @property string $title
	 * @property integer $market_type_id
	 * @property text $description
	 * @property boolean $is_seldom
	 * @property boolean $has_deadline
	 * @property boolean $has_negotiation
	 * @property boolean $has_rebate
     * @property double spot_price_ref
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */
	
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'markets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
		'description',
		'is_seldom',
		'has_deadline',
		'has_negotiation',
		'has_rebate',
        'spot_price_ref',
    ];

    protected $casts = [
        'is_seldom'             => 'Boolean',
    ];

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function jseIntergrations()
    {
        return $this->belongsToMany('App\Models\ApiIntegration\JseIntergration', 'market_jse_intergration', 'jse_intergration_id', 'market_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function marketType()
    {
        return $this->belongsTo('App\Models\StructureItems\MarketType', 'market_type_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradingAccounts()
    {
        return $this->hasMany('App\Models\UserManagement\TradingAccount', 'market_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userWatched()
    {
        return $this->belongsToMany('App\Models\UserManagement\User', 'user_market_watched', 'user_id', 'market_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarketRequestTradables()
    {
        return $this->hasMany('App\Models\MarketRequest\UserMarketRequestTradable','market_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarketRequests()
    {
        return $this->hasMany('App\Models\MarketRequest\UserMarketRequest','market_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmations()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmation','market_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function bookedTrades()
    {
        return $this->hasMany('App\Models\TradeConfirmations\BookedTrade','market_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function parent()
    {
        return $this->belongsTo('App\Models\StructureItems\Market','parent_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function children()
    {
        return $this->hasMany('App\Models\StructureItems\Market','parent_id');
    }

}
