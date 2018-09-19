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

    public function preFormatStats()
    {
        $data = [
            "id" => ,
            "updated_at" => ,
            "volatility" => ,
            "strike" => ,
            "nominal" => ,
            "strike_percentage" => ,
            "expiration" => ,
            "id" => ,
        ];
        dd($this);
    }
}