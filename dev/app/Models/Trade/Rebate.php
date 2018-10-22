<?php

namespace App\Models\Trade;

use Illuminate\Database\Eloquent\Model;

class Rebate extends Model
{
	/**
	 * @property integer $id
	 * @property integer $user_id - The user to determine put or call from the trade confirmation
	 * @property integer $user_market_request_id
	 * @property integer $user_market_id
     * @property integer $booked_trade_id 
	 * @property integer $organisation_id - The Market Maker organisation id
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
        'user_id',
        'user_market_request_id',
        'user_market_id',
        'organisation_id',
        'is_paid',
        'trade_date',
        'booked_trade_id'
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
    public function userMarketRequest()
    {
        return $this->belongsTo('App\Models\MarketRequest\UserMarketRequest','user_market_request_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function bookedTrade()
    {
        return $this->belongsTo('App\Models\TradeConfirmations\BookedTrade','booked_trade_id');
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
    public function user()
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

    public function resolveMarketStock() {
        // Resolve stock / market
        if($this->bookedTrade->stock) {
            return $this->bookedTrade->stock->code;
        } else {
            return $this->bookedTrade->market->title;
        }
    }

    public function preFormat($user)
    {
        $trade_confirmation = $this->bookedTrade->tradeConfirmation;
        $user_market_request_items = $trade_confirmation->resolveUserMarketRequestItems();

        $data = [
            "date"          => $this->trade_date,
            "market"         => $this->resolveMarketStock(),
            "is_put"        => $trade_confirmation->is_put,
            "strike"        => $user_market_request_items["strike"],
            "expiration"    => $user_market_request_items["expiration"],
            "nominal"       => $user_market_request_items["nominal"],
            "role"          => null,
            "rebate"        => $this->bookedTrade->amount,
        ];

        // Resolve role
        switch (true) {
            case ($trade_confirmation->sendUser->organisation->id == $user->organisation->id):
            case ($trade_confirmation->recievingUser->organisation->id == $user->organisation->id):
                $data["role"] = 'Traded';
                break;
            case ($trade_confirmation->tradeNegotiation->userMarket->user->organisation->id == $user->organisation->id):
                $data["role"] = 'Market Maker (traded away)';
                break;
            default:
                $data["role"] = null;
                break;
        }

        return $data;
    }

    public function preFormatAdmin()
    {
        $trade_confirmation = $this->bookedTrade->tradeConfirmation;
        $user_market_request_items = $trade_confirmation->resolveUserMarketRequestItems();

        $data = [
            "id"            => $this->id,
            "date"          => $this->trade_date,
            "user"          => $this->user->full_name,
            "organisation"  => $this->user->organisation->title,
            "market"        => $this->resolveMarketStock(),
            "is_put"        => $trade_confirmation->is_put,
            "strike"        => $user_market_request_items["strike"],
            "expiration"    => $user_market_request_items["expiration"],
            "nominal"       => $user_market_request_items["nominal"],
            "rebate"        => $this->bookedTrade->amount,
            "is_paid"       => $this->is_paid,
        ];

        return $data;
    }

    /**
     * Return a simple or query object based on the search term
     *
     * @param string $term
     * @param string $orderBy
     * @param string $order
     * @param string  $filter
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function basicSearch($term = null,$orderBy="trade_date",$order='ASC',$filter = null)
    {
        if($orderBy == null)
        {
            $orderBy = "trade_date";
        }

        if($order == null)
        {
            $order = "ASC";
        }

        $rebateQuery = Rebate::where( function ($q) use ($term)
        {
            $q->whereHas('user',function($q) use ($term){
                $q->where('full_name','like',"%$term%");
            })
            ->orWhereHas('user',function($q) use ($term){
                $q->where('full_name','like',"%$term%")
                ->orWhereHas('organisation',function($q) use ($term){
                    $q->where('title','like',"%$term%");
                });
            })
            ->orWhereHas('bookedTrade',function($q) use ($term){
                $q->whereHas('stock',function($q) use ($term){
                    $q->where('code','like',"%$term%");
                })
                ->orWhereHas('market',function($q) use ($term){
                    $q->where('title','like',"%$term%");
                })
                ->orWhereHas('tradeConfirmation',function($q) use ($term){
                    if(strtolower($term) === 'put'){
                        $q->where('is_put','1');
                    }
                    if(strtolower($term) === 'call'){
                        $q->where('is_put','0');
                    }
                });
            });
        });

        // Apply Filters
        if($filter !== null) {
            if(isset($filter["filter_paid"])) {
                $rebateQuery->where('is_paid', $filter["filter_paid"]);
            }

            if(!empty($filter["filter_date"])) {
                $rebateQuery->whereDate('trade_date', $filter["filter_date"]);
            }
        }

        $rebateQuery->orderBy($orderBy,$order);

        return $rebateQuery;
    }
}
