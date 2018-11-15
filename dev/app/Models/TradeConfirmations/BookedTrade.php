<?php

namespace App\Models\TradeConfirmations;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BookedTrade extends Model
{
	/**
	 * @property integer $id
	 * @property integer $user_id
	 * @property integer $trade_confirmation_id
	 * @property integer $trading_account_id
     * @property integer $user_market_request_id
	 * @property boolean $is_sale
     * @property boolean $is_purchase
     * @property boolean $is_rebate
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
        'is_confirmed',
        'amount',
        'user_id',
        'trade_confirmation_id',
        'trading_account_id',
        "user_market_request_id",
        "is_sale",
        "is_purchase",
        "is_rebate"
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
    public function tradeConfirmation()
    {
        return $this->belongsTo('App\Models\TradeConfirmations\TradeConfirmation','trade_confirmation_id');
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
    public function rebate()
    {
        return $this->hasMany('App\Models\Trade\Rebate','booked_trade_id');
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
    public function tradingAccount()
    {
        return $this->belongsTo('App\Models\UserManagement\TradingAccount','trading_account_id');
    }

    public function preFormatAdmin($is_csv = false)
    {
        $user_market_request_items = $this->tradeConfirmation->resolveUserMarketRequestItems();
        $data = [
            "id"            => $this->id,
            "date"          => $this->created_at->format('Y-m-d H:i:s'),
            "user"          => $this->user->full_name,
            "organisation"  => $this->user->organisation->title,
            "market"        => $this->tradeConfirmation->resolveUnderlying(),
            "is_put"        => $this->tradeConfirmation->is_put,
            "strike"        => $user_market_request_items["strike"],
            "expiration"    => $user_market_request_items["expiration"],
            "nominal"       => $user_market_request_items["nominal"],
            "amount"        => $this->amount,
            "is_confirmed"  => $this->is_confirmed,
        ];

        if($is_csv) {
            array_walk($data, function(&$field,$key) {
                if($key == "is_confirmed") {
                    $field = $field ? "Confirmed" : "Pending";
                } else {
                    $field = is_array($field) ? implode(" / ",$field) : $field;
                }
            });
        }

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
    public static function basicSearch($term = null,$orderBy="created_at",$order='ASC',$filter = null)
    {
        if($orderBy == null)
        {
            $orderBy = "created_at";
        }

        if($order == null)
        {
            $order = "ASC";
        }

        $booked_trade_query = BookedTrade::where( function ($q) use ($term)
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
            // @TODO - Rework logic to check tradables for stock and market
            /*->whereHas('stock',function($q) use ($term){
                $q->where('code','like',"%$term%");
            })
            ->orWhereHas('market',function($q) use ($term){
                $q->where('title','like',"%$term%");
            })*/;
            if(strtolower($term) === 'put' || strtolower($term) === 'call'){
                $q->orWhereHas('tradeConfirmation',function($q) use ($term){
                    if(strtolower($term) === 'put'){
                        $q->where('is_put','1');
                    } else {
                        $q->where('is_put','0');
                    }
                });
            }
        });

        // Apply Filters
        if($filter !== null) {
            if(isset($filter["filter_status"])) {
                $booked_trade_query->where('is_confirmed', $filter["filter_status"]);
            }

            if(!empty($filter["filter_date"])) {
                $booked_trade_query->whereDate('created_at', Carbon::parse($filter["filter_date"])->format('Y-m-d'));
            }

            if(!empty($filter["filter_start_date"]) && !empty($filter["filter_end_date"])) {
                $start_date = Carbon::parse($filter["filter_start_date"])->format('Y-m-d');
                $end_date = Carbon::parse($filter["filter_end_date"])->format('Y-m-d');
                $booked_trade_query->whereBetween('created_at', [$start_date,$end_date]);
            }

            if(!empty($filter["filter_expiration"])) {
                $booked_trade_query->whereHas('userMarketRequest.userMarketRequestGroups.userMarketRequestItems', function ($query) use ($filter) {
                        $query->whereIn('title', ['Expiration Date',"Expiration Date 1","Expiration Date 2"])
                              ->whereDate('value', \Carbon\Carbon::parse($filter["filter_expiration"]));
                });
            }
        }

        $booked_trade_query->orderBy($orderBy,$order);

        return $booked_trade_query;
    }
}
