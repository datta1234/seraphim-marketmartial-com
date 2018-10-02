<?php

namespace App\Models\Trade;

use Illuminate\Database\Eloquent\Model;

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
	 * @property integer $trade_negotiation_status_id
	 * @property double $quantity
	 * @property double $traded
	 * @property boolean $is_offer
	 * @property boolean $is_distpute
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
        'quantity', 'traded', 'is_offer', 'is_distpute',
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
        'is_offer' => 'boolean'
    ];
    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeNegotiationParents()
    {
        return $this->hasMany('App\Models\Trade\TradeNegotiation','trade_negotiation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeNegotiationChildren()
    {
        return $this->belongsTo('App\Models\Trade\TradeNegotiation','trade_negotiation_id');
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

    public function preFormatted()
    {
        $loggedInUserOrganisationId = $this->resolveOrganisationId();
        $sentByMe =  $this->initiateUser->organisation_id == $loggedInUserOrganisationId;
        $sentToMe = $this->recievingUser->organisation_id == $loggedInUserOrganisationId;

        $data = [
            "id"            => $this->id,
            "traded"        => $this->traded,
            "is_offer"      => $this->is_offer,
            "is_distpute"   => $this->is_distpute,
            "sent_by_me"    => $sentByMe,
            'sent_to_me'    => $sentToMe,
        ];

        if($sentByMe || $sentToMe)
        {
            $data['quantity'] = $this->quantity;
        }
        return $data;
    }
}
