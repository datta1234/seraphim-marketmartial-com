<?php

namespace App\Models\Market;

use Illuminate\Database\Eloquent\Model;

class MarketCondition extends Model
{
	/**
	 * @property integer $id
	 * @property string $title
	 * @property integer $market_condition_category_id
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'market_conditions';

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
    public function marketConditionCategories()
    {
        return $this->belongsTo('App\Models\Market\MarketConditionCategory','market_condition_category_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function marketNegotiationConditions()
    {
        return $this->hasMany('App\Models\Market\MarketNegotiationCondition','market_condition_id');
    }
}
