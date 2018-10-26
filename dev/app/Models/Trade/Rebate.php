<?php

namespace App\Models\Trade;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
    public function organisation()
    {
        return $this->belongsTo('App\Models\UserManagement\Organisation', 'organisation_id');
    }

    public function preFormat()
    {
        $trade_confirmation = $this->bookedTrade->tradeConfirmation;
        $user_market_request_items = $trade_confirmation->resolveUserMarketRequestItems();

        $data = [
            "date"          => $this->trade_date,
            "market"        => $this->bookedTrade->resolveMarketStock(),
            "is_put"        => $trade_confirmation->is_put,
            "strike"        => $user_market_request_items["strike"],
            "expiration"    => $user_market_request_items["expiration"],
            "nominal"       => $user_market_request_items["nominal"],
            "role"          => null,
            "rebate"        => $this->bookedTrade->amount,
        ];

        if(\Auth::user()->role_id == 1){
            $data["bank"] = $this->user->organisation->title;
        }

        // Resolve role
        switch (true) {
            case ($trade_confirmation->sendUser->organisation->id == $this->organisation->id):
            case ($trade_confirmation->recievingUser->organisation->id == $this->organisation->id):
                $data["role"] = 'Traded';
                break;
            case ($trade_confirmation->tradeNegotiation->userMarket->user->organisation->id == $this->organisation->id):
                $data["role"] = 'Market Maker (traded away)';
                break;
            default:
                $data["role"] = null;
                break;
        }

        return $data;
    }

    public function preFormatAdmin($is_csv = false)
    {
        $trade_confirmation = $this->bookedTrade->tradeConfirmation;
        $user_market_request_items = $trade_confirmation->resolveUserMarketRequestItems();

        $data = [
            "id"            => $this->id,
            "date"          => $this->trade_date,
            "user"          => $this->user->full_name,
            "organisation"  => $this->organisation->title,
            "market"        => $this->bookedTrade->resolveMarketStock(),
            "is_put"        => $trade_confirmation->is_put,
            "strike"        => $user_market_request_items["strike"],
            "expiration"    => $user_market_request_items["expiration"],
            "nominal"       => $user_market_request_items["nominal"],
            "rebate"        => $this->bookedTrade->amount,
            "is_paid"       => $this->is_paid,
        ];

        if($is_csv) {
            array_walk($data, function(&$field,$key) {
                if($key == "is_paid") {
                    $field = $field ? "Yes" : "No";
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
        });

        // Apply Filters
        if($filter !== null) {
            if(isset($filter["filter_paid"])) {
                $rebateQuery->where('is_paid', $filter["filter_paid"]);
            }

            if(!empty($filter["filter_date"])) {
                $rebateQuery->whereDate('trade_date', Carbon::parse($filter["filter_date"])->format('Y-m-d'));
            }

            if(!empty($filter["filter_expiration"])) {
                $rebateQuery->whereHas('bookedTrade', function ($query) use ($filter) {
                    $query->whereHas('tradeConfirmation', function ($query) use ($filter) {
                        $query->whereHas('tradeNegotiation', function ($query) use ($filter) {
                            $query->whereHas('userMarket', function ($query) use ($filter) {
                                $query->whereHas('userMarketRequest', function ($query) use ($filter) {
                                    $query->whereHas('userMarketRequestGroups', function ($query) use ($filter) {
                                        $query->whereHas('userMarketRequestItems', function ($query) use ($filter) {
                                            $query->whereIn('title', ['Expiration Date',"Expiration Date 1","Expiration Date 2"])
                                                  ->whereDate('value', \Carbon\Carbon::parse($filter["filter_expiration"]));
                                        });
                                    });
                                });
                            });
                        });
                    });
                });
            }
        }

        $rebateQuery->orderBy($orderBy,$order);

        return $rebateQuery;
    }
}
