<?php

namespace App\Models\StructureItems;

use Illuminate\Database\Eloquent\Model;

class Derivative extends Model
{
	/**
	 * @property integer $id
	 * @property string $title
	 * @property integer $derivative_type_id
	 * @property text $description
	 * @property boolean $is_seldom
	 * @property boolean $has_deadline
	 * @property boolean $has_negotiation
	 * @property boolean $has_rebate
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */
	
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'derivatives';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
		'description',
		'is_seldom',
		'has_deadline',
		'has_negotiation',
		'has_rebate',
    ];
}
