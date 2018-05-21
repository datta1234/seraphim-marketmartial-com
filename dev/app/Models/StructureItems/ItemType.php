<?php

namespace App\Models\StructureItems;

use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{
	/**
	 * @property integer $id
	 * @property string $title
	 * @property string $validation_rule
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'item_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'validation_rule',
    ];

    /**
    * Return relation based of derivative_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function items()
    {
        return $this->hasMany('App\Models\StructureItems\Item', 'item_type_id');
    }
}
}
