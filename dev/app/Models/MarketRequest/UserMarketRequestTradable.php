<?php

namespace App\Models\MarketRequest;

use Illuminate\Database\Eloquent\Model;

class UserMarketRequestTradable extends Model
{
    /**
     * @property integer $id
     * @property integer $user_market_request_id
     * @property integer $market_id
     * @property integer $stock_id
     * @property \Carbon\Carbon $created_at
     * @property \Carbon\Carbon $updated_at
     */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_market_request_tradables';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarketRequest()
    {
        return $this->belongsTo(
            'App\Models\MarketRequest\UserMarketRequest',
            'user_market_request_id'
        );
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
    public function stock()
    {
        return $this->belongsTo('App\Models\StructureItems\Stock','stock_id');
    }
}
