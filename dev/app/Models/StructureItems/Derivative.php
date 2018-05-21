<?php

namespace App\Models\StructureItems;

use Illuminate\Database\Eloquent\Model;

class Derivative extends Model
{
	/**
	 * @property integer $id
	 * @property string $title
	 * @property integer $derivative_type_id
	 * @property text $description
	 * @property boolean $is_seldom
	 * @property boolean $has_deadline
	 * @property boolean $has_negotiation
	 * @property boolean $has_rebate
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */
	
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'derivatives';

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
    ];

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function jseIntergrations()
    {
        return $this->belongsToMany('App\Models\ApiIntegration\JseIntergration', 'derivative_jse_intergration', 'jse_intergration_id', 'derivative_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function derivativeTypes()
    {
        return $this->belongsTo('App\Models\StructureItems\DerivativeType', 'derivative_type_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradingAccounts()
    {
        return $this->hasMany('App\Models\UserManagement\TradingAccount', 'derivative_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userInterests()
    {
        return $this->belongsToMany('App\Models\UserManagement\User', 'user_derivative_interests', 'user_id', 'derivative_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userWatched()
    {
        return $this->belongsToMany('App\Models\UserManagement\User', 'user_derivative_watched', 'user_id', 'derivative_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarketRequestTradables()
    {
        return $this->hasMany('App\Models\MarketRequest\UserMarketRequestTradable','derivative_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmations()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmation','derivative_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function bookedTrades()
    {
        return $this->hasMany('App\Models\TradeConfirmations\BookedTrade','derivative_id');
    }
}
