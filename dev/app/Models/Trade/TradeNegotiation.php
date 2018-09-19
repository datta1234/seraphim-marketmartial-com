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
	 * @property integer $initiate_user_id
	 * @property integer $recieving_user_id
	 * @property integer $trade_negotiation_status_id
	 * @property double $contracts
	 * @property double $nominals
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
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeNegotiationStatuses()
    {
        return $this->belongsTo('App\Models\Trade\TradeNegotiationStatus','trade_negotiation_status_id');
    }

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


    public function preFormatted()
    {
        $loggedInUserOrganisationId = $this->resolveOrganisationId();
        
        $data = [
            "quantity"      => $this->quantity,
            "traded"        => $this->traded,
            "is_offer"      => $this->is_offer,
            "is_distpute"   => $this->is_distpute,
            "sent_by_me"    => $this->initiate_user_id == $loggedInUserOrganisationId,
            'sent_to_me'    => $this->recieving_user_id == $loggedInUserOrganisationId,
        ];
        return $data;
    }
}
