<?php

namespace App\Models\StructureItems;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
	/**
	 * @property integer $id
	 * @property integer $item_type_id
	 * @property integer $trade_structure_group_id
	 * @property string $title
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'trade_structure_group_id'
    ];

    /**
    * Return relation based of market_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function ItemTypes()
    {
        return $this->belongsTo('App\Models\StructureItems\ItemType', 'item_type_id');
    }

    /**
    * Return relation based of market_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeStructureGroups()
    {
        return $this->belongsTo(
            'App\Models\StructureItems\TradeStructureGroup', 
            'trade_structure_group_id'
        );
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarketRequestItems()
    {
        return $this->hasMany('App\Models\MarketRequest\UserMarketRequestItem','item_id');
    }
}
