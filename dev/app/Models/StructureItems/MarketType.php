<?php

namespace App\Models\StructureItems;

use Illuminate\Database\Eloquent\Model;

class MarketType extends Model
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
    protected $table = 'market_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
    ];

    /**
    * Return relation based of market_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function markets()
    {
        return $this->hasMany('App\Models\StructureItems\Market', 'market_type_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeStructures()
    {
        return $this->belongsToMany('App\Models\StructureItems\TradeStructure', 'market_types_trade_structures', 'market_type_id', 'trade_structure_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userInterests()
    {
        return $this->belongsToMany('App\Models\UserManagement\User','user_market_interests','user_id','market_type_id');
    }
}
