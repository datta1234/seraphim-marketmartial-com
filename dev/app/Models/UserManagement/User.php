<?php

namespace App\Models\UserManagement;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * @property integer $id
     * @property integer $role_id
     * @property integer $organisation_id
     * @property string $email
     * @property string $full_name
     * @property string $cell_phone
     * @property string $work_phone
     * @property string $password
     * @property string $remember_token
     * @property boolean $active
     * @property boolean $tc_accepted
     * @property boolean $is_married
     * @property boolean $has_children
     * @property text $hobbies
     * @property \Carbon\Carbon $last_login
     * @property \Carbon\Carbon $birthdate
     * @property \Carbon\Carbon $created_at
     * @property \Carbon\Carbon $updated_at
     * @property \Carbon\Carbon $deleted_at
     */

    use Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name', 
        'email', 
        'password', 
        'cell_phone',
        'work_phone',
        'active',
        'tc_accepted',
        'is_married',
        'has_children',
        'hobbies',
        'birthdate',
        'organisation_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function roles()
    {
        return $this->belongsTo('App\Models\UserManagement\Role', 'role_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function organisations()
    {
        return $this->belongsTo('App\Models\UserManagement\Organisation', 'organisation_id');
    }


    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userOtps()
    {
        return $this->hasMany('App\Models\UserManagement\UserOtp', 'user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function interestUsers()
    {
        return $this->hasMany('App\Models\UserManagement\InterestUser', 'user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function emails()
    {
        return $this->hasMany('App\Models\UserManagement\Email', 'user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradingAccounts()
    {
        return $this->hasMany('App\Models\UserManagement\TradingAccount', 'user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function marketInterests()
    {
        return $this->belongsToMany('App\Models\StructureItems\Market', 'user_market_interests', 'user_id', 'market_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function marketWatched()
    {
        return $this->belongsToMany('App\Models\StructureItems\Market', 'user_market_watched', 'user_id', 'market_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarketRequests()
    {
        return $this->hasMany('App\Models\MarketRequest\UserMarketRequest','user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function sendDistputes()
    {
        return $this->hasMany('App\Models\TradeConfirmations\Distpute','send_user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function receivingDistputes()
    {
        return $this->hasMany('App\Models\TradeConfirmations\Distpute','receiving_user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarkets()
    {
        return $this->hasMany('App\Models\Market\UserMarket','user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarketSubscriptions()
    {
        return $this->hasMany('App\Models\Market\UserMarketSubscription','user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function marketNegotiations()
    {
        return $this->hasMany('App\Models\Market\MarketNegotiation','user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function bookedTrades()
    {
        return $this->hasMany('App\Models\TradeConfirmations\BookedTrade','user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function initiateTradeNegotiations()
    {
        return $this->hasMany('App\Models\Trade\TradeNegotiation','initiate_user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function recievingTradeNegotiations()
    {
        return $this->hasMany('App\Models\Trade\TradeNegotiation','recieving_user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function initiateTrades()
    {
        return $this->hasMany('App\Models\Trade\Trade','initiate_user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function recievingTrades()
    {
        return $this->hasMany('App\Models\Trade\Trade','recieving_user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function rebates()
    {
        return $this->hasMany('App\Models\Trade\Rebate','user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function sendTradeConfirmations()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmation','send_user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function recievingTradeConfirmations()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmation','receiving_user_id');
    }
}
