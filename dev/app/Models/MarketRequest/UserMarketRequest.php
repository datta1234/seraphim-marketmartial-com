<?php

namespace App\Models\MarketRequest;

use Illuminate\Database\Eloquent\Model;

class UserMarketRequest extends Model
{
    /**
     * @property integer $id
     * @property integer $user_id
     * @property integer $trade_structure_id
     * @property integer $user_market_request_statuses_id
     * @property integer $chosen_user_market_id
     * @property \Carbon\Carbon $created_at
     * @property \Carbon\Carbon $updated_at
     */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_market_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ["market_id","trade_structure_id","user_market_request_statuses_id","chosen_user_market_id"];

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarketRequestStatus()
    {
        return $this->belongsTo(
            'App\Models\MarketRequest\UserMarketRequestStatus',
            'user_market_request_statuses_id'
        );
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarketRequestTradables()
    {
        return $this->hasMany(
            'App\Models\MarketRequest\UserMarketRequestTradable',
            'user_market_request_id'
        );
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeStructure()
    {
        return $this->belongsTo('App\Models\StructureItems\TradeStructure','trade_structure_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarketRequestGroups()
    {
        return $this->hasMany(
            'App\Models\MarketRequest\UserMarketRequestGroup',
            'user_market_request_id'
        );
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function user()
    {
        return $this->belongsTo('App\Models\UserManagement\User','user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function chosenUserMarket()
    {
        return $this->belongsTo('App\Models\Market\UserMarket','chosen_user_market_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarkets()
    {
        return $this->hasMany('App\Models\Market\UserMarket','user_market_request_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function rebates()
    {
        return $this->hasMany('App\Models\Trade\Rebate','user_market_request_id');
    }

}
