<?php

namespace App\Models\TradeConfirmations;

use Illuminate\Database\Eloquent\Model;

class TradeConfirmationGroupType extends Model
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
    public function tradeConfirmationGroups()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmationGroup',
        	'trade_confirmation_group_type_id');
    }
}
