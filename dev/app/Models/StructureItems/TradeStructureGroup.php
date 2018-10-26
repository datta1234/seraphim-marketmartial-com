<?php

namespace App\Models\StructureItems;

use Illuminate\Database\Eloquent\Model;

class TradeStructureGroup extends Model
{
	/**
	 * @property integer $id
	 * @property integer $trade_structure_id
     * @property integer $trade_structure_group_type_id
	 * @property string $title
     * @property boolean $force_select
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

   /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trade_structure_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'trade_structure_id',
        'force_select',
        'trade_structure_group_type_id'
    ];

    /**
    * Return relation based of market_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function items()
    {
        return $this->hasMany('App\Models\StructureItems\Item', 'trade_structure_group_id');
    }

    /**
    * Return relation based of market_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeStructureGroupTypes()
    {
        return $this->hasMany('App\Models\StructureItems\TradeStructureGroupType',
            'trade_structure_group_type_id');
    }

    /**
    * Return relation based of market_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeStructure()
    {
        return $this->belongsTo('App\Models\StructureItems\TradeStructure', 'trade_structure_id');
    }

    /**
    * Return relation based of market_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarketRequestGroups()
    {
        return $this->hasMany('App\Models\MarketRequest\UserMarketRequestGroup', 'trade_structure_id');
    }

    /**
    * Return relation based of market_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmationGroups()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmationGroup',
            'trade_structure_group_id');
    }

        /**
    * Return relation based of market_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmationItems()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmationItem',
            'trade_confirmation_group_id');
    }
}
