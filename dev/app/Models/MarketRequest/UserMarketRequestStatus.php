<?php

namespace App\Models\MarketRequest;

use Illuminate\Database\Eloquent\Model;

class UserMarketRequestStatus extends Model
{
    /**
     * @property integer $id
     * @property string $title
     * @property \Carbon\Carbon $created_at
     * @property \Carbon\Carbon $updated_at
     */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_market_request_statuses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',    
    ];

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarketRequests()
    {
        return $this->hasMany('App\Models\MarketRequest\UserMarketRequest', 'user_market_request_statuses_id');
    }
}
