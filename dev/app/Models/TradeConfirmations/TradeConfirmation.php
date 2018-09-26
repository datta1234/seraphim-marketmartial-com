<?php

namespace App\Models\TradeConfirmations;

use Illuminate\Database\Eloquent\Model;

class TradeConfirmation extends Model
{
	/**
	 * @property integer $id
	 * @property integer $send_user_id
	 * @property integer $receiving_user_id
	 * @property integer $trade_negotiation_id
	 * @property integer $trade_confirmation_status_id
	 * @property integer $trade_confirmation_id
	 * @property integer $stock_id
	 * @property integer $market_id
	 * @property integer $traiding_account_id
	 * @property double $spot_price
	 * @property double $future_reference
	 * @property double $near_expiery_reference
	 * @property double $contracts
	 * @property double $puts
	 * @property double $calls
	 * @property double $delta
	 * @property double $gross_premiums
	 * @property double $net_premiums
	 * @property boolean $is_confirmed
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trade_confirmations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'spot_price',
		'future_reference',
		'near_expiery_reference',
		'contracts',
		'puts',
		'calls',
		'delta',
		'gross_premiums',
		'net_premiums',
		'is_confirmed',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmationStatus()
    {
        return $this->belongsTo(
        	'App\Models\TradeConfirmations\TradeConfirmationStatus',
        	'trade_confirmation_status_id'
        );
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function distputes()
    {
        return $this->hasMany('App\Models\TradeConfirmations\Distpute','trade_confirmation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmationParents()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmation','trade_confirmation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmationChildren()
    {
        return $this->belongsTo('App\Models\TradeConfirmations\TradeConfirmation','trade_confirmation_id');
    }


    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function bookedTrades()
    {
        return $this->hasMany('App\Models\TradeConfirmations\BookedTrade','trade_confirmation_id');
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
    public function tradeNegotiation()
    {
        return $this->belongsTo('App\Models\Trade\TradeNegotiation','trade_negotiation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function sendUser()
    {
        return $this->belongsTo('App\Models\UserManagement\User','send_user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function recievingUser()
    {
        return $this->belongsTo('App\Models\UserManagement\User','receiving_user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradingAccount()
    {
        return $this->belongsTo('App\Models\UserManagement\TradingAccount','traiding_account_id');
    }

    public function scopeOrganisationInvolved($query, $organistation_id, $operator, $or = false)
    {
        return $query->{($or ? 'orWhere' : 'where')}(function ($tlq)  use ($organistation_id,$operator) {
            $tlq->whereHas('sendUser', function ($query) use ($organistation_id,$operator) {
                $query->where('organisation_id', $operator,$organistation_id);
            })
            ->orWhereHas('recievingUser', function ($query) use ($organistation_id,$operator) {
                $query->where('organisation_id', $operator,$organistation_id);
            });
        });

    }

    public function scopeUserInvolved($query, $user_id, $operator, $or = false)
    {
        return $query->{($or ? 'orWhere' : 'where')}(function ($tlq) use ($user_id,$operator) {
            $tlq->where('send_user_id', $operator,$user_id)
                ->orWhere('receiving_user_id', $operator,$user_id);
        });
    }    

    public function scopeOrgnisationMarketMaker($query, $organistation_id, $or = false)
    {
        return $query->{($or ? 'orWhere' : 'where')}(function ($tlq) use ($organistation_id) {
            $tlq->whereHas('tradeNegotiation', function ($query) use ($organistation_id) {
                $query->whereHas('userMarket', function ($query) use ($organistation_id) {
                    $query->whereHas('user', function ($query) use ($organistation_id) {
                        $query->where('organisation_id', $organistation_id);
                    });
                });
            });
        });
    }

    public function preFormatStats($user = null)
    {
        $data = [
            "id" => $this->id,
            "updated_at" => $this->updated_at->format('Y-m-d H:i:s'),
            "market" => $this->market->title,
            "structure" => $this->tradeNegotiation->userMarket->userMarketRequest->tradeStructure->title,
            "nominal" => array(),
            "strike" => array(),
            "expiration" => array(),
            "strike_percentage" => array(),
            "volatility" => array(),
        ];
        
        // Get the user market request items
        $user_market_request_items = array();
        $user_market_request_groups = $this->tradeNegotiation->userMarket->userMarketRequest->userMarketRequestGroups;
        foreach ($user_market_request_groups as $key => $user_market_request_group) {
            $user_market_request_items[] = $user_market_request_group->userMarketRequestItems->groupBy(function ($item, $key) {
                return $item->user_market_request_group_id;
            });
        }

        // Set Strike, Strike percentage, expiration dates and nominals
        foreach ($user_market_request_items as $item_groups) {
            foreach ($item_groups as $item_group) {
                foreach ($item_group as $item) {
                    switch ($item->title) {
                        case 'Expiration Date':
                        case 'Expiration Date 1':
                        case 'Expiration Date 2':
                                $data["expiration"][] = $item->value;
                            break;
                        case 'Strike':
                            $data["strike"][] = $item->value;
                            if($this->spot_price !== null) {
                                $data["strike_percentage"][] = round($item->value/$this->spot_price, 2);
                            } else {
                                $data["strike_percentage"] = null;
                            }
                            break;
                        case 'Quantity':
                                $data["nominal"][] = $item->value;
                            break;
                    }
                }    
            }   
        }
        $market_negotiation = $this->tradeNegotiation->marketNegotiation;
        // volatility
        if($market_negotiation->bid_qty) {
            $data["volatility"][] = $market_negotiation->bid_qty;
        }

        if($market_negotiation->offer_qty) {
            $data["volatility"][] = $market_negotiation->offer_qty;
        }        

        if($user === null) {
            $data["status"] = $this->tradeNegotiation->traded ? 'Traded' : 'Not Traded';
            return $data; 
        }

        // Determine direction, state and trader. Priority - My Trade > My Organisation Trade > Market Maker Traded Away
        switch (true) {
            case ($this->send_user_id == $user->id):
                $data["status"] = 'My Trade';
                $data["trader"] = $user->full_name;
                $data["direction"] = 'Sell';
                break;
            case ($this->receiving_user_id == $user->id):
                $data["status"] = 'My Trade';
                $data["trader"] = $user->full_name;
                $data["direction"] = 'Buy';
                break;
            case ($this->sendUser->organisation->id == $user->organisation->id):
                $data["status"] = 'Trade';
                $data["trader"] = $this->sendUser->full_name;
                $data["direction"] = 'Sell';
                break;
            case ($this->recievingUser->organisation->id == $user->organisation->id):
                $data["status"] = 'Trade';
                $data["trader"] = $this->recievingUser->full_name;
                $data["direction"] = 'Buy';
                break;
            case ($this->tradeNegotiation->userMarket->user->organisation->id == $user->organisation->id):
                $data["status"] = 'Market Maker Traded Away';
                $data["trader"] = null;
                $data["direction"] = null;
                break;
            default:
                $data["status"] = null;
                $data["trader"] = null;
                $data["direction"] = null;
                break;
        }

        return $data;
    }

    /**
     * Return a simple or query object based on the search term
     *
     * @param string $term
     * @param string $orderBy
     * @param string $order
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function basicSearch($term = null,$orderBy="updated_at",$order='ASC', $filter = null)
    {

        // Search markets
        // @TODO - Change to account for single stocks and match with stock name (instrument)
        $trade_confirmations_query = TradeConfirmation::where( function ($q) use ($term)
        {
            $q->whereHas('market',function($q) use ($term){
                $q->where('title','like',"%$term%");
            });
        });

        // Apply Filters
        if($filter !== null) {
            if($filter["filter_date"] !== null) {
                $trade_confirmations_query->whereDate('updated_at', $filter["filter_date"]);
            }

            if($filter["filter_market"] !== null) {
                $trade_confirmations_query->where('market_id', $filter["filter_market"]);
            }

            if($filter["filter_expiration"] !== null) {
                $trade_confirmations_query->whereHas('tradeNegotiation', function ($query) use ($filter) {
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
            }
        }

        // Apply Ordering
        // @TODO - move this to a separate function
        /*switch ($orderBy) {
            case 'updated_at':
            //dd("hit");
                $trade_confirmations_query->orderBy($orderBy,$order);
                break;
            case 'market':
                $trade_confirmations_query->whereHas('market',function($q) use ($order){
                    $q->orderBy('title',$order);
                });
                //$trade_confirmations_query->orderBy("market_id",$order)
                break;
            case 'structure':
            
                break;
            case 'direction':
            
                break;
            case 'status':
            
                break;
            case 'trader':
            
                break;
            default:
                $trade_confirmations_query->orderBy("updated_at", "ASC");
                break;
        }*/

      return $trade_confirmations_query;
  }
}