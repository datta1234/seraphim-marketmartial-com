<?php

namespace App\Models\StructureItems;

use Illuminate\Database\Eloquent\Model;

class DerivativeType extends Model
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
    protected $table = 'derivative_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
    ];

    /**
    * Return relation based of derivative_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function derivatives()
    {
        return $this->hasMany('App\Models\StructureItems\Derivative', 'derivative_type_id');
    }
}
