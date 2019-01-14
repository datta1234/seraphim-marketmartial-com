<?php

namespace App\Models\Trade;

use Illuminate\Database\Eloquent\Model;
use App\Models\TradeConfirmations\TradeConfirmation;

class TradeNegotiation extends Model
{
    use \App\Traits\ResolvesUser;

    /**
	 * @property integer $id
	 * @property integer $user_market_id
	 * @property integer $trade_negotiation_id
     * @property integer $market_negotiation_id
	 * @property integer $initiate_user_id
	 * @property integer $recieving_user_id
	 * @property double $quantity
	 * @property double $traded
	 * @property boolean $is_offer
	 * @property boolean $is_distpute
     * @property boolean $no_cares
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trade_negotiations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quantity','is_offer', 'is_distpute', 'no_cares',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $hidden = [
        'initiate_user_id','recieving_user_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'traded' => 'boolean',
        'is_offer' => 'boolean',
        'no_cares' => 'boolean'
    ];

    public static function boot()
    {
        parent::boot();
        static::saved(function ($model) {
            $user_market_request = $model->userMarket->userMarketRequest;
            if($user_market_request->openToMarket()) {
                $user_market_request->notifySubscribedUsers();
            }
        });
    }

    /**
    *   Get source of negotiations
    *   
    */
    public function getRoot()
    {
        $table = $this->table;
        $parentKey = $this->tradeNegotiationParent()->getForeignKey();
        $id = (int)$this->id;
        $history = \DB::select("
            SELECT *
                FROM (
                    SELECT @id AS _id, (
                        SELECT @id := $parentKey FROM $table WHERE id = _id
                    ) as parent_id
                    FROM (
                        SELECT @id := $id
                    ) tmp1
                    JOIN $table ON @id IS NOT NULL
                ) parent_struct
                JOIN $table outcome ON parent_struct._id = outcome.id
        ");
        return self::hydrate($history)->sortBy('id')->first();
    }
    
    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeNegotiationParent()
    {
        return $this->belongsTo('App\Models\Trade\TradeNegotiation','trade_negotiation_id');

    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeNegotiationChildren()
    {
        return $this->hasMany('App\Models\Trade\TradeNegotiation','trade_negotiation_id');
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
    public function initiateUser()
    {
        return $this->belongsTo('App\Models\UserManagement\User','initiate_user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function recievingUser()
    {
        return $this->belongsTo('App\Models\UserManagement\User','recieving_user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmations()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmation','trade_negotiation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function marketNegotiation()
    {
        return $this->belongsTo('App\Models\Market\MarketNegotiation','market_negotiation_id');
    }

    //weather its a sale or offer determine on the first negotiation
    public function isOffer()
    {
        return $this->marketNegotiation->TradeNegotiations()->first();
    }

    public function isOrganisationInvolved($org_id)
    {
        return $this->initiateUser->organisation_id == $org_id || $this->recievingUser->organisation_id == $org_id;
    }

    public function setUpConfirmation()
    {
       $tradeConfirmation = new TradeConfirmation();
       return $tradeConfirmation->setUp($this);
    }

    public function getIsOfferForOrg($org_id)
    {
        if($this->initiateUser->organisation_id == $org_id)
        {
            return $this->is_offer;
        }elseif ($this->recievingUser->organisation_id == $org_id) {
            return !$this->is_offer;
        }
        return null;
    }
  

    public function preFormatted()
    {
        $loggedInUserOrganisationId = $this->resolveOrganisationId();
        $sentByMe =  $this->initiateUser->organisation_id == $loggedInUserOrganisationId;
        $sentToMe = $this->recievingUser->organisation_id == $loggedInUserOrganisationId;

        $data = [
            "user_market_id"        => $this->user_market_id,
            "id"                    => $this->id,
            "traded"                => $this->traded,
            "trade_negotiation_id"  => $this->trade_negotiation_id,
            "is_offer"              => $this->is_offer,
            "is_distpute"           => $this->is_distpute,
            "sent_by_me"            => $sentByMe,
            'sent_to_me'            => $sentToMe
        ];

        if($sentByMe || $sentToMe || $this->traded)
        {
            $data['quantity'] = $this->quantity;
        }
        return $data;
    }
}
