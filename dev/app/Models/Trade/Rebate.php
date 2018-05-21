<?php

namespace App\Models\Trade;

use Illuminate\Database\Eloquent\Model;

class Rebate extends Model
{
	/**
	 * @property integer $id
	 * @property integer $user_id
	 * @property integer $user_market_request_id
	 * @property integer $user_market_id
	 * @property integer $trade_id
	 * @property integer $organisation_id
	 * @property integer $booked_trade_id
	 * @property boolean $is_paid
	 * @property \Carbon\Carbon $trade_date
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rebates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_paid',
    ];

    /**
     * Return relation based of _id_foreign index
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function slackIntegrations()
    {
        return $this->belongsToMany('App\Models\ApiIntegration\SlackIntegration', 'rebate_slack_integration', 'slack_integration_id', 'rebate_id');
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
    public function userMarketRequests()
    {
        return $this->belongsTo('App\Models\MarketRequest\UserMarketRequest','user_market_request_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function bookedTrades()
    {
        return $this->belongsTo('App\Models\TradeConfirmations\BookedTrade','booked_trade_id');
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
    public function users()
    {
        return $this->belongsTo('App\Models\UserManagement\User','user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function organisations()
    {
        return $this->hasMany('App\Models\UserManagement\Organisation', 'organisation_id');
    }
}
