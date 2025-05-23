<?php

namespace App\Models\Market;

use Illuminate\Database\Eloquent\Model;

class MarketNegotiationCondition extends Model
{
	/**
	 * @property integer $id
	 * @property boolean $is_private
	 * @property integer $market_negotiation_id
	 * @property integer $market_condition_id
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'market_negotiation_condition';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_private',
    ];

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function marketConditions()
    {
        return $this->belongsTo('App\Models\Market\MarketCondition','market_condition_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function marketNegotiations()
    {
        return $this->belongsTo('App\Models\Market\MarketNegotiation','market_negotiation_id');
    }
}
