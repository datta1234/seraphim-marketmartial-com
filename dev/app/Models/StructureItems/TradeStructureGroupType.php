<?php

namespace App\Models\StructureItems;

use Illuminate\Database\Eloquent\Model;

class TradeStructureGroupType extends Model
{
	/**
	 * @property integer $id
	 * @property string $title
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

	/**
    * Return relation based of market_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeStructureGroup()
    {
        return $this->belongsTo('App\Models\StructureItems\TradeStructureGroup',
        	'trade_structure_group_type_id');
    }
}
