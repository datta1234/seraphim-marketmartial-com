<?php

namespace App\Models\TradeConfirmations;

use Illuminate\Database\Eloquent\Model;

class BookedTrade extends Model
{
	/**
	 * @property integer $id
	 * @property integer $user_id
	 * @property integer $trade_confirmation_id
	 * @property integer $trading_account_id
	 * @property integer $market_id
	 * @property integer $stock_id
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
        'is_sale',
        'is_confirmed',
        'amount',
        'user_id',
        'trade_confirmation_id',
        'trading_account_id',
        'market_id',
        'stock_id',
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
    public function stock()
    {
        return $this->belongsTo('App\Models\StructureItems\Stock','stock_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function market()
    {
        return $this->belongsTo('App\Models\StructureItems\Market','market_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function rebate()
    {
        return $this->hasOne('App\Models\Trade\Rebate','booked_trade_id');
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

    public function resolveMarketStock() {
        // Resolve stock / market
        if($this->stock) {
            return $this->stock->code;
        } else {
            return $this->market->title;
        }
    }

    public function preFormatAdmin()
    {
        $trade_confirmation = $this->tradeConfirmation;
        $user_market_request_items = $trade_confirmation->resolveUserMarketRequestItems();

        $data = [
            "id"            => $this->id,
            "date"          => $this->created_at->format('Y-m-d H:i:s'),
            "user"          => $this->user->full_name,
            "organisation"  => $this->user->organisation->title,
            "market"        => $this->resolveMarketStock(),
            "is_put"        => $trade_confirmation->is_put,
            "strike"        => $user_market_request_items["strike"],
            "expiration"    => $user_market_request_items["expiration"],
            "nominal"       => $user_market_request_items["nominal"],
            "amount"        => $this->amount,
            "is_confirmed"  => $this->is_confirmed,
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

        $rebateQuery = BookedTrade::where( function ($q) use ($term)
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
            ->whereHas('stock',function($q) use ($term){
                $q->where('code','like',"%$term%");
            })
            ->orWhereHas('market',function($q) use ($term){
                $q->where('title','like',"%$term%");
            });
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
                $rebateQuery->where('is_confirmed', $filter["filter_status"]);
            }

            if(!empty($filter["filter_date"])) {
                $rebateQuery->whereDate('created_at', $filter["filter_date"]);
            }
        }

        $rebateQuery->orderBy($orderBy,$order);

        return $rebateQuery;
    }
}
