<?php

namespace App\Models\UserManagement;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Lab404\Impersonate\Models\Impersonate;

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
     * @property boolean $verified
     * @property boolean $is_invited
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
    use Impersonate;

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
        'verified',
        'is_invited',
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
    * The attributes that should be cast to native types.
    *
    * @var array
    */
    protected $casts = [
        'tc_accepted' => 'boolean',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'birthdate'
    ];

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function role()
    {
        return $this->belongsTo('App\Models\UserManagement\Role', 'role_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function organisation()
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
    public function interests()
    {
        return $this->belongsToMany('App\Models\UserManagement\Interest','interest_user', 'user_id', 'interest_id')->withPivot('value');
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
        return $this->belongsToMany('App\Models\StructureItems\MarketType', 'user_market_interests', 'user_id', 'market_type_id');
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

    /**
    * Return total rebate amount of user on monthly basis
    * @return int sum
    */
    public function userTotalRebate() 
    {
        return 65000;
    }

    /*
    *
    *return wether a users profile has been completed
    */
    public function completeProfile()
    {
        return (bool)$this->tc_accepted;
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
    public static function basicSearch($term = null,$orderBy="full_name",$order='ASC', $filter = null)
    {
        if($orderBy == null)
        {
            $orderBy = "full_name";
        }

        if($order == null)
        {
            $order = "ASC";
        }

        $UserQuery = User::where( function ($q) use ($term)
        {
            $q->where('users.full_name', 'like',"%$term%")
            ->orWhere('users.email', 'like',"%$term%")
            ->orWhere('users.cell_phone', 'like',"%$term%")
            ->orWhere('users.work_phone', 'like',"%$term%")
            ->orWhereHas('organisation',function($q) use ($term){
                $q->where('title','like',"%$term%");
            });
        });

        if($filter !== null) {
            switch ($filter) {
                case 'active':
                    $UserQuery->where('users.verified', true)
                    ->where('active', true);
                    break;
                case 'inactive':
                    $UserQuery->where('users.verified', true)
                    ->where('active', false);
                    break;
                case 'request':
                    $UserQuery->where('users.verified', false)
                    ->where('active', false);
                    break;
            }
        }

        $UserQuery->orderBy($orderBy,$order);

        return $UserQuery;
    }

    /**
     * Determines if a user has been verified and is active
     *
     * @return bool|null User's active state or null if not verified
     */
    public function verifiedActiveUser()
    {
        return (bool)$this->verified ? (bool)$this->active : null;
    }

    /**
     * Only admins can impersonate
     * 
     * @return bool
     */
    public function canImpersonate()
    {
        // For example
        return $this->role->title == 'Admin';
    }

    /**
     * Only traders can be impersonated
     * 
     * @return bool
     */
    public function canBeImpersonated()
    {
        // For example
        return $this->role->title == 'Trader';
    }    


}
