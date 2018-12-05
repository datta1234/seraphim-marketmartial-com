<?php

namespace App\Models\MarketRequest;

use Illuminate\Database\Eloquent\Model;

class UserMarketRequestGroup extends Model
{
    /**
     * @property integer $id
     * @property integer $user_market_request_id
     * @property integer $trade_structure_id
     * @property boolean $is_selected
     * @property \Carbon\Carbon $created_at
     * @property \Carbon\Carbon $updated_at
     */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_market_request_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_selected',
        'trade_structure_group_id',
        'user_market_request_id'
    ];

    protected $casts = [
        'is_selected' => 'boolean'
    ];

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
    public function userMarketRequestItems()
    {
        return $this->hasMany(
            'App\Models\MarketRequest\UserMarketRequestItem',
            'user_market_request_group_id'
        );
    }

    /**
    * Return relation based of market_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeStructureGroup()
    {
        return $this->belongsTo('App\Models\StructureItems\TradeStructureGroup', 'trade_structure_group_id');
    }

    /**
    * Return relation based of market_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmationGroups()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmationGroup',
            'user_market_request_group_id');
    }

    /**
    * Scope of choice's
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function scopeChosen($query) {
        return $query->where('is_selected', true);
    }


    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradable()
    {
        return $this->hasOne('App\Models\MarketRequest\UserMarketRequestTradable','user_market_request_group_id');
    }

    /**
    * Return related volatility
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function volatility()
    {
        $um_id = $this->userMarketRequest->chosen_user_market_id;
        return $this->hasOne('App\Models\Market\UserMarketVolatility','user_market_request_group_id')
                    ->where(function($q) use($um_id) {
                        return $q->where('user_market_id', $um_id);
                    });
    }

    public function preFormatted()
    {
        return [
            "tradable" => $this->tradable ? $this->tradable->preFormatted() : null,
            'items' => $this->userMarketRequestItems->map(function($item){
                return $item->preFormatted();
            })
        ];
    }

    /**
     * return a trade item singleton by key
     *
     * @return Mixed
     */
    public function getDynamicItem($attr, $return_object = false)
    {
        $item = $this->userMarketRequestItems()
        ->where('title',$attr)
        ->first();
        if($item)
        {
            if($return_object == true) {
                return $item;
            }
            switch ($item->type) {
                case 'double':
                    return floatval($item->value);
                    break;
                default:
                    return $item->value;
                    break;
            }
        }else
        {
            return null;
        }
    }
}
