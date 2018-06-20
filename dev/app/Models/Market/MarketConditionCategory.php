<?php

namespace App\Models\Market;

use Illuminate\Database\Eloquent\Model;

class MarketConditionCategory extends Model
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
    protected $table = 'market_condition_categories';

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
    public function marketConditions()
    {
        return $this->hasMany('App\Models\Market\MarketCondition','market_condition_category_id');
    }
}
